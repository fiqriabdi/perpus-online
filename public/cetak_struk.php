<?php
require '../config/koneksi.php';
if (!isset($_SESSION['petugas']) || !isset($_SESSION['struk'])) {
    header("Location: login.php"); exit;
}
$s = $_SESSION['struk'];
unset($_SESSION['struk']); // biar cuma sekali cetak
?>
<!DOCTYPE html>
<html>
<head>
    <title>Struk Pengembalian</title>
    <style>
        body{font-family:Arial;margin:0;padding:20px;background:#f0f0f0}
        .struk{width:300px;margin:auto;background:white;padding:20px;border:2px dashed #000;text-align:center;font-size:14px}
        .judul{font-size:18px;font-weight:bold;margin-bottom:15px}
        hr{border:1px dashed #000;margin:15px 0}
        .besar{font-size:20px;font-weight:bold;color:red}
        button{margin-top:20px;padding:10px 20px;font-size:16px}
    </style>
</head>
<body onload="window.print()">
<div class="struk">
    <div class="judul">STRUK PENGEMBALIAN BUKU</div>
    <hr>
    Kode Buku : <b><?= $s['kode_buku'] ?></b><br>
    NIM       : <?= $s['nim'] ?><br>
    Nama      : <?= $s['nama'] ?><br>
    Prodi     : <?= $s['prodi'] ?><br>
    <hr>
    Dikembalikan : <?= $s['dikembalikan'] ?><br>
    Terlambat    : <?= $s['telat'] ?> hari<br>
    <span class="besar">DENDA: Rp <?= number_format($s['denda']) ?></span><br>
    <hr>
    Petugas: <?= $s['petugas'] ?><br>
    Terima kasih
</div>
<br>
<center>
    <button onclick="location.href='dashboard.php'">Tutup</button>
    <button onclick="window.print()">Cetak Ulang</button>
</center>
</body>
</html>