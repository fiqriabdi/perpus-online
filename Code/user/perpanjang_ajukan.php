<?php
require_once __DIR__ . '/_head.php';
view_head('Ajukan Perpanjangan');
if (!isset($_SESSION['user'])) { header('Location: index.php?url=login'); exit; }
$user = $_SESSION['user'];

// fetch active borrowings
$stmt = mysqli_prepare($koneksi, "SELECT id, buku_judul, tgl_jatuh_tempo FROM tb_peminjaman WHERE user_id = ? AND status = 'dipinjam'");
mysqli_stmt_bind_param($stmt, "i", $user['id']);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>
<div class="card">
  <div class="card-body">
    <h4>Ajukan Perpanjangan</h4>
    <?php if (mysqli_num_rows($res)===0): ?>
      <div class="alert alert-info">Tidak ada peminjaman aktif.</div>
    <?php else: ?>
      <form method="post" action="index.php?url=process_ajukan">
        <?php while ($r = mysqli_fetch_assoc($res)): ?>
          <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="id_peminjaman" id="p<?= $r['id'] ?>" value="<?= $r['id'] ?>" required>
            <label class="form-check-label" for="p<?= $r['id'] ?>">
              <?= e($r['buku_judul']) ?> — Jatuh tempo: <?= e($r['tgl_jatuh_tempo']) ?>
            </label>
          </div>
        <?php endwhile; ?>
        <button class="btn btn-primary">Ajukan</button>
        <a class="btn btn-secondary" href="index.php">Batal</a>
      </form>
    <?php endif; ?>
  </div>
</div>
<?php view_footer(); ?>