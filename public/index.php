<?php
require '../config/koneksi.php';
if (isset($_SESSION['petugas'])) {
    header("Location: dashboard.php");
    exit;
}
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
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .card {
            max-width: 340px; /* DIRAPATKAN */
            margin: auto;
            border-radius: 15px !important;
            overflow: hidden;
            box-shadow: 0 12px 35px rgba(0,0,0,0.55);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .card-header {
            background: linear-gradient(135deg, #2c3e50, #1a252f);
            color: white;
            padding: 1.1rem 1rem; /* DIRAPATKAN */
            text-align: center;
            border-radius: 15px 15px 0 0 !important;
        }
        .card-header i {
            font-size: 40px;    /* MENGECIL */
            margin-bottom: 6px;
            background: rgba(255,255,255,0.12);
            width: 70px; height: 70px; /* MENGECIL */
            line-height: 70px;
            border-radius: 50%;
            display: inline-block;
        }
        .card-header h1 {
            margin: 8px 0 2px;
            font-size: 18px; /* MENGECIL */
            font-weight: 800;
        }
        .card-header p {
            margin: 0;
            font-size: 12.5px; /* MENGECIL */
        }

        .card-body {
            padding: 1.2rem 1.4rem; /* DIRAPATKAN */
            background: white;
        }
        .section-title {
            font-size: 13.5px; /* MENGECIL */
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 9px;
            text-align: center;
        }
        .list-group-item {
            padding: 8px 12px; /* DIRAPATKAN */
            font-size: 13px; /* MENGECIL */
            border-left: 3px solid #2c3e50; /* MENGECIL */
            background: #f8f9fa;
            border-radius: 6px;
            margin-bottom: 6px;
        }
        .list-group-item i {
            margin-right: 9px;
            width: 16px;
            font-size: 13px;
        }

        .btn-login {
            background: linear-gradient(135deg, #2c3e50, #1a252f);
            border: none;
            padding: 10px; /* DIRAPATKAN */
            font-size: 14px; /* MENGECIL */
            font-weight: 800;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(44,62,80,0.4);
        }

        .card-footer {
            background: #2c3e50;
            color: #ccc;
            padding: 0.8rem; /* DIRAPATKAN */
            text-align: center;
            font-size: 11.5px; /* MENGECIL */
            border-radius: 0 0 15px 15px !important;
        }
        .card-footer i {
            font-size: 14px;
        }
        .card-footer strong {
            font-size: 12px;
        }

        /* Efek hover list item */
        .list-group-item {
            transition: all 0.25s ease;
        }
        .list-group-item:hover {
            background: #e9ecef;
            transform: translateX(3px);
        }

        /* Animasi tombol login */
        .btn-login {
            transition: all 0.32s ease;
        }
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 9px 22px rgba(44,62,80,0.55);
        }

        /* Animasi icon header saat hover */
        .card-header i {
            transition: transform 0.35s ease;
        }
        .card-header:hover i {
            transform: scale(1.07);
        }

    </style>
</head>
<body>

<div class="card">
    <div class="card-header">
        <i class="fas fa-book-open"></i>
        <h1>PERPANJANGAN BUKU</h1>
        <p><strong>Petugas Perpustakaan</strong></p>
    </div>

    <div class="card-body">
        <div class="section-title">
            <i class="fas fa-tasks text-primary me-1"></i>
            Tugas Petugas
        </div>

        <div class="list-group list-group-flush">
            <div class="list-group-item"><i class="fas fa-sign-in-alt"></i>Login ke sistem</div>
            <div class="list-group-item"><i class="fas fa-plus-circle"></i>Input data perpanjangan</div>
            <div class="list-group-item"><i class="fas fa-undo-alt"></i>Proses pengembalian & hitung denda</div>
            <div class="list-group-item"><i class="fas fa-print"></i>Cetak bukti transaksi</div>
            <div class="list-group-item"><i class="fas fa-gavel"></i>Sanksi Rp500/hari (otomatis)</div>
        </div>

        <div class="d-grid mt-3">
            <a href="login.php" class="btn btn-login text-white">
                <i class="fas fa-user-shield me-1"></i> LOGIN PETUGAS
            </a>
        </div>
    </div>

    <div class="card-footer">
        <i class="fas fa-info-circle"></i><br>
        Perpanjangan 1 hari • Denda otomatis • <strong>Data asli mahasiswa</strong>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
