<?php
require_once "config/database.php";

$pageTitle = "Daftar Produk";
include "includes/header.php";

$keyword  = trim($_GET['keyword'] ?? '');
$kategori = trim($_GET['kategori'] ?? '');

// Ambil daftar kategori untuk dropdown filter
$kategori_query = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

// Susun Query Dasar
$sql = "SELECT * FROM produk WHERE 1=1";
$params = [];
$types  = "";

// Filter Pencarian Nama
if ($keyword !== '') {
    $sql .= " AND nama LIKE ?";
    $params[] = "%" . $keyword . "%";
    $types   .= "s";
}

// Filter Berdasarkan Kategori
if ($kategori !== '') {
    $sql .= " AND kategori = ?";
    $params[] = $kategori;
    $types   .= "s";
}

$sql .= " ORDER BY id DESC";

// Eksekusi Prepared Statement
$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);
?>

<div class="container">
    <h1>Daftar Produk</h1>

    <!-- FORM PENCARIAN & FILTER KATEGORI -->
    <div class="card" style="margin-bottom: 25px;">
        <form method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <input type="text" 
                   name="keyword" 
                   placeholder="Cari produk..." 
                   value="<?= htmlspecialchars($keyword); ?>"
                   style="flex: 1; min-width: 200px; margin-bottom: 0;">

            <select name="kategori" style="width: 180px; margin-bottom: 0;">
                <option value="">Semua Kategori</option>
                <?php while ($kat = mysqli_fetch_assoc($kategori_query)): ?>
                    <option value="<?= htmlspecialchars($kat['nama_kategori']); ?>"
                        <?= ($kategori === $kat['nama_kategori']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($kat['nama_kategori']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit" class="btn" style="margin-bottom: 0;">Cari</button>
            <a href="produk.php" class="btn" style="margin-bottom: 0; background-color: #6c757d;">Reset</a>
        </form>
    </div>

    <!-- DAFTAR GRID PRODUK -->
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
                    <p style="color: #666; font-size: 0.9em;">Kategori: <?= htmlspecialchars($row['kategori']); ?></p>
                    <p><?= htmlspecialchars($row['deskripsi']); ?></p>

                    <h3>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></h3>

                    <a href="detail.php?id=<?= $row['id']; ?>" class="btn">Lihat Detail</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="grid-column: 1 / -1; text-align: center; color: #888;">
                Produk tidak ditemukan. Silakan gunakan kata kunci lain.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php 
mysqli_stmt_close($stmt);
include "includes/footer.php"; 
?>