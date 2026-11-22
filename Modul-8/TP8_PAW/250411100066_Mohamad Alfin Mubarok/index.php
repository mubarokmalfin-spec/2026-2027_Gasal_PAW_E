<?php
require_once 'auth.php';
wajibLevel([1, 2]);

$ringkasan = $db->query('SELECT COUNT(*) AS jumlah_transaksi, COALESCE(SUM(total), 0) AS total_pendapatan FROM transaksi')->fetch_assoc();
$jumlahBarang = $db->query('SELECT COUNT(*) AS jumlah FROM barang')->fetch_assoc()['jumlah'];

$namaLevel = levelUser() === 1 ? 'Owner' : 'Kasir';
$judul     = 'Home';

include 'header.php';
?>

<div class="head">
    <h2>Selamat Datang, <?= e($_SESSION['nama']) ?></h2>
</div>

<p>Anda login sebagai <b><?= $namaLevel ?></b>.</p>

<div class="ringkasan">
    <div class="card">
        <div class="card-judul">Jumlah Transaksi</div>
        <div class="card-nilai"><?= number_format($ringkasan['jumlah_transaksi'], 0, ',', '.') ?></div>
    </div>

    <div class="card">
        <div class="card-judul">Total Pendapatan</div>
        <div class="card-nilai"><?= rupiah($ringkasan['total_pendapatan']) ?></div>
    </div>

    <?php if (levelUser() === 1): ?>
        <div class="card">
            <div class="card-judul">Jumlah Barang</div>
            <div class="card-nilai"><?= number_format($jumlahBarang, 0, ',', '.') ?></div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
