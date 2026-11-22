<?php
require_once 'auth.php';
wajibLevel([1]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();

    $id = (int) ($_POST['id'] ?? 0);

    if ($id === $_SESSION['id_user']) {
        setFlash('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
    } else {
        $stmt = $db->prepare('DELETE FROM user WHERE id_user = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();

        setFlash('sukses', 'Data user berhasil dihapus.');
    }

    redirect('user.php');
}

$namaLevel = [
    1 => 'Owner',
    2 => 'Kasir',
];

$hasil = $db->query('SELECT id_user, username, nama, alamat, hp, level FROM user ORDER BY id_user');
$judul = 'Data Master User';

include 'header.php';
?>

<div class="head">
    <h2>Data Master User</h2>
    <a class="btn btn-success btn-sm" href="user_form.php">Tambah Data</a>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Username</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>HP</th>
            <th>Level</th>
            <th style="width: 140px;">Tindakan</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php while ($row = $hasil->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= e($row['username']) ?></td>
                <td><?= e($row['nama']) ?></td>
                <td><?= e($row['alamat']) ?></td>
                <td><?= e($row['hp']) ?></td>
                <td><?= e($namaLevel[(int) $row['level']] ?? 'Level ' . $row['level']) ?></td>
                <td>
                    <a class="btn btn-warning btn-sm" href="user_form.php?id=<?= (int) $row['id_user'] ?>">Edit</a>

                    <?php if ((int) $row['id_user'] !== $_SESSION['id_user']): ?>
                        <form method="post" class="form-inline"
                              onsubmit="return confirm('Anda yakin akan menghapus user ini?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= (int) $row['id_user'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
