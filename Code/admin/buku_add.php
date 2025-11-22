<?php
// admin/buku_add.php
require_once __DIR__ . '/../public/_head.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) { header('Location: ../public/index.php?url=login'); exit; }
view_head('Tambah Buku');

$err=''; $success='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $penerbit = trim($_POST['penerbit'] ?? '');
    $tahun = intval($_POST['tahun'] ?? 0);
    $kategori = trim($_POST['kategori'] ?? '');
    $stok = intval($_POST['stok'] ?? 1);
    $coverName = null;
    if (!empty($_FILES['cover']) && $_FILES['cover']['error']===UPLOAD_ERR_OK) {
        $up = upload_cover($_FILES['cover']);
        if ($up['ok']) $coverName = $up['file'];
        else $err = $up['error'];
    }
    if (!$err) {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO tb_buku (judul, penulis, penerbit, tahun, kategori, stok, cover) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssissi", $judul, $penulis, $penerbit, $tahun, $kategori, $stok, $coverName);
        mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($koneksi);
        addLog($koneksi, $_SESSION['user']['id'], "Tambah buku ID: $id");
        $success = 'Buku ditambahkan.';
    }
}
?>