# Narasi Dokumentasi Project

## **Aplikasi Web Perpanjangan Peminjaman Buku**

Aplikasi Web Perpanjangan Peminjaman Buku dirancang khusus untuk **mengelola proses perpanjangan peminjaman buku**, baik secara **offline** maupun **online**, dengan **validasi manual oleh petugas**.
Aplikasi ini **tidak terhubung langsung** dengan server perpustakaan kampus dan berdiri sebagai sistem mandiri.

> *Aplikasi web ini dapat dikatakan sebagai wadah kosong yang menampung data baru, di mana data akan diperbarui jika sudah ada, atau ditambahkan jika belum ada, dengan validasi manual oleh petugas.*

---

## **Perpanjangan Peminjaman Offline**

**Catatan:**
Perpanjangan offline **tidak mewajibkan mahasiswa memiliki akun**.

### Alur:

1. Mahasiswa datang membawa buku pinjaman.
2. Mahasiswa melapor kepada petugas.
3. Petugas melakukan input data ke sistem, meliputi:

   * NIM
   * Nama
   * Jurusan
   * Kode Buku

**Catatan penting:**
Pada perpanjangan offline, **hanya kode buku yang diinput**.
Hal ini dilakukan untuk menghindari ketidaksesuaian data (judul, penulis, dan metadata lain) dengan database perpustakaan kampus, mengingat aplikasi ini **tidak terintegrasi** dengan sistem perpustakaan kampus.

4. Sistem melakukan pengecekan:

   * Jika data sudah ada → data diperbarui
   * Jika data belum ada → data ditambahkan

---

## **Registrasi Akun Pengguna**

**Catatan:**
Perpanjangan peminjaman secara online **mewajibkan mahasiswa memiliki akun**.

Jika mahasiswa belum memiliki akun:

1. Mahasiswa melapor kepada petugas.
2. Registrasi akun dilakukan oleh petugas dengan menginput:

   * NIM (sebagai username)
   * Nama
   * Jurusan
3. Password:

   * Tidak diinput manual oleh petugas
   * Digenerate otomatis oleh sistem
   * Karakter:

     ```
     0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ?!
     ```
   * Panjang: **9 karakter**
4. Sistem mencetak bukti registrasi yang berisi:

   * Tanggal registrasi
   * Username
   * Password
   * Link login aplikasi

---

## **Perpanjangan Peminjaman Online**

**Catatan:**
Perpanjangan online hanya dapat dilakukan oleh mahasiswa yang telah memiliki akun.

### Alur:

1. Mahasiswa melakukan login ke sistem.
2. Mahasiswa memilih menu perpanjangan peminjaman.
3. Mahasiswa menginput data buku dan mengirim permohonan.
4. Sistem mengirim notifikasi ke petugas.
5. Petugas melakukan validasi manual dengan keputusan:

   * **Accept (diterima)**
   * **Reject (ditolak)**
6. Mahasiswa menerima notifikasi hasil keputusan.

---

## **Pengembalian Setelah Perpanjangan & Denda**

Aplikasi ini juga mencatat **pengembalian buku setelah perpanjangan**, bukan pengembalian peminjaman awal.

### Pencatatan Waktu:

* `tanggal_perpanjangan`
  → tanggal buku diperpanjang (ditetapkan otomatis oleh sistem)
* `tanggal_kembali`
  → tanggal seharusnya buku dikembalikan
  (default: `tanggal_perpanjangan + 1 hari`)
* `tanggal_dikembalikan`
  → tanggal aktual mahasiswa mengembalikan buku

### Perhitungan Denda:

Jika:

```
tanggal_dikembalikan > tanggal_kembali
```

maka:

```
denda = 500 rupiah
```

**Catatan:**
Pengaturan waktu dapat dilakukan secara otomatis, semi otomatis, atau manual.
Namun, **best practice yang digunakan adalah otomatis**, untuk meminimalkan human error.

---

## **Fokus Sistem**

Aplikasi ini berfokus pada:

* Perpanjangan peminjaman buku
* Pengembalian setelah perpanjangan
* Denda keterlambatan pengembalian setelah perpanjangan

---

## **Yang Tidak Ditangani Sistem**

Aplikasi **tidak menangani**:

* Peminjaman buku
* Pengembalian peminjaman awal
* Denda pengembalian peminjaman awal
* Validasi otomatis yang melibatkan server perpustakaan kampus

---

## **Ruang Lingkup Aplikasi**

Aplikasi web ini hanya mengelola:

* Perpanjangan peminjaman buku
* Pengembalian setelah perpanjangan
* Denda keterlambatan pengembalian setelah perpanjangan

Seluruh proses validasi dilakukan **secara manual oleh petugas**, berdasarkan permohonan dari mahasiswa.

Aplikasi ini bersifat **konsisten dalam ruang lingkup**, tidak mencampuradukkan fungsi, dan berjalan sesuai lintasan yang telah ditentukan.

---

## **Penegasan Akhir**

Sekali lagi, aplikasi web ini merupakan **wadah kosong** yang berfungsi untuk:

* Menampung data perpanjangan
* Memperbarui data jika sudah ada
* Menambahkan data jika belum ada

Seluruh proses dilakukan dengan **validasi manual oleh petugas**, tanpa keterlibatan langsung sistem perpustakaan kampus.

---
