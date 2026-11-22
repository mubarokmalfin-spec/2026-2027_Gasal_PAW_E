<?php
require_once 'auth.php';

if (sudahLogin()) {
    redirect('index.php');
}

$error    = '';
$username = '';
$flash    = ambilFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $db->prepare('SELECT id_user, username, password, nama, level FROM user WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || !hash_equals($user['password'], md5($password))) {
            $error = 'Username atau password salah.';
        } elseif (!in_array((int) $user['level'], [1, 2], true)) {
            $error = 'Level user ini tidak memiliki akses ke aplikasi.';
        } else {
            session_regenerate_id(true);

            $_SESSION['id_user']  = (int) $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama']     = $user['nama'];
            $_SESSION['level']    = (int) $user['level'];

            redirect('index.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="halaman-login">

    <div class="login-kotak">
        <h1>Sistem Penjualan</h1>
        <p>Silakan login untuk melanjutkan</p>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php elseif ($flash): ?>
            <div class="alert alert-<?= e($flash['tipe']) ?>"><?= e($flash['pesan']) ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <?= csrfField() ?>

            <div class="form-grup">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" maxlength="30" value="<?= e($username) ?>" autofocus>
            </div>

            <div class="form-grup">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
            </div>

            <button type="submit" class="btn btn-primary btn-blok">Login</button>
        </form>
    </div>

</body>
</html>
