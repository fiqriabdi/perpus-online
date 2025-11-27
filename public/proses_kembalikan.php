<?php
require '../config/koneksi.php';
if (!isset($_SESSION['petugas'])) { header("Location: login.php"); exit; }

if ($_POST && !empty($_POST['id_perpanjangan'])) {
    $id = (int)$_POST['id_perpanjangan'];
    $hari_ini = date('Y-m-d');

    $q = mysqli_query($conn, "SELECT p.*, m.nim, m.nama, m.prodi 
                              FROM perpanjangan p 
                              JOIN mahasiswa m ON p.mhs_id = m.id 
                              WHERE p.id = $id");
    $data = mysqli_fetch_assoc($q);

    if ($data['tanggal_dikembalikan'] !== null) {
        die("Buku sudah dikembalikan sebelumnya.");
    }

    $telat = max(0, (strtotime($hari_ini) - strtotime($data['tanggal_kembali'])) / 86400);
    $denda = $telat * 500;

    $ket = "Dikembalikan tgl " . date('d-m-Y');
    $ket .= $denda > 0 ? " → Terlambat $telat hari → Denda Rp " . number_format($denda) : " → Tepat waktu";

    mysqli_query($conn, "UPDATE perpanjangan 
                         SET tanggal_dikembalikan = '$hari_ini',
                             keterangan = CONCAT(IFNULL(keterangan,''), ' | $ket')
                         WHERE id = $id");

    // Simpan data untuk struk
    $_SESSION['struk'] = [
        'kode_buku' => $data['kode_buku'],
        'nim'       => $data['nim'],
        'nama'      => $data['nama'],
        'prodi'     => $data['prodi'],
        'dikembalikan' => date('d-m-Y'),
        'telat'     => $telat,
        'denda'     => $denda,
        'petugas'   => $_SESSION['petugas']['sure_name']
    ];

    // Redirect ke halaman cetak struk
    header("Location: cetak_struk.php");
    exit;
}
?>