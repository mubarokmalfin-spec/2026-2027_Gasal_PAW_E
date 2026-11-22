<?php
function validTanggal($tanggal)
{
    $objek = DateTime::createFromFormat('Y-m-d', $tanggal);

    return $objek && $objek->format('Y-m-d') === $tanggal;
}

function ambilLaporan($db, $mulai, $selesai)
{
    $laporan = [
        'rekap'            => [],
        'jumlah_pelanggan' => 0,
        'total_pendapatan' => 0,
    ];

    $sql = 'SELECT DATE(waktu_transaksi) AS tgl,
                   SUM(total)            AS total_harian
            FROM transaksi
            WHERE waktu_transaksi BETWEEN ? AND ?
            GROUP BY DATE(waktu_transaksi)
            ORDER BY tgl ASC';

    $stmt = $db->prepare($sql);
    $stmt->bind_param('ss', $mulai, $selesai);
    $stmt->execute();
    $hasil = $stmt->get_result();

    while ($baris = $hasil->fetch_assoc()) {
        $laporan['rekap'][] = $baris;
    }

    $sql = 'SELECT COUNT(DISTINCT pelanggan_id) AS jumlah_pelanggan,
                   COALESCE(SUM(total), 0)      AS total_pendapatan
            FROM transaksi
            WHERE waktu_transaksi BETWEEN ? AND ?';

    $stmt = $db->prepare($sql);
    $stmt->bind_param('ss', $mulai, $selesai);
    $stmt->execute();
    $ringkasan = $stmt->get_result()->fetch_assoc();

    $laporan['jumlah_pelanggan'] = (int) $ringkasan['jumlah_pelanggan'];
    $laporan['total_pendapatan'] = (int) $ringkasan['total_pendapatan'];

    return $laporan;
}
