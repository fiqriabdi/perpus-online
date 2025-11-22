<?php
// admin/buku_delete.php
require_once __DIR__ . '/../public/_head.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) { header('Location: ../public/index.php?url=login'); exit; }

$id = intval($_GET['id'] ?? 0);
if ($id<=0) die('ID tidak valid');

$stmt = mysqli_prepare($koneksi, "SELECT cover FROM tb_buku WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$r = mysqli_fetch_assoc($res);
if ($r && $r['cover'] && file_exists(__DIR__.'/../public/uploads/'.$r['cover'])) unlink(__DIR__.'/../public/uploads/'.$r['cover']);

$del = mysqli_prepare($koneksi, "DELETE FROM tb_buku WHERE id = ?");
mysqli_stmt_bind_param($del,"i",$id);
mysqli_stmt_execute($del);
addLog($koneksi, $_SESSION['user']['id'], "Hapus buku ID: $id");
header('Location: ../public/index.php?url=admin_buku_list');
exit;
