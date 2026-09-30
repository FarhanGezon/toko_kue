<?php
session_start();

require_once "../includes/auth.php";
require_once "../config/database.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data produk menggunakan Prepared Statement
$stmt = mysqli_prepare($conn, "SELECT * FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$produk) {
    $_SESSION['error'] = "Produk tidak ditemukan.";
    header("Location: produk.php");
    exit;
}

// Ambil semua kategori dari database
$kategori_query = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Produk - Sweet Cake</title>
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
        <h1>Edit Produk</h1>

        <?php include "../includes/flash.php"; ?>

        <form action="proses_produk.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="aksi" value="edit">
            <input type="hidden" name="id" value="<?= $produk['id']; ?>">

            <label>Nama Produk</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($produk['nama']); ?>" required>

            <label>Kategori</label>
            <select name="kategori" required>
                <?php while ($kat = mysqli_fetch_assoc($kategori_query)): ?>
                    <option value="<?= htmlspecialchars($kat['nama_kategori']); ?>"
                        <?= ($produk['kategori'] === $kat['nama_kategori']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($kat['nama_kategori']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Deskripsi (Maksimal 255 Karakter)</label>
            <textarea name="deskripsi" rows="4" maxlength="255" required><?= htmlspecialchars($produk['deskripsi']); ?></textarea>

            <label>Harga (Rp)</label>
            <input type="number" name="harga" min="0" value="<?= $produk['harga']; ?>" required>

            <label>Stok</label>
            <input type="number" name="stok" min="0" value="<?= $produk['stok']; ?>" required>

            <label>Ganti Gambar (Biarkan kosong jika tidak ingin diubah)</label>
            <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp">

            <?php if (!empty($produk['gambar'])): ?>
                <p style="margin-top: 10px; font-size: 0.9em; color: #555;">Gambar saat ini:</p>
                <img src="../uploads/produk/<?= htmlspecialchars($produk['gambar']); ?>" width="120" style="border-radius: 6px;">
            <?php endif; ?>

            <br><br>
            <button type="submit" class="btn">Perbarui Data</button>
            <a href="produk.php" class="btn" style="background-color: #6c757d;">Batal</a>
        </form>
    </div>
</div>

</body>
</html>