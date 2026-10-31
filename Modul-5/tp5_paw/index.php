<?php
$judul = 'Data Master Supplier';
include 'header.php';
$pesan = $_GET['pesan'] ?? '';
$res = $db->query('SELECT * FROM supplier ORDER BY id');
?>
<div class="head"><h2>Data Master Supplier</h2><a class="btn btn-success btn-sm" href="tambah.php">Tambah Data</a></div>
<?php if ($pesan): ?><div class="alert"><?= e($pesan) ?></div><?php endif; ?>
<table>
<thead><tr><th style="width:50px">No</th><th>Nama</th><th>Telp</th><th>Alamat</th><th style="width:140px">Tindakan</th></tr></thead>
<tbody>
<?php $no = 1; while ($r = $res->fetch_assoc()): ?>
<tr>
  <td><?= $no++ ?></td><td><?= e($r['nama']) ?></td><td><?= e($r['telp']) ?></td><td><?= e($r['alamat']) ?></td>
  <td>
    <a class="btn btn-warning btn-sm" href="edit.php?id=<?= (int)$r['id'] ?>">Edit</a>
    <a class="btn btn-danger btn-sm" href="hapus.php?id=<?= (int)$r['id'] ?>" onclick="return confirm('Anda yakin akan menghapus supplier ini?')">Hapus</a>
  </td>
</tr>
<?php endwhile; ?>
</tbody></table>
</div></body></html>
