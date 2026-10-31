<?php
require_once 'config.php';
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$st = $db->prepare('SELECT * FROM supplier WHERE id=?'); $st->bind_param('i', $id); $st->execute();
$data = $st->get_result()->fetch_assoc();
if (!$data) { header('Location: index.php?pesan=' . urlencode('Data tidak ditemukan.')); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']); $telp = trim($_POST['telp']); $alamat = trim($_POST['alamat']);
    if ($nama === '' || $telp === '' || $alamat === '') { $error = 'Semua kolom wajib diisi.'; }
    elseif (strlen($telp) > 12) { $error = 'Telp maksimal 12 karakter.'; }
    else {
        $up = $db->prepare('UPDATE supplier SET nama=?, telp=?, alamat=? WHERE id=?');
        $up->bind_param('sssi', $nama, $telp, $alamat, $id); $up->execute();
        header('Location: index.php?pesan=' . urlencode('Data supplier berhasil diperbarui.')); exit;
    }
    $data = ['nama'=>$nama,'telp'=>$telp,'alamat'=>$alamat];
}
$judul = 'Edit Data Master Supplier';
include 'header.php';
?>
<div class="head"><h2>Edit Data Master Supplier</h2></div>
<?php if (!empty($error)): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
<form method="post" style="margin-top:35px">
  <input type="hidden" name="id" value="<?= $id ?>">
  <div class="form-row"><label for="nama">Nama</label><input class="w-nama" type="text" id="nama" name="nama" maxlength="100" required value="<?= e($data['nama']) ?>"></div>
  <div class="form-row"><label for="telp">Telp</label><input class="w-sm" type="text" id="telp" name="telp" maxlength="12" required value="<?= e($data['telp']) ?>"></div>
  <div class="form-row"><label for="alamat">Alamat</label><input class="w-sm" type="text" id="alamat" name="alamat" required value="<?= e($data['alamat']) ?>"></div>
  <div class="form-actions"><button class="btn btn-success" type="submit">Update</button><a class="btn btn-danger" href="index.php">Batal</a></div>
</form></div></body></html>
