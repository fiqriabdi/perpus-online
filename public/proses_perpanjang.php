<?php
require '../config/koneksi.php';
if (!isset($_SESSION['petugas'])) {
    header("Location: login.php");
    exit;
}

if ($_POST) {
    $kode_buku = strtoupper(trim($_POST['kode_buku']));
    $nim       = trim($_POST['nim']);
    $nama      = trim($_POST['nama']);
    $prodi     = $_POST['prodi'];
    $ket       = trim($_POST['keterangan']);
    $petugas_id = $_SESSION['petugas']['id'];

    // CEK APAKAH BUKU MASIH DIPINJAM (belum ada tanggal_dikembalikan)
    $cek_pinjam = mysqli_query($conn, "
        SELECT m.nama 
        FROM perpanjangan p 
        JOIN mahasiswa m ON p.mhs_id = m.id 
        WHERE p.kode_buku = '$kode_buku' 
          AND p.tanggal_dikembalikan IS NULL 
        LIMIT 1
    ");

    if (mysqli_num_rows($cek_pinjam) > 0) {
        $pemilik = mysqli_fetch_assoc($cek_pinjam)['nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Buku Sedang Dipinjam</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
    body { background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%); min-height: 100vh; display: flex; align-items: center; margin: 0; font-family: 'Segoe UI', Arial, sans-serif; }
    .card { max-width: 360px; margin: auto; border-radius: 15px; overflow: hidden; box-shadow: 0 12px 35px rgba(0,0,0,0.6); }
    .card-header { background: #e74c3c; padding: 1rem; text-align: center; color: #fff; }
    .card-header i { font-size: 34px; background: rgba(255,255,255,0.15); padding: 12px; border-radius: 50%; }
    }
    .card-header h1 { font-size: 17px; font-weight: 700; margin: 5px 0 0; }
    .card-body { background: white; padding: 1.4rem 1.6rem; text-align: center; }
    .alert { background: #fee; border: 1px solid #fcc; padding: 14px; border-radius: 10px; color: #c0392b; font-size: 14px; }
    .btn-mini { padding: 9px 14px; font-size: 13px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; margin: 5px; }
    .btn-back { background: #e74c3c; color: white; }
    .btn-back:hover { background: #c0392b; }
</style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <i class="fas fa-ban"></i>
        <h1>GAGAL!</h1>
    </div>
    <div class="card-body">
        <div class="alert">
            Buku dengan kode<br>
            <strong style="font-size:18px"><?= $kode_buku ?></strong><br><br>
            sedang dipinjam oleh:<br>
            <strong><?= htmlspecialchars($pemilik) ?></strong>
        </div>
        <p class="text-muted small">Tidak dapat diperpanjang sebelum buku dikembalikan.</p>
        <a href="form_perpanjangan.php" class="btn-mini btn-back">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
</body>
</html>
<?php
        exit;
    }

    // === LANJUTKAN PROSES KALAU BUKU SUDAH BEBAS ===
    $cek = mysqli_query($conn, "SELECT id FROM mahasiswa WHERE nim='$nim'");
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($conn, "INSERT INTO mahasiswa (nim, nama, prodi) VALUES ('$nim','$nama','$prodi')");
        $mhs_id = mysqli_insert_id($conn);
    } else {
        $mhs_id = mysqli_fetch_assoc($cek)['id'];
    }

    $sql = "INSERT INTO perpanjangan (petugas_id, mhs_id, kode_buku, keterangan)
            VALUES ($petugas_id, $mhs_id, '$kode_buku', '$ket')";

    if (mysqli_query($conn, $sql)) {
        $tampil_kembali = date('d-m-Y', strtotime('+1 day'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Perpanjangan Berhasil</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #2c3e50 0%, #1a252f);
    min-height: 100vh;
    display: flex;
    align-items: center;
    margin: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
}
.card-success {
    max-width: 360px;
    margin: auto;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 12px 35px rgba(0,0,0,0.55);
}
.card-header {
    background: #27ae60;
    padding: 1rem;
    text-align: center;
    color: #fff;
}
.card-header i {
    font-size: 34px;
    margin-bottom: 6px;
    background: rgba(255,255,255,0.15);
    padding: 12px;
    border-radius: 50%;
}
.card-header h1 {
    font-size: 17px;
    font-weight: 700;
    margin: 5px 0 0;
}
.card-body {
    background: white;
    padding: 1.2rem 1.5rem;
}
.info-row {
    background: #f8f9fa;
    padding: 8px 12px;
    border-radius: 8px;
    margin-bottom: 8px;
    font-size: 13px;
}
.info-row strong { color: #2c3e50; }
.badge {
    background: #27ae60 !important;
    font-size: 12px;
    padding: 4px 8px;
}
.btn-mini {
    border: none;
    padding: 9px 12px;
    font-size: 13px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
}
.btn-again { background: #27ae60; color: #fff; }
.btn-again:hover { background: #229954; }
.btn-dashboard { background: #2c3e50; color: #fff; }
.btn-dashboard:hover { background: #1a252f; }
</style>
</head>
<body>
<div class="card-success">
    <div class="card-header">
        <i class="fas fa-check"></i>
        <h1>Berhasil Dicatat</h1>
    </div>
    <div class="card-body">
        <div class="info-row"><strong>Kode Buku:</strong> <span class="badge"><?= $kode_buku ?></span></div>
        <div class="info-row"><strong>Mahasiswa:</strong> <?= $nama ?> (<?= $prodi ?>)</div>
        <div class="info-row"><strong>NIM:</strong> <?= $nim ?></div>
        <div class="info-row"><strong>Tgl. Perpanjang:</strong> <?= date('d-m-Y') ?></div>
        <div class="info-row"><strong>Harus Kembali:</strong> <span class="text-danger fw-bold"><?= $tampil_kembali ?></span></div>
        <?php if($ket): ?><div class="info-row"><strong>Keterangan:</strong> <?= htmlspecialchars($ket) ?></div><?php endif; ?>
        <div class="text-center mt-3">
            <a href="form_perpanjang.php" class="btn-mini btn-again"><i class="fas fa-plus me-1"></i>Catat Lagi</a>
            <a href="dashboard.php" class="btn-mini btn-dashboard"><i class="fas fa-home me-1"></i>Dashboard</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
    } else {
        echo "<div style='text-align:center;color:red;margin-top:50px;'>Error: " . mysqli_error($conn) . "</div>";
    }
    exit;
}
?>