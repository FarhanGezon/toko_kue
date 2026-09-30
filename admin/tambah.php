<?php
session_start();

require_once "../includes/auth.php";
require_once "../config/database.php";

// Ambil data kategori dari tabel kategori
$kategori_query = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Produk - Sweet Cake</title>
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
    <div class="card">
        <h1>Tambah Produk</h1>

        <?php include "../includes/flash.php"; ?>

        <form action="proses_produk.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="aksi" value="tambah">

            <label>Nama Produk</label>
            <input type="text" name="nama" placeholder="Contoh: Brownies Kukus" required>

            <label>Kategori</label>
            <select name="kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <?php while ($kat = mysqli_fetch_assoc($kategori_query)): ?>
                    <option value="<?= htmlspecialchars($kat['nama_kategori']); ?>">
                        <?= htmlspecialchars($kat['nama_kategori']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Deskripsi (Maksimal 255 Karakter)</label>
            <textarea name="deskripsi" rows="4" maxlength="255" placeholder="Tuliskan deskripsi singkat produk..." required></textarea>

            <label>Harga (Rp)</label>
            <input type="number" name="harga" min="0" placeholder="Contoh: 35000" required>

            <label>Stok</label>
            <input type="number" name="stok" min="0" placeholder="Contoh: 20" required>

            <label>Gambar Produk (Opsional, Maks 2MB: JPG, PNG, WEBP)</label>
            <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp">
            <br>

            <button type="submit" class="btn">Simpan Produk</button>
            <a href="produk.php" class="btn" style="background-color: #6c757d;">Kembali</a>
        </form>
    </div>
</div>

</body>
</html>