<?php
require_once __DIR__ . '/../public/_head.php';

view_head('Dashboard Anggota');
if (!isset($_SESSION['user'])) { header('Location: index.php?url=login'); exit; }
$user = $_SESSION['user'];
?>
<div class="card">
  <div class="card-body">
    <h4>Dashboard Anggota</h4>
    <p>Selamat datang, <strong><?= e($user['nama'] ?? $user['username']) ?></strong></p>
    <a class="btn btn-primary" href="index.php?url=ajukan">Ajukan Perpanjangan</a>
    <a class="btn btn-outline-secondary" href="index.php?url=logout">Logout</a>
  </div>
</div>
<?php view_footer(); ?>