<?php
// Koneksi database (sesuaikan dengan XAMPP/Laragon Anda)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli('localhost', 'root', '', 'penjualan');
$db->set_charset('utf8mb4');
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
