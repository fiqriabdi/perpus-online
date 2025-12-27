## Fitur Perpanjangan

### Deskripsi Offline
#### Perpanjangan  
  1. Mahasiswa datang dengan membawa buku pinjaman
  2. Mahasiswa melapor ke petugas
  2. Petugas melakukan input data ke sistem
  3. Sistem mengecek:
     - Jika data ada, update status
     - Jika data belum ada, tambah data baru

#### Registrasi akun oleh petugas  
   1. Petugas input nim dan kontak mahasiswa
   2. Pesan berupa username dan password dikirim ke kontak mahasiswa
   3. Mahasiswa dapat login ke akun menggunakan username dan password yang diterima.
   4. Mahasiswa dapat menambahkan kontak dan update password.

### Deskripsi Online
  1. Mahasiswa melakukan login ke sistem
  2. Mahasiswa memilih menu perpanjangan
  3. Mahasiswa melakukan input data buku dan submit
  4. Sistem menerima notifikasi
  5. Petugas mengecek dan melakukan aksi:
     - Terima (accept)
     - Tolak (reject)
  6. Mahasiswa menerima notifikasi

<br>

```mermaid
graph TD
    Mahasiswa["Mahasiswa"]
    Petugas["Petugas"]
    UC1["Login"]
    UC2["Ajukan Perpanjangan"]
    UC3["Terima Notifikasi"]
    UC4["Update Profil"]
    UC5["Registrasi Mahasiswa"]
    UC6["Input Perpanjangan Offline"]
    UC7["Validasi Perpanjangan"]
    UC8["Input Tanggal Pengembalian"]
    UC9["Hitung Denda"]
    UC10["Kirim Notifikasi"]
    
    %% Hubungan Mahasiswa
    Mahasiswa --> UC1
    Mahasiswa --> UC2
    Mahasiswa --> UC3
    Mahasiswa --> UC4
    
    %% Hubungan Petugas
    Petugas --> UC1
    Petugas --> UC5
    Petugas --> UC6
    Petugas --> UC7
    Petugas --> UC8
    Petugas --> UC9
    Petugas --> UC10
    
    %% Relasi antar use case
    UC7 --> UC10
    UC8 --> UC9
    UC2 --> UC7
```

</br>

### DAD  
<img src="img/dad_refisi.png" width="450">


---   
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
