<?php
// admin/buku_edit.php
require_once __DIR__ . '/../public/_head.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) { header('Location: ../public/index.php?url=login'); exit; }
view_head('Edit Buku');

$id = intval($_GET['id'] ?? 0);
if ($id<=0) die('ID tidak valid');
$stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_buku WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$b = mysqli_fetch_assoc($res);
if (!$b) die('Buku tidak ditemukan');

$err=''; $success='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $penerbit = trim($_POST['penerbit'] ?? '');
    $tahun = intval($_POST['tahun'] ?? 0);
    $kategori = trim($_POST['kategori'] ?? '');
    $stok = intval($_POST['stok'] ?? 1);
    $coverName = $b['cover'];
    if (!empty($_FILES['cover']) && $_FILES['cover']['error']===UPLOAD_ERR_OK) {
        $up = upload_cover($_FILES['cover']);
        if ($up['ok']) {
            if ($coverName && file_exists(__DIR__.'/../public/uploads/'.$coverName)) unlink(__DIR__.'/../public/uploads/'.$coverName);
            $coverName = $up['file'];
        } else $err = $up['error'];
    }
    if (!$err) {
        $u = mysqli_prepare($koneksi, "UPDATE tb_buku SET judul=?, penulis=?, penerbit=?, tahun=?, kategori=?, stok=?, cover=? WHERE id=?");
        mysqli_stmt_bind_param($u,"sssiissi", $judul, $penulis, $penerbit, $tahun, $kategori, $stok, $coverName, $id);
        mysqli_stmt_execute($u);
        addLog($koneksi, $_SESSION['user']['id'], "Edit buku ID: $id");
        $success = 'Perubahan disimpan.';
    }
}
?>