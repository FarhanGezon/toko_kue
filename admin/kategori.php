<?php
session_start();

require_once "../includes/auth.php";
require_once "../config/database.php";

// Logika Tambah Kategori
if (isset($_POST['tambah_kategori'])) {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');

    if ($nama_kategori === '') {
        $_SESSION['error'] = "Nama kategori tidak boleh kosong.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO kategori (nama_kategori) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $nama_kategori);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Kategori berhasil ditambahkan.";
        } else {
            $_SESSION['error'] = "Gagal menambahkan kategori.";
        }
        mysqli_stmt_close($stmt);
    }
    header("Location: kategori.php");
    exit;
}

// Logika Hapus Kategori
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = mysqli_prepare($conn, "DELETE FROM kategori WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Kategori berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Gagal menghapus kategori.";
    }
    mysqli_stmt_close($stmt);
    header("Location: kategori.php");
    exit;
}

// Ambil semua kategori
$query = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Kategori - Sweet Cake</title>
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
    <h1>Kelola Kategori</h1>

    <?php include "../includes/flash.php"; ?>

    <!-- Form Tambah Kategori -->
    <div class="card" style="margin-bottom: 25px;">
        <h3>Tambah Kategori Baru</h3>
        <form method="POST" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="nama_kategori" placeholder="Nama kategori baru..." required style="margin-bottom: 0;">
            <button type="submit" name="tambah_kategori" class="btn" style="white-space: nowrap;">Simpan Kategori</button>
        </form>
    </div>

    <!-- Tabel Daftar Kategori -->
    <div class="card">
        <table>
            <tr>
                <th style="width: 10%;">No</th>
                <th>Nama Kategori</th>
                <th style="width: 20%;">Aksi</th>
            </tr>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($query)): 
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nama_kategori']); ?></td>
                <td>
                    <a href="kategori.php?hapus=<?= $row['id']; ?>" 
                       class="btn btn-danger" 
                       onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                       Hapus
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

</body>
</html>