<?php
require_once 'config.php';
$id = (int)($_GET['id'] ?? 0);
$st = $db->prepare('DELETE FROM supplier WHERE id=?'); $st->bind_param('i', $id); $st->execute();
header('Location: index.php?pesan=' . urlencode('Data supplier berhasil dihapus.')); exit;
