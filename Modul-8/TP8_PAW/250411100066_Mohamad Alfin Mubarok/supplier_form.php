<?php
require_once 'auth.php';
wajibLevel([1]);

$id    = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$error = '';
$data  = [
    'nama'   => '',
    'telp'   => '',
    'alamat' => '',
];

if ($id > 0) {
    $stmt = $db->prepare('SELECT nama, telp, alamat FROM supplier WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $ada = $stmt->get_result()->fetch_assoc();

    if (!$ada) {
        setFlash('error', 'Data supplier tidak ditemukan.');
        redirect('supplier.php');
    }

    $data = $ada;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();

    $data['nama']   = trim($_POST['nama'] ?? '');
    $data['telp']   = trim($_POST['telp'] ?? '');
    $data['alamat'] = trim($_POST['alamat'] ?? '');

    if ($data['nama'] === '' || $data['telp'] === '' || $data['alamat'] === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (strlen($data['nama']) > 100) {
        $error = 'Nama maksimal 100 karakter.';
    } elseif (strlen($data['telp']) > 12) {
        $error = 'Telp maksimal 12 karakter.';
    } else {
        if ($id > 0) {
            $stmt = $db->prepare('UPDATE supplier SET nama = ?, telp = ?, alamat = ? WHERE id = ?');
            $stmt->bind_param('sssi', $data['nama'], $data['telp'], $data['alamat'], $id);
            $pesan = 'Data supplier berhasil diperbarui.';
        } else {
            $stmt = $db->prepare('INSERT INTO supplier (nama, telp, alamat) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $data['nama'], $data['telp'], $data['alamat']);
            $pesan = 'Data supplier berhasil ditambahkan.';
        }

        $stmt->execute();
        setFlash('sukses', $pesan);
        redirect('supplier.php');
    }
}

$judul = $id > 0 ? 'Edit Data Master Supplier' : 'Tambah Data Master Supplier Baru';

include 'header.php';
?>

<div class="head">
    <h2><?= e($judul) ?></h2>
</div>

<?php if ($error !== ''): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<form method="post" class="form-master">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="form-row">
        <label for="nama">Nama</label>
        <input type="text" id="nama" name="nama" class="w-nama" maxlength="100" placeholder="Nama" value="<?= e($data['nama']) ?>">
    </div>

    <div class="form-row">
        <label for="telp">Telp</label>
        <input type="text" id="telp" name="telp" class="w-sm" maxlength="12" placeholder="telp" value="<?= e($data['telp']) ?>">
    </div>

    <div class="form-row">
        <label for="alamat">Alamat</label>
        <input type="text" id="alamat" name="alamat" class="w-sm" placeholder="alamat" value="<?= e($data['alamat']) ?>">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?= $id > 0 ? 'Update' : 'Simpan' ?></button>
        <a class="btn btn-danger" href="supplier.php">Batal</a>
    </div>
</form>

<?php include 'footer.php'; ?>
