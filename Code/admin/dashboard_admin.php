<?php
require_once __DIR__ . '/../public/_head.php';

view_head('Dashboard Admin');
if (!isset($_SESSION['user']) || $_SESSION['user']['role']!=='admin') 
  { header('Location: index.php?url=login'); exit; }
$user = $_SESSION['user'];
?>
<div class="card">
  <div class="card-body">
    <h4>Dashboard Admin</h4>
    <p>Selamat datang, <strong><?= e($user['nama'] ?? $user['username']) ?></strong></p>
    <a class="btn btn-success" href="index.php?url=petugas_list">Kelola Permintaan</a>
    <a class="btn btn-outline-secondary" href="index.php?url=logs">Lihat Log</a>
    <a class="btn btn-outline-secondary" href="index.php?url=logout">Logout</a>
  </div>
</div>
<?php view_footer(); ?>