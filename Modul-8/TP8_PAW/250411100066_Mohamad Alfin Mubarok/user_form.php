<?php
require_once 'auth.php';
wajibLevel([1]);

$id    = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$error = '';
$data  = [
    'username' => '',
    'nama'     => '',
    'alamat'   => '',
    'hp'       => '',
    'level'    => 2,
];

if ($id > 0) {
    $stmt = $db->prepare('SELECT username, nama, alamat, hp, level FROM user WHERE id_user = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $ada = $stmt->get_result()->fetch_assoc();

    if (!$ada) {
        setFlash('error', 'Data user tidak ditemukan.');
        redirect('user.php');
    }

    $data = $ada;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();

    $data['username'] = trim($_POST['username'] ?? '');
    $data['nama']     = trim($_POST['nama'] ?? '');
    $data['alamat']   = trim($_POST['alamat'] ?? '');
    $data['hp']       = trim($_POST['hp'] ?? '');
    $data['level']    = (int) ($_POST['level'] ?? 0);
    $password         = $_POST['password'] ?? '';

    $stmt = $db->prepare('SELECT COUNT(*) AS jumlah FROM user WHERE username = ? AND id_user <> ?');
    $stmt->bind_param('si', $data['username'], $id);
    $stmt->execute();
    $usernameKembar = (int) $stmt->get_result()->fetch_assoc()['jumlah'];

    if ($data['username'] === '' || $data['nama'] === '' || $data['alamat'] === '' || $data['hp'] === '') {
        $error = 'Username, nama, alamat, dan HP wajib diisi.';
    } elseif (strlen($data['username']) > 30 || strlen($data['nama']) > 50) {
        $error = 'Username maksimal 30 karakter dan nama maksimal 50 karakter.';
    } elseif (strlen($data['alamat']) > 150 || strlen($data['hp']) > 20) {
        $error = 'Alamat maksimal 150 karakter dan HP maksimal 20 karakter.';
    } elseif (!in_array($data['level'], [1, 2], true)) {
        $error = 'Level harus Owner atau Kasir.';
    } elseif ($id === 0 && $password === '') {
        $error = 'Password wajib diisi.';
    } elseif ($password !== '' && strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($usernameKembar > 0) {
        $error = 'Username sudah digunakan.';
    } elseif ($id === $_SESSION['id_user'] && $data['level'] !== $_SESSION['level']) {
        $error = 'Level akun yang sedang digunakan tidak boleh diubah.';
    } else {
        if ($id > 0 && $password === '') {
            $stmt = $db->prepare('UPDATE user SET username = ?, nama = ?, alamat = ?, hp = ?, level = ? WHERE id_user = ?');
            $stmt->bind_param('ssssii', $data['username'], $data['nama'], $data['alamat'], $data['hp'], $data['level'], $id);
            $pesan = 'Data user berhasil diperbarui.';
        } elseif ($id > 0) {
            $hash = md5($password);
            $stmt = $db->prepare('UPDATE user SET username = ?, password = ?, nama = ?, alamat = ?, hp = ?, level = ? WHERE id_user = ?');
            $stmt->bind_param('sssssii', $data['username'], $hash, $data['nama'], $data['alamat'], $data['hp'], $data['level'], $id);
            $pesan = 'Data user berhasil diperbarui.';
        } else {
            $hash = md5($password);
            $stmt = $db->prepare('INSERT INTO user (username, password, nama, alamat, hp, level) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('sssssi', $data['username'], $hash, $data['nama'], $data['alamat'], $data['hp'], $data['level']);
            $pesan = 'Data user berhasil ditambahkan.';
        }

        $stmt->execute();

        if ($id === $_SESSION['id_user']) {
            $_SESSION['nama']     = $data['nama'];
            $_SESSION['username'] = $data['username'];
        }

        setFlash('sukses', $pesan);
        redirect('user.php');
    }
}

$judul = $id > 0 ? 'Edit Data Master User' : 'Tambah Data Master User Baru';

include 'header.php';
?>

<div class="head">
    <h2><?= e($judul) ?></h2>
</div>

<?php if ($error !== ''): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<form method="post" class="form-master" autocomplete="off">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="form-row">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" class="w-sm" maxlength="30" value="<?= e($data['username']) ?>">
    </div>

    <div class="form-row">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="w-sm"
               placeholder="<?= $id > 0 ? 'kosongkan jika tidak diubah' : 'minimal 6 karakter' ?>">
    </div>

    <div class="form-row">
        <label for="nama">Nama</label>
        <input type="text" id="nama" name="nama" class="w-nama" maxlength="50" value="<?= e($data['nama']) ?>">
    </div>

    <div class="form-row">
        <label for="alamat">Alamat</label>
        <input type="text" id="alamat" name="alamat" class="w-nama" maxlength="150" value="<?= e($data['alamat']) ?>">
    </div>

    <div class="form-row">
        <label for="hp">HP</label>
        <input type="text" id="hp" name="hp" class="w-sm" maxlength="20" value="<?= e($data['hp']) ?>">
    </div>

    <div class="form-row">
        <label for="level">Level</label>
        <select id="level" name="level" class="w-sm">
            <option value="1" <?= (int) $data['level'] === 1 ? 'selected' : '' ?>>1 - Owner</option>
            <option value="2" <?= (int) $data['level'] === 2 ? 'selected' : '' ?>>2 - Kasir</option>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?= $id > 0 ? 'Update' : 'Simpan' ?></button>
        <a class="btn btn-danger" href="user.php">Batal</a>
    </div>
</form>

<?php include 'footer.php'; ?>
