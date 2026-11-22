<?php
require_once __DIR__ . '/config.php';

header('Cache-Control: no-store, no-cache, must-revalidate');

ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function sudahLogin()
{
    return isset($_SESSION['id_user']);
}

function levelUser()
{
    return $_SESSION['level'] ?? 0;
}

function wajibLogin()
{
    if (!sudahLogin()) {
        setFlash('error', 'Silakan login terlebih dahulu.');
        redirect('login.php');
    }
}

function wajibLevel(array $levelBoleh)
{
    wajibLogin();

    if (!in_array(levelUser(), $levelBoleh, true)) {
        http_response_code(403);
        $judul = 'Akses Ditolak';
        include __DIR__ . '/akses_ditolak.php';
        exit;
    }
}

function setFlash($tipe, $pesan)
{
    $_SESSION['flash'] = [
        'tipe'  => $tipe,
        'pesan' => $pesan,
    ];
}

function ambilFlash()
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $flash;
}

function csrfToken()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function csrfField()
{
    return '<input type="hidden" name="csrf" value="' . csrfToken() . '">';
}

function cekCsrf()
{
    $token = $_POST['csrf'] ?? '';

    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(400);
        die('Permintaan tidak valid.');
    }
}
