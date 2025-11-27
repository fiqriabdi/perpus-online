<?php
require '../config/koneksi.php';
if (!isset($_SESSION['petugas'])) exit;

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Riwayat_Pengembalian_".date('d-m-Y').".xls");

echo "No\tKode Buku\tNIM\tNama\tProdi\tTgl Kembali\tTelat(hari)\tDenda\n";

$q = mysqli_query($conn, "SELECT p.*, m.nim,m.nama,m.prodi FROM perpanjangan p
                          JOIN mahasiswa m ON p.mhs_id=m.id
                          WHERE p.tanggal_dikembalikan IS NOT NULL
                          ORDER BY p.tanggal_dikembalikan DESC");
$no=1;
while($r=mysqli_fetch_assoc($q)) {
    $telat = max(0, (strtotime($r['tanggal_dikembalikan']) - strtotime($r['tanggal_kembali'])) / 86400);
    $denda = $telat * 500;
    echo "$no\t{$r['kode_buku']}\t{$r['nim']}\t{$r['nama']}\t{$r['prodi']}\t".
         date('d-m-Y', strtotime($r['tanggal_dikembalikan']))."\t$telat\t$denda\n";
    $no++;
}
?>