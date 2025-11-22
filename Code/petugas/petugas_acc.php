<?php
require_once __DIR__ . '/_head.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['petugas','admin'])) { header('Location: index.php?url=login'); exit; }

$id = intval($_GET['id'] ?? 0);
$pid = intval($_GET['pid'] ?? 0);
if ($id<=0 || $pid<=0) { die('ID tidak valid'); }

// fetch peminjaman
$stmt = mysqli_prepare($koneksi, "SELECT id, tgl_jatuh_tempo FROM tb_peminjaman WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $pid);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$pm = mysqli_fetch_assoc($res);
if (!$pm) { die('Peminjaman tidak ditemukan'); }

// check overdue
$due = new DateTime($pm['tgl_jatuh_tempo']);
$today = new DateTime();
if ($today > $due) { die('Tidak dapat perpanjang: sudah lewat jatuh tempo'); }

// new due +7
$due->modify('+7 days');
$newDue = $due->format('Y-m-d');

// transaction
mysqli_begin_transaction($koneksi);
try {
    $u1 = mysqli_prepare($koneksi, "UPDATE tb_peminjaman SET tgl_jatuh_tempo = ? WHERE id = ?");
    mysqli_stmt_bind_param($u1, "si", $newDue, $pid);
    mysqli_stmt_execute($u1);

    $u2 = mysqli_prepare($koneksi, "UPDATE tb_perpanjangan SET status='approved', processed_date = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($u2, "i", $id);
    mysqli_stmt_execute($u2);

    mysqli_commit($koneksi);

    addLog($koneksi, $_SESSION['user']['id'], 'ACC perpanjangan ID: '.$id);

    // notify user email if exists
    $q = mysqli_prepare($koneksi, "SELECT u.email, u.nama FROM tb_perpanjangan p JOIN tb_user u ON u.id = p.user_id WHERE p.id = ? LIMIT 1");
    mysqli_stmt_bind_param($q, "i", $id);
    mysqli_stmt_execute($q);
    $r = mysqli_stmt_get_result($q);
    $row = mysqli_fetch_assoc($r);
    if (!empty($row['email'])) {
        $sb = 'Perpanjangan Disetujui';
        $bd = '<p>Halo '.e($row['nama']).', permintaan perpanjangan Anda disetujui. Jatuh tempo baru: <strong>'.$newDue.'</strong></p>';
        send_mail_simple($row['email'], $sb, $bd);
    }

    view_head('Sukses');
    echo "<div class='card'><div class='card-body'>
    <div class='alert alert-success'>Perpanjangan disetujui. Jatuh tempo baru: <strong>".e($newDue)."</strong>
    </div><a class='btn btn-primary' href='index.php?url=petugas_list'>Kembali</a></div></div>";
    view_footer();
    exit;
} catch (Exception $e) {
    mysqli_rollback($koneksi);
    die('Terjadi kesalahan: '. $e->getMessage());
}
?>