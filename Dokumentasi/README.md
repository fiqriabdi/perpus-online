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

### Informasi table
<pre>
  MariaDB [perpus]> show tables;
+------------------+
| Tables_in_perpus |
+------------------+
| buku             |
| mahasiswa        |
| perpanjangan     |
+------------------+
3 rows in set (0.001 sec)

MariaDB [perpus]> describe buku;
+-------+--------------+------+-----+---------+----------------+
| Field | Type         | Null | Key | Default | Extra          |
+-------+--------------+------+-----+---------+----------------+
| id    | int(11)      | NO   | PRI | NULL    | auto_increment |
| kode  | char(5)      | NO   | UNI | NULL    |                |
| judul | varchar(100) | NO   |     | NULL    |                |
+-------+--------------+------+-----+---------+----------------+
3 rows in set (0.020 sec)

MariaDB [perpus]> describe mahasiswa;
+-------------------+--------------------------+------+-----+---------+----------------+
| Field             | Type                     | Null | Key | Default | Extra          |
+-------------------+--------------------------+------+-----+---------+----------------+
| id                | int(11)                  | NO   | PRI | NULL    | auto_increment |
| nim               | char(10)                 | NO   | UNI | NULL    |                |
| nama              | varchar(255)             | NO   |     | NULL    |                |
| prodi             | varchar(50)              | NO   |     | NULL    |                |
| password          | varchar(255)             | YES  |     | NULL    |                |
| wa                | varchar(20)              | YES  |     | NULL    |                |
| email             | varchar(100)             | YES  |     | NULL    |                |
| status            | enum('aktif','nonaktif') | YES  |     | aktif   |                |
| registered_by     | enum('self','petugas')   | YES  |     | NULL    |                |
| email_verified_at | datetime                 | YES  |     | NULL    |                |
| wa_verified_at    | datetime                 | YES  |     | NULL    |                |
+-------------------+--------------------------+------+-----+---------+----------------+
11 rows in set (0.017 sec)

MariaDB [perpus]> describe perpanjangan;
+----------------------+---------------------------------------+------+-----+---------------------+----------------+
| Field                | Type                                  | Null | Key | Default             | Extra          |
+----------------------+---------------------------------------+------+-----+---------------------+----------------+
| id                   | int(11)                               | NO   | PRI | NULL                | auto_increment |
| mahasiswa_id         | int(11)                               | NO   | MUL | NULL                |                |
| buku_id              | int(11)                               | NO   | MUL | NULL                |                |
| tanggal_perpanjang   | date                                  | NO   | MUL | curdate()           |                |
| tanggal_kembali      | date                                  | NO   |     | NULL                |                |
| tanggal_dikembalikan | date                                  | YES  |     | NULL                |                |
| denda                | int(11)                               | YES  |     | 0                   |                |
| cara                 | enum('offline','online')              | YES  |     | offline             |                |
| status               | enum('pending','accepted','rejected') | YES  |     | pending             |                |
| keterangan           | varchar(150)                          | YES  |     | NULL                |                |
| created_at           | datetime                              | YES  |     | current_timestamp() |                |
+----------------------+---------------------------------------+------+-----+---------------------+----------------+
11 rows in set (0.017 sec)

MariaDB [perpus]>
</pre>

  
**DELIMITER**
```sql
-- Trigger cerdas: +1 hari otomatis + hitung denda 500/hari
DELIMITER $$
CREATE TRIGGER trg_perpanjangan_accepted 
BEFORE UPDATE ON perpanjangan
FOR EACH ROW
BEGIN
    -- Saat status jadi 'accepted' → set tanggal_kembali = tanggal_perpanjang + 1 hari
    IF NEW.status = 'accepted' AND OLD.status != 'accepted' THEN
        SET NEW.tanggal_kembali = DATE_ADD(NEW.tanggal_perpanjang, INTERVAL 1 DAY);
    END IF;

    -- Saat buku dikembalikan → hitung denda otomatis
    IF NEW.tanggal_dikembalikan IS NOT NULL AND (OLD.tanggal_dikembalikan IS NULL OR OLD.tanggal_dikembalikan != NEW.tanggal_dikembalikan) THEN
        IF NEW.tanggal_dikembalikan > NEW.tanggal_kembali THEN
            SET NEW.denda = DATEDIFF(NEW.tanggal_dikembalikan, NEW.tanggal_kembali) * 500;
        ELSE
            SET NEW.denda = 0;
        END IF;
    END IF;
END$$ 
DELIMITER ;
```
