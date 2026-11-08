<?php require_once __DIR__.'/config.php'; ?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($judul ?? 'Penjualan') ?></title>
<link rel="stylesheet" href="assets/style.css"></head><body>
<div class="navbar"><div class="container nav"><a class="brand" href="transaksi.php">Penjualan</a><a href="transaksi.php">Master Detail Transaksi</a><a href="barang.php">Data Barang</a></div></div>
<div class="container">
<?php
if (!empty($_GET['pesan'])) echo '<div class="alert">'.e($_GET['pesan']).'</div>';
if (!empty($_GET['err'])) echo '<div class="alert err">'.e($_GET['err']).'</div>';
