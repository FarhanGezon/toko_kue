<?php
require_once "config/database.php";

$pageTitle = "Beranda";
include "includes/header.php";

// Ambil 6 produk terbaru
$query = mysqli_query(
    $conn,
    "SELECT * FROM produk ORDER BY id DESC LIMIT 6"
);
?>

<section class="hero">
    <div class="container">
        <h1>Sweet Cake</h1>
        <p>Kue lezat untuk setiap momen spesial.</p>
        <a href="produk.php" class="btn">Lihat Semua Produk</a>
    </div>
</section>

<div class="container">
    <h2>Produk Terbaru</h2>

    <div class="grid">
        <?php if (mysqli_num_rows($query) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                <div class="card">
                    <?php if (!empty($row['gambar'])): ?>
                        <img src="uploads/produk/<?= htmlspecialchars($row['gambar']); ?>" 
                             alt="<?= htmlspecialchars($row['nama']); ?>" 
                             class="product-image">
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($row['nama']); ?></h3>
                    <p style="color: #666; font-size: 0.9em;"><?= htmlspecialchars($row['kategori']); ?></p>
                    
                    <strong>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></strong>
                    <br><br>

                    <a href="detail.php?id=<?= $row['id']; ?>" class="btn">Detail</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="grid-column: 1 / -1; text-align: center; color: #888;">Belum ada produk yang tersedia.</p>
        <?php endif; ?>
    </div>
</div>

<?php include "includes/footer.php"; ?>