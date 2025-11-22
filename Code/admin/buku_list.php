<?php
// admin/buku_list.php
require_once __DIR__ . '/../public/_head.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) { header('Location: ../public/index.php?url=login'); exit; }
view_head('Manajemen Buku');

$q = mysqli_query($koneksi, "SELECT * FROM tb_buku ORDER BY created_at DESC");
?>
<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between">
      <h4>Daftar Buku</h4>
      <a class="btn btn-success" href="../public/index.php?url=admin_buku_add">Tambah Buku</a>
    </div>
    <hr>
    <table class="table table-striped">
      <thead><tr><th>#</th><th>Judul</th><th>Penulis</th><th>Stok</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php while ($b = mysqli_fetch_assoc($q)): ?>
        <tr>
          <td><?= e($b['id']) ?></td>
          <td><?= e($b['judul']) ?></td>
          <td><?= e($b['penulis']) ?></td>
          <td><?= (int)$b['stok'] ?></td>
          <td>
            <a class="btn btn-sm btn-warning" href="../public/index.php?url=admin_buku_edit&id=<?= $b['id'] ?>">Edit</a>
            <a class="btn btn-sm btn-danger" href="../public/index.php?url=admin_buku_delete&id=<?= $b['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php view_footer(); ?>
