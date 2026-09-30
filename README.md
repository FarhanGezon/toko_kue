# Sweet Cake - UMKM Company Profile & Katalog Produk

Aplikasi berbasis web untuk profil usaha dan katalog produk kue (UMKM Sweet Cake) yang dibangun menggunakan **Native PHP** dan **MySQL**. Proyek ini mengimplementasikan alur **CRUD Lengkap** (Create, Read, Update, Delete) dengan proteksi data, modularisasi layout, dan panel administrasi dinamis[cite: 5].

---

## Ringkasan Pembaruan (Changelog / Release Notes)

### Versi 1.1.0 - Penyempurnaan Keamanan & Fitur Kategori Dinamis
* **Fitur Baru (CRUD Kategori):**
  * Penambahan skema tabel `kategori` pada database.
  * Halaman manajemen kategori (`admin/kategori.php`) untuk menambah dan menghapus kategori kue secara dinamis.
  * Dropdown form tambah/edit produk kini terintegrasi secara dinamis dengan tabel kategori.
* **Keamanan Database (Prepared Statements):**
  * Seluruh manipulasi data (Create, Update, Delete) dan query pencarian/filter diubah menggunakan **Prepared Statements** (`mysqli_prepare` & `bind_param`) untuk memitigasi risiko SQL Injection.
* **Validasi Sisi Server (Server-Side Validation):**
  * Penambahan validasi pembatasan panjang karakter deskripsi (`strlen() <= 255`).
  * Validasi tipe data numerik untuk harga dan stok produk.
  * Pembatasan berkas unggahan gambar (hanya ekstensi `.jpg`, `.jpeg`, `.png`, `.webp` dengan batas ukuran maksimal 2 MB).
* **Modularisasi Layout (DRY - Don't Repeat Yourself):**
  * Implementasi komponen terpusat pada `includes/header.php` dan `includes/footer.php` untuk seluruh halaman publik (`index.php`, `produk.php`, `detail.php`, `tentang.php`).
* **Portabilitas Database:**
  * Pembaruan skrip `toko_kue.sql` dengan menyertakan instruksi `CREATE DATABASE IF NOT EXISTS` dan `USE toko_kue;` untuk kemudahan instalasi.

---

## Fitur Utama

### Sisi Publik (Katalog Pelanggan)
* **Beranda:** Menampilkan hero banner pengenalan dan 6 daftar produk terbaru.
* **Daftar Produk:** Katalog lengkap dengan fitur pencarian nama produk dan filter kategori dinamis.
* **Detail Produk:** Informasi spesifik produk (foto beresolusi jelas, kategori, harga, stok, dan deskripsi).
* **Tentang Kami:** Profil singkat perusahaan dan kontak bisnis.

### Sisi Administrator (Dashboard & CRUD)
* **Autentikasi Aman:** Sistem login admin menggunakan enkripsi `password_hash()` dan verifikasi `password_verify()` dengan proteksi session `auth.php`.
* **Statistik Dashboard:** Ringkasan jumlah total produk dan total kategori secara *real-time*.
* **CRUD Produk:** Menambah produk baru beserta upload berkas gambar, memperbarui data, menghapus produk sekaligus menghapus file fisik gambar via `unlink()`.
* **CRUD Kategori:** Mengelola kategori kue secara independen.
* **Paginasi (Pagination):** Pembatasan data 5 baris per halaman menggunakan klausa SQL `LIMIT` dan `OFFSET`.
* **Flash Messages:** Notifikasi umpan balik sukses/gagal berbasis session dengan pola *Post/Redirect/Get*.

---

## Struktur Direktori

```text
toko_kue/
├── admin/                 # Panel kelola produk & kategori
│   ├── edit.php           # Form edit produk
│   ├── hapus.php          # Handler hapus produk & gambar fisik
│   ├── index.php          # Dashboard statistik admin
│   ├── kategori.php       # Modul CRUD kategori dinamis
│   ├── logout.php         # Handler destroy session
│   ├── produk.php         # Daftar produk, pencarian & pagination
│   ├── proses_produk.php  # Controller Create & Update (Prepared Statements)
│   └── tambah.php         # Form tambah produk
├── assets/                # Aset statis pendukung
│   └── style.css          # Berkas styling CSS
├── auth/                  # Sistem autentikasi admin
│   ├── buat_admin.php     # Seeder pembuatan akun admin pertama
│   ├── login.php          # Form login admin
│   └── proses_login.php   # Verifikasi kredensial admin
├── config/
│   └── database.php       # Konfigurasi koneksi MySQLi
├── includes/              # Komponen modular reusable
│   ├── auth.php           # Middleware proteksi halaman admin
│   ├── flash.php          # Komponen tampilan notifikasi session
│   ├── footer.php         # Layout footer publik
│   └── header.php         # Layout header & navigasi publik
├── uploads/               # Direktori penyimpanan berkas gambar
│   └── produk/            # File foto produk
├── detail.php             # Halaman detail produk publik
├── index.php              # Halaman utama / katalog terbaru
├── produk.php             # Halaman katalog seluruh produk
├── tentang.php            # Halaman profil toko kue
├── toko_kue.sql           # Skema & data awal database MySQL
└── README.md              # Dokumentasi proyek
