<?php
session_start();

require_once "../includes/auth.php";
require_once "../config/database.php";

$keyword  = trim($_GET['keyword'] ?? '');
$kategori = trim($_GET['kategori'] ?? '');

// Ambil daftar kategori dinamis dari database
$kategori_list = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

$where  = [];
$params = [];
$types  = "";

if ($keyword !== '') {
    $where[]  = "nama LIKE ?";
    $params[] = "%" . $keyword . "%";
    $types   .= "s";
}

if ($kategori !== '') {
    $where[]  = "kategori = ?";
    $params[] = $kategori;
    $types   .= "s";
}

$where_sql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/
$per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $per_page;

// Hitung total baris data (Prepared Statement)
$count_sql = "SELECT COUNT(*) AS total FROM produk $where_sql";
$stmt_count = mysqli_prepare($conn, $count_sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt_count, $types, ...$params);
}
mysqli_stmt_execute($stmt_count);
$count_res = mysqli_stmt_get_result($stmt_count);
$count_data = mysqli_fetch_assoc($count_res);
$total_data = $count_data['total'];
mysqli_stmt_close($stmt_count);

$total_page = ceil($total_data / $per_page);

// Ambil data halaman aktif (Prepared Statement)
$sql = "SELECT * FROM produk $where_sql ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);

$params_page = $params;
$params_page[] = $per_page;
$params_page[] = $offset;
$types_page = $types . "ii";

mysqli_stmt_bind_param($stmt, $types_page, ...$params_page);
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Produk - Sweet Cake</title>
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
    <h1>Kelola Produk</h1>

    <?php include "../includes/flash.php"; ?>

    <a href="tambah.php" class="btn">
        + Tambah Produk
    </a>

    <br><br>

    <!-- SEARCH & FILTER -->
    <div class="card">
        <form method="GET">
            <label>Cari Produk</label>
            <input
                type="text"
                name="keyword"
                placeholder="Masukkan nama produk"
                value="<?= htmlspecialchars($keyword); ?>"
            >

            <label>Kategori</label>
            <select name="kategori">
                <option value="">Semua Kategori</option>
                <?php while ($kat = mysqli_fetch_assoc($kategori_list)): ?>
                    <option value="<?= htmlspecialchars($kat['nama_kategori']); ?>"
                        <?= ($kategori === $kat['nama_kategori']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($kat['nama_kategori']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit" class="btn">
                Cari
            </button>

            <a href="produk.php" class="btn">
                Reset
            </a>
        </form>
    </div>

    <!-- TABEL PRODUK -->
    <div class="card">
        <table>
            <tr>
                <th>No</th>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>

            <?php
            $no = $offset + 1;
            while ($row = mysqli_fetch_assoc($query)):
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td>
                    <?php if ($row['gambar']): ?>
                        <img
                            src="../uploads/produk/<?= htmlspecialchars($row['gambar']); ?>"
                            width="80"
                        >
                    <?php else: ?>
                        Tidak ada
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['nama']); ?></td>
                <td><?= htmlspecialchars($row['kategori']); ?></td>
                <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                <td><?= $row['stok']; ?></td>
                <td>
                    <a
                        href="edit.php?id=<?= $row['id']; ?>"
                        class="btn btn-warning"
                    >
                        Edit
                    </a>
                    <a
                        href="hapus.php?id=<?= $row['id']; ?>"
                        class="btn btn-danger"
                        onclick="return confirm('Yakin ingin menghapus produk?')"
                    >
                        Hapus
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_page; $i++): ?>
            <a
                href="?page=<?= $i; ?>&keyword=<?= urlencode($keyword); ?>&kategori=<?= urlencode($kategori); ?>"
            >
                <?= $i; ?>
            </a>
        <?php endfor; ?>
    </div>
</div>

</body>
</html>
<?php
mysqli_stmt_close($stmt);
?>