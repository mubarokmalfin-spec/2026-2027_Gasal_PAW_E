<?php
$level = levelUser();
$flash = ambilFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($judul ?? 'Sistem Penjualan') ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <nav class="navbar no-print">
        <div class="container navbar-isi">
            <a class="brand" href="index.php">Sistem Penjualan</a>

            <ul class="menu">
                <li><a href="index.php">Home</a></li>

                <?php if ($level === 1): ?>
                    <li class="dropdown">
                        <span class="dropdown-toggle" tabindex="0">Data Master &#9662;</span>
                        <ul class="dropdown-menu">
                            <li><a href="barang.php">Data Barang</a></li>
                            <li><a href="supplier.php">Data Supplier</a></li>
                            <li><a href="pelanggan.php">Data Pelanggan</a></li>
                            <li><a href="user.php">Data User</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <li><a href="transaksi.php">Transaksi</a></li>
                <li><a href="laporan.php">Laporan</a></li>
            </ul>

            <ul class="menu menu-kanan">
                <li class="dropdown">
                    <span class="dropdown-toggle" tabindex="0"><?= e($_SESSION['nama']) ?> &#9662;</span>
                    <ul class="dropdown-menu dropdown-kanan">
                        <li>
                            <form method="post" action="logout.php">
                                <?= csrfField() ?>
                                <button type="submit" class="link-tombol">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['tipe']) ?> no-print"><?= e($flash['pesan']) ?></div>
        <?php endif; ?>
