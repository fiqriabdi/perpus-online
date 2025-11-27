<?php
require '../config/koneksi.php';
if (!isset($_SESSION['petugas'])) { header("Location: login.php"); exit; }

if ($_POST && !empty($_POST['id_perpanjangan'])) {
    $id = (int)$_POST['id_perpanjangan'];
    $hari_ini = date('Y-m-d');

    // Ambil data perpanjangan
    $q = mysqli_query($conn, "SELECT p.*, m.nim, m.nama, m.prodi 
                              FROM perpanjangan p 
                              JOIN mahasiswa m ON p.mhs_id = m.id 
                              WHERE p.id = $id");
    if (!$data = mysqli_fetch_assoc($q)) {
        die("Data tidak ditemukan.");
    }

    if ($data['tanggal_dikembalikan'] !== null) {
        die("Buku ini sudah dikembalikan sebelumnya.");
    }

    // Hitung denda
    $telat = max(0, (strtotime($hari_ini) - strtotime($data['tanggal_kembali'])) / 86400);
    $denda = $telat * 500;

    // Update tanggal dikembalikan + keterangan denda
    $ket = "Dikembalikan tanggal " . date('d-m-Y');
    if ($denda > 0) {
        $ket .= " → Terlambat $telat hari → Denda Rp " . number_format($denda);
    } else {
        $ket .= " → Tepat waktu, tidak ada denda";
    }

    mysqli_query($conn, "UPDATE perpanjangan 
                         SET tanggal_dikembalikan = '$hari_ini', 
                             keterangan = CONCAT(IFNULL(keterangan,''), ' | $ket')
                         WHERE id = $id");

    // Tampilkan hasil
    echo "<h2 style='color:green'>Pengembalian Berhasil!</h2>";
    echo "<p><b>Kode Buku:</b> {$data['kode_buku']}<br>";
    echo "<b>NIM:</b> {$data['nim']} - {$data['nama']} ({$data['prodi']})<br>";
    echo "<b>Dikembalikan:</b> " . date('d-m-Y') . "<br>";
    echo "<b>Denda:</b> <b style='color:red'>Rp " . number_format($denda) . "</b> ($telat hari telat)</p>";
    echo "<hr>";
    echo "<a href='form_pengembalian.php'>Kembali ke Pengembalian</a> | ";
    echo "<a href='dashboard.php'>Dashboard</a>";
}
?>