<?php
// admin/user_list.php
require_once __DIR__ . '/../public/_head.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role']!=='admin') { header('Location: ../public/index.php?url=login'); exit; }
view_head('Daftar User');

$q = mysqli_query($koneksi, "SELECT id,nama,username,email,role,created_at FROM tb_user ORDER BY id DESC");
?>