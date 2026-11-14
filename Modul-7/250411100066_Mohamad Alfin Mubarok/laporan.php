<?php
require_once 'config.php';
require_once 'fungsi.php';

// Nilai filter dari form (method GET)
$mulai   = $_GET['mulai'] ?? '';
$selesai = $_GET['selesai'] ?? '';

$tampilkan = false;
$error     = '';
$laporan   = null;
$labels    = [];
$dataTotal = [];

// Proses hanya jika form filter sudah dikirim
if ($mulai !== '' || $selesai !== '') {
    if (!validTanggal($mulai) || !validTanggal($selesai)) {
        $error = 'Tanggal mulai dan tanggal selesai wajib diisi dengan benar.';
    } elseif ($mulai > $selesai) {
        $error = 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.';
    } else {
        $tampilkan = true;
        $laporan   = ambilLaporan($db, $mulai, $selesai);

        // Data untuk sumbu X (tanggal) dan sumbu Y (total) pada grafik
        foreach ($laporan['rekap'] as $baris) {
            $labels[]    = $baris['tgl'];
            $dataTotal[] = (int) $baris['total_harian'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <div class="navbar no-print">
        <div class="container">
            <a class="brand" href="laporan.php">Penjualan</a>
        </div>
    </div>

    <div class="container">

        <!-- ===== FORM FILTER RENTANG TANGGAL ===== -->
        <div class="no-print">
            <h2>Filter Laporan Penjualan</h2>

            <?php if ($error !== ''): ?>
                <div class="alert"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="GET" class="filter">
                <label for="mulai">Dari</label>
                <input type="date" id="mulai" name="mulai" value="<?= e($mulai) ?>" required>

                <label for="selesai">Sampai</label>
                <input type="date" id="selesai" name="selesai" value="<?= e($selesai) ?>" required>

                <button type="submit" class="btn btn-success">Tampilkan</button>
            </form>
        </div>

        <?php if ($tampilkan): ?>

            <h2>
                Rekap Laporan Penjualan
                <small><?= e($mulai) ?> sampai <?= e($selesai) ?></small>
            </h2>

            <!-- ===== TOMBOL ===== -->
            <div class="no-print aksi">
                <a class="btn btn-primary" href="laporan.php">Kembali</a>

                <?php if (!empty($laporan['rekap'])): ?>
                    <button type="button" class="btn btn-warning" onclick="window.print()">Cetak</button>
                    <a class="btn btn-warning"
                       href="export_excel.php?mulai=<?= urlencode($mulai) ?>&selesai=<?= urlencode($selesai) ?>">Excel</a>
                <?php endif; ?>
            </div>

            <?php if (empty($laporan['rekap'])): ?>

                <p class="kosong">Tidak ada transaksi pada rentang tanggal tersebut.</p>

            <?php else: ?>

                <!-- ===== GRAFIK BATANG ===== -->
                <div class="grafik">
                    <canvas id="grafikPenjualan"></canvas>
                </div>

                <script>
                    const ctx = document.getElementById('grafikPenjualan').getContext('2d');

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($labels) ?>,
                            datasets: [{
                                label: 'Total',
                                data: <?= json_encode($dataTotal) ?>,
                                backgroundColor: 'rgba(0, 0, 0, 0.1)'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: false,
                            scales: {
                                x: {
                                    title: { display: true, text: 'Tanggal Transaksi' }
                                },
                                y: {
                                    beginAtZero: true,
                                    title: { display: true, text: 'Total Pendapatan (Rp)' },
                                    ticks: {
                                        callback: function (nilai) {
                                            return nilai.toLocaleString('en-US');
                                        }
                                    }
                                }
                            }
                        }
                    });
                </script>

                <!-- ===== TABEL REKAPITULASI ===== -->
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Total Pendapatan</th>
                            <th>Tanggal Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($laporan['rekap'] as $baris): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= rupiah($baris['total_harian']) ?></td>
                                <td><?= date('d M Y', strtotime($baris['tgl'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- ===== RINGKASAN TOTAL ===== -->
                <div class="ringkasan">
                    <div class="card">
                        <div class="card-judul">Jumlah Pelanggan</div>
                        <div class="card-nilai"><?= $laporan['jumlah_pelanggan'] ?> Orang</div>
                    </div>

                    <div class="card">
                        <div class="card-judul">Jumlah Pendapatan</div>
                        <div class="card-nilai"><?= rupiah($laporan['total_pendapatan']) ?></div>
                    </div>
                </div>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</body>
</html>
