<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']); $telp = trim($_POST['telp']); $alamat = trim($_POST['alamat']);
    if ($nama === '' || $telp === '' || $alamat === '') { $error = 'Semua kolom wajib diisi.'; }
    elseif (strlen($telp) > 12) { $error = 'Telp maksimal 12 karakter.'; }
    else {
        $st = $db->prepare('INSERT INTO supplier (nama,telp,alamat) VALUES (?,?,?)');
        $st->bind_param('sss', $nama, $telp, $alamat); $st->execute();
        header('Location: index.php?pesan=' . urlencode('Data supplier berhasil ditambahkan.')); exit;
    }
}
$judul = 'Tambah Data Master Supplier Baru';
include 'header.php';
?>
<div class="head"><h2>Tambah Data Master Supplier Baru</h2></div>
<?php if (!empty($error)): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
<form method="post" style="margin-top:35px">
  <div class="form-row"><label for="nama">Nama</label><input class="w-nama" type="text" id="nama" name="nama" placeholder="Nama" maxlength="100" required value="<?= e($_POST['nama'] ?? '') ?>"></div>
  <div class="form-row"><label for="telp">Telp</label><input class="w-sm" type="text" id="telp" name="telp" placeholder="telp" maxlength="12" required value="<?= e($_POST['telp'] ?? '') ?>"></div>
  <div class="form-row"><label for="alamat">Alamat</label><input class="w-sm" type="text" id="alamat" name="alamat" placeholder="alamat" required value="<?= e($_POST['alamat'] ?? '') ?>"></div>
  <div class="form-actions"><button class="btn btn-success" type="submit">Simpan</button><a class="btn btn-danger" href="index.php">Batal</a></div>
</form></div></body></html>
