<?php
require_once __DIR__ . '/../public/_head.php';
view_head('Permintaan Perpanjangan');
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['petugas','admin'])) { header('Location: index.php?url=login'); exit; }

$q = mysqli_query($koneksi, "SELECT p.id as req_id, p.request_date, p.status, 
pm.buku_judul, pm.tgl_jatuh_tempo, u.nama, p.peminjaman_id FROM tb_perpanjangan p 
JOIN tb_peminjaman pm ON pm.id = p.peminjaman_id JOIN tb_user u ON u.id = p.user_id WHERE p.status = 'pending' ORDER BY p.request_date ASC");
?>
<div class="card">
  <div class="card-body">
    <h4>Permintaan Perpanjangan (Pending)</h4>
    <?php if (mysqli_num_rows($q)===0): ?>
      <div class="alert alert-info">Tidak ada permintaan.</div>
    <?php else: ?>
      <div class="list-group">
      <?php while ($r = mysqli_fetch_assoc($q)): ?>
        <div class="list-group-item">
          <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1"><?= e($r['nama']) ?></h5>
            <small><?= e($r['request_date']) ?></small>
          </div>
          <p class="mb-1"><?= e($r['buku_judul']) ?> — Jatuh tempo: <?= e($r['tgl_jatuh_tempo']) ?></p>
          <a href="index.php?url=petugas_acc&id=<?= $r['req_id'] ?>&pid=<?= $r['peminjaman_id'] ?>" class="btn btn-success btn-sm">ACC</a>
          <a href="index.php?url=petugas_reject&id=<?= $r['req_id'] ?>" class="btn btn-danger btn-sm">Tolak</a>
        </div>
      <?php endwhile; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php view_footer(); ?>