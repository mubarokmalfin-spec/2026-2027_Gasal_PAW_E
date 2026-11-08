<?php
$judul = 'Data Barang';
include 'header.php';
$res = $db->query('SELECT b.*, s.nama AS supplier, (SELECT COUNT(*) FROM transaksi_detail d WHERE d.barang_id=b.id) AS dipakai
                   FROM barang b LEFT JOIN supplier s ON s.id=b.supplier_id ORDER BY b.id');
?>
<div class="head"><h2>Data Barang</h2></div>
<table>
<thead><tr><th style="width:50px">No</th><th>Kode</th><th>Nama Barang</th><th>Harga</th><th>Stok</th><th>Supplier</th><th>Dipakai di Transaksi</th><th style="width:80px">Tindakan</th></tr></thead>
<tbody>
<?php $no = 1; while ($r = $res->fetch_assoc()): ?>
<tr><td><?= $no++ ?></td><td><?= e($r['kode_barang']) ?></td><td><?= e($r['nama_barang']) ?></td>
<td class="num"><?= number_format($r['harga'], 0, ',', '.') ?></td><td class="num"><?= $r['stok'] ?></td><td><?= e($r['supplier']) ?></td><td class="num"><?= $r['dipakai'] ?></td>
<td><a class="btn btn-danger btn-sm" href="hapus_barang.php?id=<?= (int)$r['id'] ?>" onclick="return confirm('Anda yakin akan menghapus barang ini?')">Hapus</a></td></tr>
<?php endwhile; ?>
</tbody></table>
</div></body></html>
