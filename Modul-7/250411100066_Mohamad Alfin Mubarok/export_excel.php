<?php
require_once 'config.php';
require_once 'fungsi.php';

$mulai   = $_GET['mulai'] ?? '';
$selesai = $_GET['selesai'] ?? '';

// Tanggal harus valid sebelum file dikirim ke browser
if (!validTanggal($mulai) || !validTanggal($selesai) || $mulai > $selesai) {
    die('Rentang tanggal tidak valid.');
}

$laporan = ambilLaporan($db, $mulai, $selesai);

// Header HTTP: memberi tahu browser bahwa isinya file Excel yang harus diunduh
$namaFile = 'rekap_penjualan_' . $mulai . '_sd_' . $selesai . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $namaFile . '"');
header('Pragma: no-cache');
header('Expires: 0');
?>
<meta charset="UTF-8">

<h3>Rekap Laporan Penjualan <?= e($mulai) ?> sampai <?= e($selesai) ?></h3>

<table border="1">
    <tr>
        <th>No</th>
        <th>Total Pendapatan</th>
        <th>Tanggal Transaksi</th>
    </tr>

    <?php $no = 1; ?>
    <?php foreach ($laporan['rekap'] as $baris): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= (int) $baris['total_harian'] ?></td>
            <td><?= date('d M Y', strtotime($baris['tgl'])) ?></td>
        </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="2"><b>Jumlah Pelanggan</b></td>
        <td><?= $laporan['jumlah_pelanggan'] ?> Orang</td>
    </tr>
    <tr>
        <td colspan="2"><b>Jumlah Pendapatan</b></td>
        <td><?= $laporan['total_pendapatan'] ?></td>
    </tr>
</table>
