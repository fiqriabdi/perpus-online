<?php
// user/kembali.php
require_once __DIR__ . '/../public/_head.php';
if (!isset($_SESSION['user'])) { header('Location: ../public/index.php?url=login'); exit; }
$user = $_SESSION['user'];
$pinjam_id = intval($_GET['pinjam_id'] ?? 0);
if ($pinjam_id<=0) die('ID tidak valid');

$stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_peminjaman WHERE id = ? AND user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt,"ii",$pinjam_id,$user['id']);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$p = mysqli_fetch_assoc($res);
if (!$p) die('Peminjaman tidak ditemukan');
if ($p['status'] === 'dikembalikan') die('Sudah dikembalikan');

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $u = mysqli_prepare($koneksi, "UPDATE tb_peminjaman SET status='dikembalikan' WHERE id = ?");
    mysqli_stmt_bind_param($u,"i",$pinjam_id);
    mysqli_stmt_execute($u);
    $upd = mysqli_prepare($koneksi, "UPDATE tb_buku SET stok = stok + 1 WHERE id = ?");
    mysqli_stmt_bind_param($upd,"i",$p['buku_id']);
    mysqli_stmt_execute($upd);
    addLog($koneksi, $user['id'], "Kembalikan peminjaman ID: $pinjam_id");
    header('Location: ../public/index.php?url=dashboard_user');
    exit;
}
view_head('Kembalikan Buku');
?>