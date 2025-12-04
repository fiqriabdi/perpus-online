## Dokumentasi Project

### Struktur Direktori  
<pre>
perpus-utdi-final/                 ← nama folder bebas, misal: perpus, perpus-utdi, dll
├── config/
│   └── database.php
├── fungsi/
│   ├── helper.php
│   ├── auth.php
│   ├── buku.php
│   ├── mahasiswa.php
│   ├── perpanjangan.php
│   └── wa.php                      ← kirim WA otomatis
├── proses/
│   ├── login.php
│   ├── registrasi-mandiri.php
│   ├── ajukan-perpanjangan.php
│   ├── petugas-accept-reject.php
│   ├── petugas-buat-akun.php
│   ├── kembalikan-buku.php
│   └── update-profil.php
├── view/
│   ├── mahasiswa/
│   │   ├── login.php
│   │   ├── register.php
│   │   ├── dashboard.php
│   │   ├── ajukan.php             ← ada tombol scan QR
│   │   ├── riwayat.php
│   │   └── profil.php
│   └── petugas/                   ← tanpa login (hanya dipakai di komputer perpus)
│       ├── index.php
│       ├── buat-akun.php
│       ├── daftar-ajuan.php       ← semua status pending
│       ├── riwayat.php
│       └── statistik.php
├── assets/
│   ├── css/style-utdi.css         ← Navi + Gold full custom
│   ├── img/logo-utdi.png
│   └── js/qr-scanner.min.js       ← untuk scan QR kode buku
├── libraries/
│   └── html5-qrcode.min.js        ← scanner QR ringan
├── struk/
│   └── generate-pdf.php           ← struk digital otomatis
└── index.php                      ← auto redirect: petugas kalau dari IP lokal, mahasiswa kalau dari luar
</pre>
