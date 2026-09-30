<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . " - Sweet Cake" : "Sweet Cake - Toko Kue" ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="navbar">
    <div class="container">
        <a href="index.php">Sweet Cake</a>
        <a href="produk.php">Produk</a>
        <a href="tentang.php">Tentang Kami</a>
    </div>
</div>