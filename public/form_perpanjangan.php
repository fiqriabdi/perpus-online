<?php
require '../config/koneksi.php';
if (!isset($_SESSION['petugas'])) { header("Location: login.php"); exit; }
$petugas = $_SESSION['petugas'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perpanjangan Buku</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            font-family: Arial, sans-serif;
        }
        .card-form {
            max-width: 400px;                    /* SUPER KECIL */
            margin: auto;
            border-radius: 16px !important;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(0,0,0,0.5);
        }
        .card-header {
            background: #2c3e50;
            color: white;
            padding: 1rem;
            text-align: center;
            border-radius: 16px 16px 0 0 !important;
        }
        .card-header i { font-size: 36px; margin-bottom: 6px; }
        .card-header h1 { margin: 6px 0 2px; font-size: 19px; font-weight: 800; }
        .card-header p { margin: 0; font-size: 13px; opacity: 0.9; }

        .card-body { padding: 1.4rem 1.6rem; background: white; }

        .petugas-badge {
            background: #e3f2fd; color: #1565c0; padding: 6px 14px;
            border-radius: 30px; font-size: 13px; font-weight: bold;
            display: inline-block; margin-bottom: 14px;
        }

        .form-control, .form-select {
            padding: 10px 12px; font-size: 14.5px; border-radius: 8px;
            border: 1.5px solid #ddd;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2c3e50; box-shadow: 0 0 0 3px rgba(44,62,80,0.15);
        }

        .btn-simpan {
            background: #2c3e50; border: none; padding: 12px;
            font-size: 16px; font-weight: bold; border-radius: 10px;
            margin-top: 10px;
        }
        .btn-simpan:hover { background: #1a252f; }

        .card-footer {
            background: #2c3e50;
            color: #888;                    /* lebih redup dari #aaa */
            padding: 0.8rem;
            text-align: center;
            font-size: 12px;
            border-radius: 0 0 16px 16px !important;
        }
        .card-footer a {
            color: #7fb8e0;                 /* biru muda lebih soft dari #a0d8ff */
            opacity: 0.6;                   /* REDUP BANGET dari awal */
            transition: opacity 0.3s ease;
        }
        .card-footer a:hover {
            opacity: 1;                     /* baru terang pas di-hover */
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="card-form">
    <div class="card-header">
        <i class="fas fa-plus-circle"></i>
        <h1>PERPANJANGAN</h1>
        <p>+1 Hari Otomatis</p>
    </div>

    <div class="card-body">
        <div class="text-center mb-3">
            <span class="petugas-badge">
                <i class="fas fa-user"></i> <?= htmlspecialchars($petugas['sure_name']) ?>
            </span>
        </div>

        <form action="proses_perpanjang.php" method="POST">
            <input type="text" name="kode_buku" class="form-control mb-3" 
                   placeholder="Kode Buku" required autofocus>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <input type="text" name="nim" class="form-control" placeholder="NIM" required>
                </div>
                <div class="col-6">
                    <input type="text" name="nama" class="form-control" placeholder="Nama" required>
                </div>
            </div>

            <select name="prodi" class="form-select mb-3" required>
                <option value="" disabled selected>Pilih Prodi</option>
                <option>Informatika</option>
                <option>Sistem Informasi</option>
                <option>Teknik Komputer</option>
                <option>Manajemen Ritel</option>
                <option>Bisnis Digital</option>
                <option>Magister TI</option>
            </select>

            <textarea name="keterangan" rows="1" class="form-control mb-3" 
                      placeholder="Keterangan (opsional)"></textarea>

            <button type="submit" class="btn btn-simpan text-white w-100">
                <i class="fas fa-check me-1"></i> CATAT PERPANJANGAN
            </button>
        </form>
    </div>

    <div class="card-footer">
        <a href="dashboard.php" class="text-white text-decoration-none d-flex align-items-center justify-content-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span class="fw-bold">Kembali ke Dashboard</span>
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>