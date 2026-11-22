<?php
require_once 'auth.php';
wajibLevel([1]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();

    $id = (int) ($_POST['id'] ?? 0);

    $stmt = $db->prepare('SELECT COUNT(*) AS jumlah FROM transaksi_detail WHERE barang_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $dipakai = (int) $stmt->get_result()->fetch_assoc()['jumlah'];

    if ($dipakai > 0) {
        setFlash('error', 'Barang tidak dapat dihapus karena masih digunakan pada ' . $dipakai . ' transaksi.');
    } else {
        $stmt = $db->prepare('DELETE FROM barang WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();

        setFlash('sukses', 'Data barang berhasil dihapus.');
    }

    redirect('barang.php');
}

$sql = 'SELECT b.*, s.nama AS supplier
        FROM barang b
        LEFT JOIN supplier s ON s.id = b.supplier_id
        ORDER BY b.id';
$hasil = $db->query($sql);
$judul = 'Data Master Barang';

include 'header.php';
?>

<div class="head">
    <h2>Data Master Barang</h2>
    <a class="btn btn-success btn-sm" href="barang_form.php">Tambah Data</a>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Supplier</th>
            <th style="width: 140px;">Tindakan</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php while ($row = $hasil->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= e($row['kode_barang']) ?></td>
                <td><?= e($row['nama_barang']) ?></td>
                <td class="num"><?= rupiah($row['harga']) ?></td>
                <td class="num"><?= (int) $row['stok'] ?></td>
                <td><?= e($row['supplier']) ?></td>
                <td>
                    <a class="btn btn-warning btn-sm" href="barang_form.php?id=<?= (int) $row['id'] ?>">Edit</a>

                    <form method="post" class="form-inline"
                          onsubmit="return confirm('Anda yakin akan menghapus barang ini?')">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
