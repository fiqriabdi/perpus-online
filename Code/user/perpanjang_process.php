<?php
require_once __DIR__ . '/_head.php';
if (!isset($_SESSION['user'])) { header('Location: index.php?url=login'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
$user = $_SESSION['user'];
$id_peminjaman = intval($_POST['id_peminjaman'] ?? 0);

$stmt = mysqli_prepare($koneksi, "SELECT id, tgl_jatuh_tempo, status FROM tb_peminjaman WHERE id = ? AND user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ii", $id_peminjaman, $user['id']);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$pm = mysqli_fetch_assoc($res);
if (!$pm) { die('Peminjaman tidak ditemukan'); }
if ($pm['status'] !== 'dipinjam') { die('Tidak dapat diperpanjang: status bukan dipinjam'); }

// check overdue
$now = new DateTime();
$due = new DateTime($pm['tgl_jatuh_tempo']);
if ($now > $due) { die('Tidak dapat diperpanjang: sudah melewati jatuh tempo'); }

// check pending
$q = mysqli_prepare($koneksi, "SELECT id FROM tb_perpanjangan WHERE peminjaman_id = ? AND status = 'pending' LIMIT 1");
mysqli_stmt_bind_param($q, "i", $id_peminjaman);
mysqli_stmt_execute($q);
$r = mysqli_stmt_get_result($q);
if (mysqli_fetch_assoc($r)) { die('Sudah ada permintaan perpanjangan pending'); }

// insert request
$ins = mysqli_prepare($koneksi, "INSERT INTO tb_perpanjangan (peminjaman_id, user_id, request_date, status) VALUES (?, ?, NOW(), 'pending')");
mysqli_stmt_bind_param($ins, "ii", $id_peminjaman, $user['id']);
mysqli_stmt_execute($ins);

addLog($koneksi, $user['id'], 'Ajukan perpanjangan untuk peminjaman ID: ' . $id_peminjaman);

// notify petugas (optional) - fetch petugas emails if available
$qt = mysqli_prepare($koneksi, "SELECT email FROM tb_user WHERE role IN ('petugas','admin') AND email IS NOT NULL AND email<>''");
mysqli_stmt_execute($qt);
$rez = mysqli_stmt_get_result($qt);
$subject = 'Permintaan Perpanjangan Baru';
$body = '<p>Ada permintaan perpanjangan baru. Silakan cek sistem.</p>';
while ($row = mysqli_fetch_assoc($rez)) {
    if (!empty($row['email'])) { send_mail_simple($row['email'], $subject, $body); }
}

view_head('Berhasil');
?>
<div class="card"><div class="card-body"><div class="alert alert-success">
Permintaan perpanjangan terkirim.</div><a class="btn btn-primary" href="index.php?url=dashboard_user">Kembali</a></div></div>
<?php view_footer(); ?>