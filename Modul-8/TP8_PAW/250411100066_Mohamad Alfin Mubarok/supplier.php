<?php
require_once 'auth.php';
wajibLevel([1]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();

    $id = (int) ($_POST['id'] ?? 0);

    $stmt = $db->prepare('DELETE FROM supplier WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();

    setFlash('sukses', 'Data supplier berhasil dihapus.');
    redirect('supplier.php');
}

$hasil = $db->query('SELECT * FROM supplier ORDER BY id');
$judul = 'Data Master Supplier';

include 'header.php';
?>

<div class="head">
    <h2>Data Master Supplier</h2>
    <a class="btn btn-success btn-sm" href="supplier_form.php">Tambah Data</a>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Nama</th>
            <th>Telp</th>
            <th>Alamat</th>
            <th style="width: 140px;">Tindakan</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php while ($row = $hasil->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= e($row['nama']) ?></td>
                <td><?= e($row['telp']) ?></td>
                <td><?= e($row['alamat']) ?></td>
                <td>
                    <a class="btn btn-warning btn-sm" href="supplier_form.php?id=<?= (int) $row['id'] ?>">Edit</a>

                    <form method="post" class="form-inline"
                          onsubmit="return confirm('Anda yakin akan menghapus supplier ini?')">
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
