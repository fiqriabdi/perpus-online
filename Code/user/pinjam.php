<?php
// user/pinjam.php
require_once __DIR__ . '/../public/_head.php';
if (!isset($_SESSION['user'])) { header('Location: ../public/index.php?url=login'); exit; }
$user = $_SESSION['user'];
$buku_id = intval($_GET['buku_id'] ?? 0);
if ($buku_id<=0) die('Buku tidak valid');

$stmt = mysqli_prepare($koneksi, "SELECT id, judul, stok FROM tb_buku WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt,"i",$buku_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$b = mysqli_fetch_assoc($res);
if (!$b) die('Buku tidak ditemukan');
if ($b['stok'] <= 0) die('Stok habis');

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $tgl_pinjam = date('Y-m-d');
    $tgl_jatuh = date('Y-m-d', strtotime('+7 days'));
    $ins = mysqli_prepare($koneksi, "INSERT INTO tb_peminjaman (user_id, buku_id, tgl_pinjam, tgl_jatuh_tempo) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins,"iiss",$user['id'],$buku_id,$tgl_pinjam,$tgl_jatuh);
    mysqli_stmt_execute($ins);
    $pid = mysqli_insert_id($koneksi);
    $upd = mysqli_prepare($koneksi, "UPDATE tb_buku SET stok = stok - 1 WHERE id = ?");
    mysqli_stmt_bind_param($upd,"i",$buku_id);
    mysqli_stmt_execute($upd);
    addLog($koneksi, $user['id'], "Pinjam buku ID: $buku_id -> peminjaman ID: $pid");
    header('Location: ../public/index.php?url=dashboard_user');
    exit;
}
view_head('Pinjam Buku - '.e($b['judul']));
?>