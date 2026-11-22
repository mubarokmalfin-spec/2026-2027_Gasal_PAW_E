<?php
require_once 'auth.php';
wajibLevel([1]);

$id    = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$error = '';
$data  = [
    'kode_barang' => '',
    'nama_barang' => '',
    'harga'       => '',
    'stok'        => '',
    'supplier_id' => '',
];

if ($id > 0) {
    $stmt = $db->prepare('SELECT kode_barang, nama_barang, harga, stok, supplier_id FROM barang WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $ada = $stmt->get_result()->fetch_assoc();

    if (!$ada) {
        setFlash('error', 'Data barang tidak ditemukan.');
        redirect('barang.php');
    }

    $data = $ada;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();

    $data['kode_barang'] = trim($_POST['kode_barang'] ?? '');
    $data['nama_barang'] = trim($_POST['nama_barang'] ?? '');
    $data['harga']       = trim($_POST['harga'] ?? '');
    $data['stok']        = trim($_POST['stok'] ?? '');
    $data['supplier_id'] = trim($_POST['supplier_id'] ?? '');

    $stmt = $db->prepare('SELECT COUNT(*) AS jumlah FROM barang WHERE kode_barang = ? AND id <> ?');
    $stmt->bind_param('si', $data['kode_barang'], $id);
    $stmt->execute();
    $kodeKembar = (int) $stmt->get_result()->fetch_assoc()['jumlah'];

    $stmt = $db->prepare('SELECT COUNT(*) AS jumlah FROM supplier WHERE id = ?');
    $supplierId = (int) $data['supplier_id'];
    $stmt->bind_param('i', $supplierId);
    $stmt->execute();
    $supplierAda = (int) $stmt->get_result()->fetch_assoc()['jumlah'];

    if ($data['kode_barang'] === '' || $data['nama_barang'] === '' || $data['harga'] === '' || $data['stok'] === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (strlen($data['kode_barang']) > 10) {
        $error = 'Kode barang maksimal 10 karakter.';
    } elseif (strlen($data['nama_barang']) > 100) {
        $error = 'Nama barang maksimal 100 karakter.';
    } elseif (!ctype_digit($data['harga']) || !ctype_digit($data['stok'])) {
        $error = 'Harga dan stok harus berupa angka bulat positif.';
    } elseif ($kodeKembar > 0) {
        $error = 'Kode barang sudah digunakan.';
    } elseif ($supplierAda === 0) {
        $error = 'Supplier wajib dipilih.';
    } else {
        $harga = (int) $data['harga'];
        $stok  = (int) $data['stok'];

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE barang SET kode_barang = ?, nama_barang = ?, harga = ?, stok = ?, supplier_id = ? WHERE id = ?');
            $stmt->bind_param('ssiiii', $data['kode_barang'], $data['nama_barang'], $harga, $stok, $supplierId, $id);
            $pesan = 'Data barang berhasil diperbarui.';
        } else {
            $stmt = $db->prepare('INSERT INTO barang (kode_barang, nama_barang, harga, stok, supplier_id) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('ssiii', $data['kode_barang'], $data['nama_barang'], $harga, $stok, $supplierId);
            $pesan = 'Data barang berhasil ditambahkan.';
        }

        $stmt->execute();
        setFlash('sukses', $pesan);
        redirect('barang.php');
    }
}

$daftarSupplier = $db->query('SELECT id, nama FROM supplier ORDER BY nama');
$judul = $id > 0 ? 'Edit Data Master Barang' : 'Tambah Data Master Barang Baru';

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
        <label for="kode_barang">Kode Barang</label>
        <input type="text" id="kode_barang" name="kode_barang" class="w-sm" maxlength="10" value="<?= e($data['kode_barang']) ?>">
    </div>

    <div class="form-row">
        <label for="nama_barang">Nama Barang</label>
        <input type="text" id="nama_barang" name="nama_barang" class="w-nama" maxlength="100" value="<?= e($data['nama_barang']) ?>">
    </div>

    <div class="form-row">
        <label for="harga">Harga</label>
        <input type="text" id="harga" name="harga" class="w-sm" value="<?= e($data['harga']) ?>">
    </div>

    <div class="form-row">
        <label for="stok">Stok</label>
        <input type="text" id="stok" name="stok" class="w-sm" value="<?= e($data['stok']) ?>">
    </div>

    <div class="form-row">
        <label for="supplier_id">Supplier</label>
        <select id="supplier_id" name="supplier_id" class="w-nama">
            <option value="">[pilih supplier]</option>
            <?php while ($supplier = $daftarSupplier->fetch_assoc()): ?>
                <option value="<?= (int) $supplier['id'] ?>"
                    <?= (string) $supplier['id'] === (string) $data['supplier_id'] ? 'selected' : '' ?>>
                    <?= e($supplier['nama']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?= $id > 0 ? 'Update' : 'Simpan' ?></button>
        <a class="btn btn-danger" href="barang.php">Batal</a>
    </div>
</form>

<?php include 'footer.php'; ?>
