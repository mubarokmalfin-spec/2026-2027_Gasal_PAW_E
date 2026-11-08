<?php
require_once 'config.php';
$rp = fn($n) => 'Rp ' . number_format((int)$n, 0, ',', '.');

// ---------- PROSES FORM ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'tambah_transaksi') {                       // simpan MASTER
        $tgl = $_POST['waktu_transaksi'] ?? ''; $plg = $_POST['pelanggan_id'] ?? ''; $ket = trim($_POST['keterangan'] ?? '');
        if ($tgl === '' || $plg === '') { header('Location: transaksi.php?err=' . urlencode('Tanggal dan pelanggan wajib diisi.')); exit; }
        $st = $db->prepare('INSERT INTO transaksi (waktu_transaksi,keterangan,total,pelanggan_id) VALUES (?,?,0,?)');
        $st->bind_param('sss', $tgl, $ket, $plg); $st->execute();
        header('Location: transaksi.php?id=' . $db->insert_id . '&pesan=' . urlencode('Transaksi baru dibuat. Silakan isi detail barang.')); exit;
    }
    if ($aksi === 'tambah_detail') {                          // simpan DETAIL + update total
        $tid = (int)$_POST['transaksi_id']; $bid = (int)$_POST['barang_id']; $qty = (int)$_POST['qty'];
        $back = 'transaksi.php?id=' . $tid;
        if ($bid <= 0 || $qty < 1) { header("Location: $back&err=" . urlencode('Pilih barang dan isi qty minimal 1.')); exit; }
        $st = $db->prepare('SELECT harga FROM barang WHERE id=?'); $st->bind_param('i', $bid); $st->execute();
        $b = $st->get_result()->fetch_assoc();
        $st = $db->prepare('SELECT 1 FROM transaksi_detail WHERE transaksi_id=? AND barang_id=?'); $st->bind_param('ii', $tid, $bid); $st->execute();
        if (!$b) { header("Location: $back&err=" . urlencode('Barang tidak ditemukan.')); exit; }
        if ($st->get_result()->num_rows > 0) { header("Location: $back&err=" . urlencode('Barang sudah ada di detail transaksi ini.')); exit; }
        try {
            $db->begin_transaction();
            $ins = $db->prepare('INSERT INTO transaksi_detail (transaksi_id,barang_id,harga,qty) VALUES (?,?,?,?)');
            $ins->bind_param('iiii', $tid, $bid, $b['harga'], $qty); $ins->execute();
            // hitung ulang total = SUM(harga * qty) seluruh detail transaksi ini
            $up = $db->prepare('UPDATE transaksi SET total=(SELECT COALESCE(SUM(harga*qty),0) FROM transaksi_detail WHERE transaksi_id=?) WHERE id=?');
            $up->bind_param('ii', $tid, $tid); $up->execute();
            $db->commit();
            header("Location: $back&pesan=" . urlencode('Detail tersimpan, total transaksi diperbarui.')); exit;
        } catch (Throwable $ex) { $db->rollback(); header("Location: $back&err=" . urlencode('Gagal menyimpan detail.')); exit; }
    }
}

$id = (int)($_GET['id'] ?? 0);
$judul = 'Master Detail Transaksi';
include 'header.php';

$master = $db->query('SELECT t.*, p.nama AS pelanggan FROM transaksi t JOIN pelanggan p ON p.id=t.pelanggan_id ORDER BY t.id');
$plg = $db->query('SELECT id,nama FROM pelanggan ORDER BY nama');
?>
<div class="head"><h2>Master Detail Transaksi</h2></div>

<h3>Master: Data Transaksi</h3>
<table>
<thead><tr><th>ID</th><th>Tanggal</th><th>Pelanggan</th><th>Keterangan</th><th>Total</th><th style="width:90px">Tindakan</th></tr></thead>
<tbody>
<?php while ($r = $master->fetch_assoc()): ?>
<tr class="<?= $r['id'] == $id ? 'sel' : '' ?>">
  <td><?= $r['id'] ?></td><td><?= e($r['waktu_transaksi']) ?></td><td><?= e($r['pelanggan']) ?></td><td><?= e($r['keterangan']) ?></td>
  <td class="num"><?= $rp($r['total']) ?></td>
  <td><a class="btn btn-success btn-sm" href="transaksi.php?id=<?= $r['id'] ?>">Detail</a></td>
