<?php
require_once 'config.php';
$id = (int)($_GET['id'] ?? 0);
// 1) cek apakah barang sudah dipakai di transaksi_detail
$st = $db->prepare('SELECT COUNT(*) AS n FROM transaksi_detail WHERE barang_id=?'); $st->bind_param('i', $id); $st->execute();
$n = (int)$st->get_result()->fetch_assoc()['n'];
if ($n > 0) {   // sudah dipakai -> tolak
    header('Location: barang.php?err=' . urlencode("Barang tidak dapat dihapus karena masih digunakan pada $n transaksi.")); exit;
}
// 2) belum dipakai -> hapus
try {
    $st = $db->prepare('DELETE FROM barang WHERE id=?'); $st->bind_param('i', $id); $st->execute();
    header('Location: barang.php?pesan=' . urlencode('Data barang berhasil dihapus.'));
} catch (Throwable $e) {
    header('Location: barang.php?err=' . urlencode('Barang tidak dapat dihapus.'));
}
exit;
