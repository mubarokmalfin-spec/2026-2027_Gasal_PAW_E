<?php
require_once 'auth.php';
wajibLevel([1]);

$idLama = trim($_GET['id'] ?? $_POST['id_lama'] ?? '');
$edit   = $idLama !== '';
$error  = '';
$data   = [
    'id'            => '',
    'nama'          => '',
    'jenis_kelamin' => 'L',
    'telp'          => '',
    'alamat'        => '',
];

if ($edit) {
    $stmt = $db->prepare('SELECT id, nama, jenis_kelamin, telp, alamat FROM pelanggan WHERE id = ?');
    $stmt->bind_param('s', $idLama);
    $stmt->execute();
    $ada = $stmt->get_result()->fetch_assoc();

    if (!$ada) {
        setFlash('error', 'Data pelanggan tidak ditemukan.');
        redirect('pelanggan.php');
    }

    $data = $ada;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();

    if (!$edit) {
        $data['id'] = trim($_POST['id'] ?? '');
    }

    $data['nama']          = trim($_POST['nama'] ?? '');
    $data['jenis_kelamin'] = $_POST['jenis_kelamin'] ?? '';
    $data['telp']          = trim($_POST['telp'] ?? '');
    $data['alamat']        = trim($_POST['alamat'] ?? '');

    $idKembar = 0;

    if (!$edit && $data['id'] !== '') {
        $stmt = $db->prepare('SELECT COUNT(*) AS jumlah FROM pelanggan WHERE id = ?');
        $stmt->bind_param('s', $data['id']);
        $stmt->execute();
        $idKembar = (int) $stmt->get_result()->fetch_assoc()['jumlah'];
    }

    if ($data['id'] === '' || $data['nama'] === '' || $data['telp'] === '' || $data['alamat'] === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (strlen($data['id']) > 20) {
        $error = 'ID pelanggan maksimal 20 karakter.';
    } elseif (strlen($data['nama']) > 100) {
        $error = 'Nama maksimal 100 karakter.';
    } elseif (!in_array($data['jenis_kelamin'], ['L', 'P'], true)) {
        $error = 'Jenis kelamin tidak valid.';
    } elseif (strlen($data['telp']) > 12) {
        $error = 'Telp maksimal 12 karakter.';
    } elseif ($idKembar > 0) {
        $error = 'ID pelanggan sudah digunakan.';
    } else {
        if ($edit) {
            $stmt = $db->prepare('UPDATE pelanggan SET nama = ?, jenis_kelamin = ?, telp = ?, alamat = ? WHERE id = ?');
            $stmt->bind_param('sssss', $data['nama'], $data['jenis_kelamin'], $data['telp'], $data['alamat'], $idLama);
            $pesan = 'Data pelanggan berhasil diperbarui.';
        } else {
            $stmt = $db->prepare('INSERT INTO pelanggan (id, nama, jenis_kelamin, telp, alamat) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $data['id'], $data['nama'], $data['jenis_kelamin'], $data['telp'], $data['alamat']);
            $pesan = 'Data pelanggan berhasil ditambahkan.';
        }

        $stmt->execute();
        setFlash('sukses', $pesan);
        redirect('pelanggan.php');
    }
}

$judul = $edit ? 'Edit Data Master Pelanggan' : 'Tambah Data Master Pelanggan Baru';

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
    <input type="hidden" name="id_lama" value="<?= e($idLama) ?>">

    <div class="form-row">
        <label for="id">ID Pelanggan</label>
        <input type="text" id="id" name="id" class="w-sm" maxlength="20"
               value="<?= e($data['id']) ?>" <?= $edit ? 'readonly' : '' ?>>
    </div>

    <div class="form-row">
        <label for="nama">Nama</label>
        <input type="text" id="nama" name="nama" class="w-nama" maxlength="100" value="<?= e($data['nama']) ?>">
    </div>

    <div class="form-row">
        <label for="jenis_kelamin">Jenis Kelamin</label>
        <select id="jenis_kelamin" name="jenis_kelamin" class="w-sm">
            <option value="L" <?= $data['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="P" <?= $data['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
        </select>
    </div>

    <div class="form-row">
        <label for="telp">Telp</label>
        <input type="text" id="telp" name="telp" class="w-sm" maxlength="12" value="<?= e($data['telp']) ?>">
    </div>

    <div class="form-row">
        <label for="alamat">Alamat</label>
        <input type="text" id="alamat" name="alamat" class="w-nama" value="<?= e($data['alamat']) ?>">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?= $edit ? 'Update' : 'Simpan' ?></button>
        <a class="btn btn-danger" href="pelanggan.php">Batal</a>
    </div>
</form>

<?php include 'footer.php'; ?>