</tr>
<?php endwhile; ?>
</tbody></table>

<form method="post" class="inline">
  <input type="hidden" name="aksi" value="tambah_transaksi">
  <strong>Transaksi baru:</strong>
  <input type="date" name="waktu_transaksi" value="<?= date('Y-m-d') ?>" required>
  <select name="pelanggan_id" required><option value="">[pilih pelanggan]</option>
  <?php while ($p = $plg->fetch_assoc()) echo '<option value="'.e($p['id']).'">'.e($p['nama']).'</option>'; ?></select>
  <input type="text" name="keterangan" placeholder="keterangan">
  <button class="btn btn-success" type="submit">Tambah Transaksi</button>
</form>

<h3>Detail: Barang pada Transaksi <?= $id ? '#' . $id : '' ?></h3>
<?php
if (!$id) { echo '<p class="muted">Klik tombol "Detail" pada salah satu transaksi di atas untuk menampilkan detailnya.</p>'; }
else {
  $st = $db->prepare('SELECT * FROM transaksi WHERE id=?'); $st->bind_param('i', $id); $st->execute();
  $trx = $st->get_result()->fetch_assoc();
  if (!$trx) { echo '<p class="muted">Transaksi tidak ditemukan.</p>'; }
  else {
    $st = $db->prepare('SELECT d.*, b.kode_barang, b.nama_barang FROM transaksi_detail d JOIN barang b ON b.id=d.barang_id WHERE d.transaksi_id=? ORDER BY b.nama_barang');
    $st->bind_param('i', $id); $st->execute(); $det = $st->get_result();
?>
<table>
<thead><tr><th style="width:50px">No</th><th>Kode</th><th>Nama Barang</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead>
<tbody>
<?php $no = 1; while ($d = $det->fetch_assoc()): ?>
<tr><td><?= $no++ ?></td><td><?= e($d['kode_barang']) ?></td><td><?= e($d['nama_barang']) ?></td>
<td class="num"><?= $rp($d['harga']) ?></td><td class="num"><?= $d['qty'] ?></td><td class="num"><?= $rp($d['harga'] * $d['qty']) ?></td></tr>
<?php endwhile; if ($no === 1) echo '<tr><td colspan="6" class="muted">Belum ada detail barang.</td></tr>'; ?>
</tbody>
<tfoot><tr><td colspan="5" class="num">Total Transaksi</td><td class="num"><?= $rp($trx['total']) ?></td></tr></tfoot>
</table>
<?php
    // dropdown hanya berisi barang yang BELUM ada di detail transaksi ini
    $st = $db->prepare('SELECT id,kode_barang,nama_barang,harga FROM barang WHERE id NOT IN (SELECT barang_id FROM transaksi_detail WHERE transaksi_id=?) ORDER BY nama_barang');
    $st->bind_param('i', $id); $st->execute(); $opt = $st->get_result();
    if ($opt->num_rows === 0) echo '<p class="muted">Semua barang sudah dimasukkan ke transaksi ini.</p>';
    else { ?>
<form method="post" class="inline">
  <input type="hidden" name="aksi" value="tambah_detail"><input type="hidden" name="transaksi_id" value="<?= $id ?>">
  <strong>Tambah detail:</strong>
  <select name="barang_id" required><option value="">[pilih barang]</option>
  <?php while ($o = $opt->fetch_assoc()) echo '<option value="'.$o['id'].'">'.e($o['kode_barang'].' - '.$o['nama_barang'].' ('.$rp($o['harga']).')').'</option>'; ?></select>
  <input type="number" name="qty" min="1" value="1" style="width:90px" required>
  <button class="btn btn-success" type="submit">Simpan</button>
</form>
<?php } } } ?>
</div></body></html>
