<?php
session_start();

require_once "../includes/auth.php";
require_once "../config/database.php";

$aksi = $_POST['aksi'] ?? '';

/*
|--------------------------------------------------------------------------
| TAMBAH PRODUK
|--------------------------------------------------------------------------
*/

if ($aksi == 'tambah') {

    $nama = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = $_POST['harga'] ?? '';
    $stok = $_POST['stok'] ?? '';

    // 1. Validasi Kolom Kosong
    if ($nama === '' || $kategori === '' || $deskripsi === '' || $harga === '' || $stok === '') {
        $_SESSION['error'] = "Semua data wajib diisi.";
        header("Location: tambah.php");
        exit;
    }

    // 2. Validasi Panjang Karakter Deskripsi (Sesuai Modul M6)
    if (strlen($deskripsi) > 255) {
        $_SESSION['error'] = "Deskripsi maksimal 255 karakter.";
        header("Location: tambah.php");
        exit;
    }

    // 3. Validasi Angka
    if (!is_numeric($harga) || !is_numeric($stok)) {
        $_SESSION['error'] = "Harga dan stok harus berupa angka.";
        header("Location: tambah.php");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | UPLOAD GAMBAR
    |--------------------------------------------------------------------------
    */
    $nama_file = null;

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $file = $_FILES['gambar'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowed)) {
            $_SESSION['error'] = "Format gambar tidak diperbolehkan.";
            header("Location: tambah.php");
            exit;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            $_SESSION['error'] = "Ukuran gambar maksimal 2 MB.";
            header("Location: tambah.php");
            exit;
        }

        $nama_file = uniqid() . "." . $extension;
        move_uploaded_file($file['tmp_name'], "../uploads/produk/" . $nama_file);
    }

    /*
    |--------------------------------------------------------------------------
    | INSERT DENGAN PREPARED STATEMENT (Aman dari SQL Injection)
    |--------------------------------------------------------------------------
    */
    $sql = "INSERT INTO produk (nama, kategori, deskripsi, harga, stok, gambar) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    // s = string, d = double/desimal, i = integer
    mysqli_stmt_bind_param($stmt, "sssdis", $nama, $kategori, $deskripsi, $harga, $stok, $nama_file);
    $eksekusi = mysqli_stmt_execute($stmt);

    if ($eksekusi) {
        $_SESSION['success'] = "Produk berhasil ditambahkan.";
    } else {
        $_SESSION['error'] = "Produk gagal ditambahkan.";
    }

    mysqli_stmt_close($stmt);
    header("Location: produk.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| EDIT PRODUK
|--------------------------------------------------------------------------
*/

if ($aksi == 'edit') {

    $id = (int)($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = $_POST['harga'] ?? '';
    $stok = $_POST['stok'] ?? '';

    // 1. Validasi Kolom Kosong
    if ($id <= 0 || $nama === '' || $kategori === '' || $deskripsi === '' || $harga === '' || $stok === '') {
        $_SESSION['error'] = "Semua data wajib diisi.";
        header("Location: edit.php?id=$id");
        exit;
    }

    // 2. Validasi Panjang Karakter Deskripsi (Sesuai Modul M6)
    if (strlen($deskripsi) > 255) {
        $_SESSION['error'] = "Deskripsi maksimal 255 karakter.";
        header("Location: edit.php?id=$id");
        exit;
    }

    // 3. Validasi Angka
    if (!is_numeric($harga) || !is_numeric($stok)) {
        $_SESSION['error'] = "Harga dan stok harus berupa angka.";
        header("Location: edit.php?id=$id");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA GAMBAR LAMA
    |--------------------------------------------------------------------------
    */
    $old_stmt = mysqli_prepare($conn, "SELECT gambar FROM produk WHERE id = ?");
    mysqli_stmt_bind_param($old_stmt, "i", $id);
    mysqli_stmt_execute($old_stmt);
    $result = mysqli_stmt_get_result($old_stmt);
    $old_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($old_stmt);

    $nama_file = $old_data['gambar'] ?? null;

    /*
    |--------------------------------------------------------------------------
    | UPLOAD GAMBAR BARU JIKA ADA
    |--------------------------------------------------------------------------
    */
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $file = $_FILES['gambar'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowed)) {
            $_SESSION['error'] = "Format gambar tidak diperbolehkan.";
            header("Location: edit.php?id=$id");
            exit;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            $_SESSION['error'] = "Ukuran gambar maksimal 2 MB.";
            header("Location: edit.php?id=$id");
            exit;
        }

        $nama_file_baru = uniqid() . "." . $extension;

        if (move_uploaded_file($file['tmp_name'], "../uploads/produk/" . $nama_file_baru)) {
            // Hapus file gambar lama jika ada fisiknya di folder
            if (!empty($old_data['gambar']) && file_exists("../uploads/produk/" . $old_data['gambar'])) {
                unlink("../uploads/produk/" . $old_data['gambar']);
            }
            $nama_file = $nama_file_baru;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE DENGAN PREPARED STATEMENT
    |--------------------------------------------------------------------------
    */
    $sql = "UPDATE produk SET nama = ?, kategori = ?, deskripsi = ?, harga = ?, stok = ?, gambar = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "sssdisi", $nama, $kategori, $deskripsi, $harga, $stok, $nama_file, $id);
    $eksekusi = mysqli_stmt_execute($stmt);

    if ($eksekusi) {
        $_SESSION['success'] = "Produk berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Produk gagal diperbarui.";
    }

    mysqli_stmt_close($stmt);
    header("Location: produk.php");
    exit;
}