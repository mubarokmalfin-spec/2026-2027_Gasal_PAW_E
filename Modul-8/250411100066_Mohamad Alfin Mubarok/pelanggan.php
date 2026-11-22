<?php
require_once 'auth.php';
wajibLevel([1]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();

    $id = $_POST['id'] ?? '';

    $stmt = $db->prepare('SELECT COUNT(*) AS jumlah FROM transaksi WHERE pelanggan_id = ?');
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $dipakai = (int) $stmt->get_result()->fetch_assoc()['jumlah'];

    if ($dipakai > 0) {
        setFlash('error', 'Pelanggan tidak dapat dihapus karena memiliki ' . $dipakai . ' transaksi.');
    } else {
        $stmt = $db->prepare('DELETE FROM pelanggan WHERE id = ?');
        $stmt->bind_param('s', $id);
        $stmt->execute();

        setFlash('sukses', 'Data pelanggan berhasil dihapus.');
    }

    redirect('pelanggan.php');
}

$hasil = $db->query('SELECT * FROM pelanggan ORDER BY id');
$judul = 'Data Master Pelanggan';

include 'header.php';
?>

<div class="head">
    <h2>Data Master Pelanggan</h2>
    <a class="btn btn-success btn-sm" href="pelanggan_form.php">Tambah Data</a>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>ID</th>
            <th>Nama</th>
            <th>Jenis Kelamin</th>
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
                <td><?= e($row['id']) ?></td>
                <td><?= e($row['nama']) ?></td>
                <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                <td><?= e($row['telp']) ?></td>
                <td><?= e($row['alamat']) ?></td>
                <td>
                    <a class="btn btn-warning btn-sm" href="pelanggan_form.php?id=<?= urlencode($row['id']) ?>">Edit</a>

                    <form method="post" class="form-inline"
                          onsubmit="return confirm('Anda yakin akan menghapus pelanggan ini?')">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
