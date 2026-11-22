<?php
require_once 'auth.php';
require_once 'fungsi.php';
wajibLevel([1, 2]);

$mulai   = $_GET['mulai'] ?? '';
$selesai = $_GET['selesai'] ?? '';

$tampilkan = false;
$error     = '';
$laporan   = null;
$labels    = [];
$dataTotal = [];

if ($mulai !== '' || $selesai !== '') {
    if (!validTanggal($mulai) || !validTanggal($selesai)) {
        $error = 'Tanggal mulai dan tanggal selesai wajib diisi dengan benar.';
    } elseif ($mulai > $selesai) {
        $error = 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.';
    } else {
        $tampilkan = true;
        $laporan   = ambilLaporan($db, $mulai, $selesai);

        foreach ($laporan['rekap'] as $baris) {
            $labels[]    = $baris['tgl'];
            $dataTotal[] = (int) $baris['total_harian'];
        }
    }
}

$judul = 'Laporan Penjualan';

include 'header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<div class="no-print">
    <h2>Filter Laporan Penjualan</h2>

    <?php if ($error !== ''): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
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

<?php include 'footer.php'; ?>
