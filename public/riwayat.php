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
    <title>Riwayat Pengembalian & Total Denda</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body { background: #f8f9fa; padding: 20px 0; }
        .navbar { background: #2c3e50 !important; padding: 1rem; }
        .card { box-shadow: 0 4px 20px rgba(0,0,0,0.12); border: none; }
        .card-header {
            background: #2c3e50;
            color: white;
            border-radius: 12px 12px 0 0 !important;
        }
        .table thead { background: #0d6efd; color: white; }
        .total-denda {
            font-size: 1.6rem;
            font-weight: bold;
            color: #e74c3c;
            text-align: right;
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border-radius: 10px;
            border-left: 6px solid #ffc107;
        }
        .btn-excel {
            background: #1e7e34;
            border: none;
            padding: 12px 24px;
            font-weight: bold;
            border-radius: 10px;
            font-size: 16px;
        }
        .btn-excel:hover {
            background: #19692c;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 4rem;
            color: #0d6efd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="fas fa-history me-2"></i> Riwayat Pengembalian
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
                <i class="fas fa-clipboard-list me-2"></i>
                RIWAYAT PENGEMBALIAN BUKU & DENDA
            </h4>
        </div>

        <div class="card-body p-4">
            <!-- Tombol Export Excel -->
            <div class="text-end mb-4">
                <a href="export_excel.php" class="btn btn-excel text-white shadow-sm">
                    <i class="fas fa-file-excel fa-lg me-2"></i>
                    Export ke Excel
                </a>
            </div>

            <?php
            $q = mysqli_query($conn, "SELECT p.*, m.nim, m.nama, m.prodi 
                                      FROM perpanjangan p
                                      JOIN mahasiswa m ON p.mhs_id = m.id
                                      WHERE p.tanggal_dikembalikan IS NOT NULL
                                      ORDER BY p.tanggal_dikembalikan DESC");

            $total_denda = 0;

            if (mysqli_num_rows($q) > 0):
            ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th>Kode Buku</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Prodi</th>
                                <th>Tgl Kembali</th>
                                <th>Telat (hari)</th>
                                <th>Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($r = mysqli_fetch_assoc($q)): 
                                $telat = max(0, (strtotime($r['tanggal_dikembalikan']) - strtotime($r['tanggal_kembali'])) / 86400);
                                $denda = $telat * 500;
                                $total_denda += $denda;
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= $r['kode_buku'] ?></strong></td>
                                <td><?= $r['nim'] ?></td>
                                <td><?= $r['nama'] ?></td>
                                <td><?= $r['prodi'] ?></td>
                                <td><?= date('d-m-Y', strtotime($r['tanggal_dikembalikan'])) ?></td>
                                <td class="text-center">
                                    <?php if($telat > 0): ?>
                                        <span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> <?= $telat ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><i class="fas fa-check"></i> 0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-danger">Rp <?= number_format($denda) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TOTAL DENDA BESAR -->
                <div class="total-denda">
                    <i class="fas fa-coins me-3"></i>
                    TOTAL DENDA KESELURUHAN: 
                    <span class="fs-4">Rp <?= number_format($total_denda) ?></span>
                </div>

            <?php else: ?>
                <!-- KOSONG -->
                <div class="empty-state">
                    <i class="fas fa-receipt"></i>
                    <h4 class="mt-3 text-muted">Belum Ada Riwayat Pengembalian</h4>
                    <p>Semua buku masih dalam masa peminjaman atau perpanjangan.</p>
                    <a href="dashboard.php" class="btn btn-primary mt-3">
                        <i class="fas fa-tachometer-alt"></i> Kembali ke Dashboard
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-footer bg-light text-center">
            <a href="dashboard.php" class="text-decoration-none">
                <i class="fas fa-home me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>