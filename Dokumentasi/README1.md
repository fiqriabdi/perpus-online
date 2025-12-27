## 1️⃣ ERD (Login NIM & Email Organisasi)  

Prinsip Desain (biar konsisten & defensible)  
- Mahasiswa login pakai NIM
- Petugas login pakai email @utdi.ac.id
- Auth ≠ Identitas
- Offline tetap ditangani

## 📘 Entitas & Relasi  

### users — AUTENTIKASI

<pre>
users
------
id (PK)
login_identifier     -- NIM (mahasiswa) / email UTDI (petugas)
password_hash
role                 -- mahasiswa | petugas
is_active
created_at
</pre>

### mahasiswa — IDENTITAS MAHASISWA

<pre>
  mahasiswa
---------
id (PK)
user_id (FK, nullable)
nim
nama
email (nullable)
whatsapp (nullable)
created_at
</pre>

> 📌 Rule:  
> email OR whatsapp wajib salah satu  
> Offline → user_id = NULL

### petugas — IDENTITAS PETUGAS

<pre>
  petugas
-------
id (PK)
user_id (FK)
nama
email_organisasi
created_at
</pre>

### perpanjangan — DATA INTI  

<pre>
  perpanjangan
------------
id (PK)
mahasiswa_id (FK)
kode_buku
judul_buku
tanggal_perpanjangan
tanggal_kembali
tanggal_dikembalikan
status   -- pending | accepted | rejected | offline_extended
denda
created_at
</pre>

## ERD Diagram (Mermaid)

```mermaid
erDiagram

    USERS {
        int id PK
        string login_identifier
        string password_hash
        string role
        boolean is_active
        datetime created_at
    }

    MAHASISWA {
        int id PK
        int user_id FK
        string nim
        string nama
        string email
        string whatsapp
        datetime created_at
    }

    PETUGAS {
        int id PK
        int user_id FK
        string nama
        string email_organisasi
        datetime created_at
    }

    PERPANJANGAN {
        int id PK
        int mahasiswa_id FK
        string kode_buku
        string judul_buku
        date tanggal_perpanjangan
        date tanggal_kembali
        date tanggal_dikembalikan
        string status
        int denda
        datetime created_at
    }

    USERS ||--o| MAHASISWA : autentikasi
    USERS ||--|| PETUGAS : autentikasi
    MAHASISWA ||--o{ PERPANJANGAN : mengajukan
```

## 🔁 Flowchart PERPANJANGAN ONLINE

```mermaid
flowchart TD
    A[Mahasiswa Login] --> B[Pilih Menu Perpanjangan]
    B --> C[Input Data Buku]
    C --> D[Submit Request]
    D --> E[Status = Pending]
    E --> F[Petugas Review]
    F --> G{Keputusan?}
    G -- Accept --> H[Update Status = Accepted]
    G -- Reject --> I[Update Status = Rejected]
    H --> J[Kirim Notifikasi]
    I --> J
```

## 🧾 Flowchart PERPANJANGAN OFFLINE

```mermaid
flowchart TD
    A[Mahasiswa Datang] --> B[Petugas Input Data]
    B --> C{Data ada?}
    C -- Ya --> D[Update Data]
    C -- Tidak --> E[Tambah Data]
    D --> F[Status = Offline_Extended]
    E --> F
```
