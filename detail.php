<?php
require_once "config/database.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data produk menggunakan Prepared Statement
$stmt = mysqli_prepare($conn, "SELECT * FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result);

if (!$produk) {
    die("Produk tidak ditemukan.");
}

$pageTitle = $produk['nama'];
include "includes/header.php";
?>

<div class="container">
    <div class="card" style="margin-top: 20px;">
        <?php if (!empty($produk['gambar'])): ?>
            <img src="uploads/produk/<?= htmlspecialchars($produk['gambar']); ?>" 
                 alt="<?= htmlspecialchars($produk['nama']); ?>" 
                 class="product-image" 
                 style="max-width: 350px; border-radius: 8px; margin-bottom: 20px;">
        <?php endif; ?>

        <h1><?= htmlspecialchars($produk['nama']); ?></h1>

        <p style="color: #666; font-weight: 500;">
            Kategori: <span style="color: #333;"><?= htmlspecialchars($produk['kategori']); ?></span>
        </p>

        <p style="line-height: 1.6; margin: 15px 0;">
            <?= nl2br(htmlspecialchars($produk['deskripsi'])); ?>
        </p>

        <h2 style="color: #2c3e50; margin: 15px 0;">
            Rp <?= number_format($produk['harga'], 0, ',', '.'); ?>
        </h2>

        <p><strong>Stok Tersedia:</strong> <?= $produk['stok']; ?> pcs</p>

        <br>
        <a href="produk.php" class="btn" style="background-color: #6c757d;">&larr; Kembali ke Daftar Produk</a>
    </div>
</div>

<?php 
mysqli_stmt_close($stmt);
include "includes/footer.php"; 
?>