<?php
require '../config/koneksi.php';
if (!isset($_SESSION['petugas'])) {
    header("Location: login.php");
    exit;
}
$petugas = $_SESSION['petugas'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Petugas Perpanjangan</title>

    <!-- Bootstrap 5 + Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body { background: #f8f9fa; padding: 20px 0; }
        .navbar { background: #2c3e50 !important; padding: 1rem; }
        .card { box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .btn-primary { background: #2c3e50; border: none; }
        .btn-primary:hover { background: #1a252f; }
        .btn-success { background: #27ae60; border: none; }
        .btn-success:hover { background: #219653; }
        .telat { color: #e74c3c; font-weight: bold; }
        .table thead { background: #0d6efd; color: white; }
        .footer-text { font-size: 0.9rem; color: #6c757d; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">
            <i class="fas fa-book-open me-2"></i> Perpanjangan Buku
        </a>
        <div class="d-flex align-items-center text-white">
            <i class="fas fa-user-circle me-2"></i>
            <?= htmlspecialchars($petugas['sure_name']) ?>
            <a href="logout.php" class="btn btn-danger btn-sm ms-3">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row g-4">
        <!-- Menu Cepat -->
        <div class="col-12">
            <h4 class="mb-3"><i class="fas fa-tachometer-alt text-primary"></i> Menu Cepat</h4>
            <div class="row g-3">
                <div class="col-md-4">
                    <a href="form_perpanjangan.php" class="btn btn-primary btn-lg w-100 py-4 text-white shadow-sm">
                        <i class="fas fa-plus-circle fa-2x mb-2 d-block"></i>
                        <strong>Perpanjangan Baru</strong><br>
                        <small>+1 Hari Otomatis</small>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="form_pengembalian.php" class="btn btn-success btn-lg w-100 py-4 text-white shadow-sm">
                        <i class="fas fa-undo-alt fa-2x mb-2 d-block"></i>
                        <strong>Pengembalian Buku</strong><br>
                        <small>Hitung Denda Otomatis</small>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="riwayat.php" class="btn btn-info btn-lg w-100 py-4 text-white shadow-sm">
                        <i class="fas fa-history fa-2x mb-2 d-block"></i>
                        <strong>Riwayat & Total Denda</strong><br>
                        <small>Lihat Semua Transaksi</small>
                    </a>
                </div>
            </div>
        </div>

        <!-- Daftar Belum Dikembalikan -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-circle"></i> Perpanjangan Belum Dikembalikan</h5>
                </div>
                <div class="card-body p-0">
                    <?php
                    $q = mysqli_query($conn, "
                        SELECT p.id, p.kode_buku, p.tanggal_perpanjang, p.tanggal_kembali,
                               m.nim, m.nama, m.prodi
                        FROM perpanjangan p
                        JOIN mahasiswa m ON p.mhs_id = m.id
                        WHERE p.tanggal_dikembalikan IS NULL
                        ORDER BY p.tanggal_perpanjang DESC
                    ");

                    if (mysqli_num_rows($q) == 0): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                            <h5>Yeay! Semua buku sudah dikembalikan</h5>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Buku</th>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        <th>Prodi</th>
                                        <th>Tgl Perpanjang</th>
                                        <th>Harus Kembali</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while ($r = mysqli_fetch_assoc($q)): 
                                        $telat = max(0, (strtotime(date('Y-m-d')) - strtotime($r['tanggal_kembali'])) / 86400);
                                        $status = $telat > 0 ? "<span class='telat'><i class='fas fa-exclamation-triangle'></i> Telat $telat hari</span>" : "<span class='text-success'><i class='fas fa-check'></i> Tepat waktu</span>";
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><strong><?= $r['kode_buku'] ?></strong></td>
                                        <td><?= $r['nim'] ?></td>
                                        <td><?= $r['nama'] ?></td>
                                        <td><?= $r['prodi'] ?></td>
                                        <td><?= date('d-m-Y', strtotime($r['tanggal_perpanjang'])) ?></td>
                                        <td><?= date('d-m-Y', strtotime($r['tanggal_kembali'])) ?></td>
                                        <td><?= $status ?></td>
                                        <td>
                                            <form action="proses_kembalikan.php" method="POST" class="d-inline">
                                                <input type="hidden" name="id_perpanjangan" value="<?= $r['id'] ?>">
                                                <button type="submit" class="btn btn-success btn-sm"
                                                        onclick="return confirm('Kembalikan buku ini sekarang?')">
                                                    <i class="fas fa-check"></i> Kembalikan
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5 footer-text">
        <small>
            <i class="fas fa-info-circle"></i> 
            Sistem hanya mencatat perpanjangan 1 hari & pengembalian • Denda Rp500/hari • 100% data asli dari mahasiswa
        </small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>