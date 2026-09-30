<?php
session_start();

require_once "../includes/auth.php";
require_once "../config/database.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = "ID produk tidak valid.";
    header("Location: produk.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| AMBIL DATA GAMBAR DENGAN PREPARED STATEMENT
|--------------------------------------------------------------------------
*/
$stmt_get = mysqli_prepare($conn, "SELECT gambar FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt_get, "i", $id);
mysqli_stmt_execute($stmt_get);
$result = mysqli_stmt_get_result($stmt_get);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt_get);

/*
|--------------------------------------------------------------------------
| HAPUS FILE FISIK GAMBAR JIKA ADA
|--------------------------------------------------------------------------
*/
if ($data && !empty($data['gambar'])) {
    $lokasi_file = "../uploads/produk/" . $data['gambar'];
    if (file_exists($lokasi_file)) {
        unlink($lokasi_file);
    }
}

/*
|--------------------------------------------------------------------------
| HAPUS DATA DARI DATABASE DENGAN PREPARED STATEMENT
|--------------------------------------------------------------------------
*/
$stmt_del = mysqli_prepare($conn, "DELETE FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt_del, "i", $id);

if (mysqli_stmt_execute($stmt_del)) {
    $_SESSION['success'] = "Produk berhasil dihapus.";
} else {
    $_SESSION['error'] = "Produk gagal dihapus.";
}

mysqli_stmt_close($stmt_del);

header("Location: produk.php");
exit;