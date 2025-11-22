<?php
require_once __DIR__ . '/../public/_head.php';
view_head('Log Aktivitas');
if (!isset($_SESSION['user']) || $_SESSION['user']['role']!=='admin') { header('Location: index.php?url=login'); exit; }

$q = mysqli_query($koneksi, "SELECT l.*, u.nama FROM audit_log l LEFT JOIN tb_user u ON u.id = l.user_id ORDER BY l.id DESC LIMIT 200");

?>
<div class="card">
  <div class="card-body">
    <h4>Log Aktivitas </h4>
    <table class="table table-sm table-striped">
      <thead><tr><th>Waktu</th><th>User</th><th>Aktivitas</th></tr></thead>
      <tbody>
      <?php while ($r = mysqli_fetch_assoc($q)): ?>
      <tr>
        <td><?= e($r['created_at']) ?></td>
        <td><?= e($r['nama'] ?? 'System') ?></td>
        <td><?= e($r['activity']) ?></td>
      </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php view_footer(); ?>