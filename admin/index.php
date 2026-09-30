<?php
session_start();

require_once "../includes/auth.php";
require_once "../config/database.php";

// Total Produk
$total_produk_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk");
$data_produk = mysqli_fetch_assoc($total_produk_res);

// Total Kategori dari tabel kategori
$total_kategori_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM kategori");
$data_kategori = mysqli_fetch_assoc($total_kategori_res);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin - Sweet Cake</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="navbar">
    <div class="container">
        <a href="index.php">Dashboard</a>
        <a href="produk.php">Produk</a>
        <a href="kategori.php">Kategori</a>
        <a href="../index.php">Website</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h1>Dashboard Admin</h1>
    <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin'); ?></strong>!</p>

    <div class="grid">
        <div class="card">
            <h3>Total Produk</h3>
            <h1><?= $data_produk['total'] ?? 0; ?></h1>
        </div>

        <div class="card">
            <h3>Total Kategori</h3>
            <h1><?= $data_kategori['total'] ?? 0; ?></h1>
        </div>

        <div class="card">
            <h3>Status Server</h3>
            <h1 style="color: #28a745;">Aktif</h1>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h2>Manajemen Katalog</h2>
        <p>Gunakan menu berikut untuk mengelola data katalog Sweet Cake secara dinamis.</p>
        <div style="display: flex; gap: 10px; margin-top: 15px;">
            <a href="produk.php" class="btn">Kelola Produk</a>
            <a href="kategori.php" class="btn" style="background-color: #17a2b8;">Kelola Kategori</a>
        </div>
    </div>
</div>

</body>
</html>