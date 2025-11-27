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
    <title>Pengembalian Buku</title>

    <!-- Bootstrap 5 + Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body { 
            background: #f8f9fa; 
            padding: 20px 0; 
            font-family: Arial, sans-serif;
        }
        .navbar { 
            background: #2c3e50 !important; 
            padding: 1rem; 
        }
        .card { 
            box-shadow: 0 4px 20px rgba(0,0,0,0.12); 
            border: none; 
        }
        .card-header {
            background: #2c3e50;
            color: white;
            border-radius: 12px 12px 0 0 !important;
        }
        .telat { 
            color: #e74c3c; 
            font-weight: bold; 
        }
        .btn-kembali {
            background: #27ae60;
            border: none;
            font-size: 14px;
            padding: 6px 14px;
            border-radius: 6px;
        }
        .btn-kembali:hover { 
            background: #219653; 
        }
        .form-control { 
            border-radius: 8px; 
        }
        .btn-search {
            background: #0d6efd;
            border: none;
            border-radius: 8px;
        }
        .btn-search:hover { 
            background: #0b5ed7; 
        }
        .footer-links a {
            color: #2c3e50;
            font-weight: 500;
            text-decoration: none;
        }
        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="fas fa-book-open me-2"></i> Perpanjangan Buku
        </a>
        <div class="text-white">
            <i class="fas fa-user me-2"></i> <?= htmlspecialchars($petugas['sure_name']) ?>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="card">
        <div class="card-header text-center">
            <h4 class="mb-0">
                <i class="fas fa-undo-alt me-2"></i>
                PENGEMBALIAN BUKU PERPANJANGAN
            </h4>
        </div>

        <div class="card-body p-4">
            <!-- FORM CARI -->
            <form method="GET" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <input type="text" name="cari" class="form-control form-control-lg" 
                               placeholder="Cari Kode Buku atau NIM..." 
                               value="<?= @$_GET['cari'] ?>" autofocus>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-search text-white w-100">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="form_pengembalian.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-sync"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <?php
            $where = "WHERE p.tanggal_dikembalikan IS NULL";
            if (!empty($_GET['cari'])) {
                $cari = mysqli_real_escape_string($conn, $_GET['cari']);
                $where .= " AND (p.kode_buku LIKE '%$cari%' OR m.nim LIKE '%$cari%')";
            }

            $q = mysqli_query($conn, "
                SELECT p.id, p.kode_buku, p.tanggal_perpanjang, p.tanggal_kembali,
                       m.nim, m.nama, m.prodi
                FROM perpanjangan p
                JOIN mahasiswa m ON p.mhs_id = m.id
                $where
                ORDER BY p.tanggal_perpanjang DESC
            ");

            if (mysqli_num_rows($q) == 0): ?>
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h5 class="text-success">Semua buku sudah dikembalikan!</h5>
                    <p class="text-muted">Tidak ada perpanjangan aktif saat ini.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
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
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($r = mysqli_fetch_assoc($q)): 
                                $telat = max(0, (strtotime(date('Y-m-d')) - strtotime($r['tanggal_kembali'])) / 86400);
                                $status = $telat > 0 
                                    ? "<span class='telat'><i class='fas fa-exclamation-triangle'></i> Telat $telat hari</span>"
                                    : "<span class='text-success'><i class='fas fa-check'></i> Tepat waktu</span>";
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
                                <td class="text-center">
                                    <form action="proses_kembalikan.php" method="POST" class="d-inline">
                                        <input type="hidden" name="id_perpanjangan" value="<?= $r['id'] ?>">
                                        <button type="submit" class="btn btn-kembali text-white btn-sm"
                                                onclick="return confirm('Kembalikan buku ini sekarang?')">
                                            <i class="fas fa-check me-1"></i> Kembalikan
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

        <div class="card-footer bg-light text-center footer-links">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <span class="mx-3 text-muted">|</span>
            <a href="form_perpanjang.php"><i class="fas fa-plus-circle"></i> Perpanjangan Baru</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>