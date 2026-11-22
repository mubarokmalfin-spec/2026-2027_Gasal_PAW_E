<?php
require_once 'auth.php';
require_once 'fungsi.php';
wajibLevel([1, 2]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();

    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'tambah_transaksi') {
        $tanggal     = $_POST['waktu_transaksi'] ?? '';
        $pelangganId = $_POST['pelanggan_id'] ?? '';
        $keterangan  = trim($_POST['keterangan'] ?? '');

        if (!validTanggal($tanggal) || $pelangganId === '') {
            setFlash('error', 'Tanggal dan pelanggan wajib diisi dengan benar.');
            redirect('transaksi.php');
        }

        try {
            $stmt = $db->prepare('INSERT INTO transaksi (waktu_transaksi, keterangan, total, pelanggan_id) VALUES (?, ?, 0, ?)');
            $stmt->bind_param('sss', $tanggal, $keterangan, $pelangganId);
            $stmt->execute();
        } catch (Throwable $ex) {
            setFlash('error', 'Pelanggan tidak ditemukan.');
            redirect('transaksi.php');
        }

        setFlash('sukses', 'Transaksi baru dibuat. Silakan isi detail barang.');
        redirect('transaksi.php?id=' . $db->insert_id);
    }

    if ($aksi === 'tambah_detail') {
        $transaksiId = (int) ($_POST['transaksi_id'] ?? 0);
        $barangId    = (int) ($_POST['barang_id'] ?? 0);
        $qty         = (int) ($_POST['qty'] ?? 0);
        $kembali     = 'transaksi.php?id=' . $transaksiId;

        if ($barangId <= 0 || $qty < 1) {
            setFlash('error', 'Pilih barang dan isi qty minimal 1.');
            redirect($kembali);
        }

        $stmt = $db->prepare('SELECT harga FROM barang WHERE id = ?');
        $stmt->bind_param('i', $barangId);
        $stmt->execute();
        $barang = $stmt->get_result()->fetch_assoc();

        $stmt = $db->prepare('SELECT 1 FROM transaksi_detail WHERE transaksi_id = ? AND barang_id = ?');
        $stmt->bind_param('ii', $transaksiId, $barangId);
        $stmt->execute();
        $sudahAda = $stmt->get_result()->num_rows > 0;

        if (!$barang) {
            setFlash('error', 'Barang tidak ditemukan.');
            redirect($kembali);
        }

        if ($sudahAda) {
            setFlash('error', 'Barang sudah ada di detail transaksi ini.');
            redirect($kembali);
        }

        try {
            $db->begin_transaction();

            $stmt = $db->prepare('INSERT INTO transaksi_detail (transaksi_id, barang_id, harga, qty) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('iiii', $transaksiId, $barangId, $barang['harga'], $qty);
            $stmt->execute();

            $sql = 'UPDATE transaksi
                    SET total = (SELECT COALESCE(SUM(harga * qty), 0)
                                 FROM transaksi_detail
                                 WHERE transaksi_id = ?)
                    WHERE id = ?';
            $stmt = $db->prepare($sql);
            $stmt->bind_param('ii', $transaksiId, $transaksiId);
            $stmt->execute();

            $db->commit();
            setFlash('sukses', 'Detail tersimpan, total transaksi diperbarui.');
        } catch (Throwable $ex) {
            $db->rollback();
            setFlash('error', 'Gagal menyimpan detail transaksi.');
        }

        redirect($kembali);
    }
}

$id = (int) ($_GET['id'] ?? 0);

$sql = 'SELECT t.*, p.nama AS pelanggan
        FROM transaksi t
        JOIN pelanggan p ON p.id = t.pelanggan_id
        ORDER BY t.id DESC
        LIMIT 20';
$master    = $db->query($sql);
$pelanggan = $db->query('SELECT id, nama FROM pelanggan ORDER BY nama');
$judul     = 'Master Detail Transaksi';

include 'header.php';
?>

<div class="head">
    <h2>Master Detail Transaksi</h2>
</div>

<h3>Master: 20 Transaksi Terakhir</h3>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Pelanggan</th>
            <th>Keterangan</th>
            <th>Total</th>
            <th style="width: 90px;">Tindakan</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $master->fetch_assoc()): ?>
            <tr class="<?= (int) $row['id'] === $id ? 'sel' : '' ?>">
                <td><?= (int) $row['id'] ?></td>
                <td><?= e($row['waktu_transaksi']) ?></td>
                <td><?= e($row['pelanggan']) ?></td>
                <td><?= e($row['keterangan']) ?></td>
                <td class="num"><?= rupiah($row['total']) ?></td>
                <td>
                    <a class="btn btn-success btn-sm" href="transaksi.php?id=<?= (int) $row['id'] ?>">Detail</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<form method="get" class="inline">
    <strong>Cari nomor transaksi:</strong>
    <input type="number" name="id" min="1" placeholder="ID transaksi" style="width: 140px;">
    <button type="submit" class="btn btn-primary">Tampilkan Detail</button>
</form>

<form method="post" class="inline">
    <?= csrfField() ?>
    <input type="hidden" name="aksi" value="tambah_transaksi">

    <strong>Transaksi baru:</strong>
    <input type="date" name="waktu_transaksi" value="<?= date('Y-m-d') ?>" required>

    <select name="pelanggan_id" required>
        <option value="">[pilih pelanggan]</option>
        <?php while ($row = $pelanggan->fetch_assoc()): ?>
            <option value="<?= e($row['id']) ?>"><?= e($row['nama']) ?></option>
        <?php endwhile; ?>
    </select>

    <input type="text" name="keterangan" placeholder="keterangan">
    <button type="submit" class="btn btn-success">Tambah Transaksi</button>
</form>

<h3>Detail: Barang pada Transaksi <?= $id > 0 ? '#' . $id : '' ?></h3>

<?php
if ($id === 0) {
    echo '<p class="muted">Klik tombol "Detail" pada salah satu transaksi di atas untuk menampilkan detailnya.</p>';
} else {
    $stmt = $db->prepare('SELECT * FROM transaksi WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $transaksi = $stmt->get_result()->fetch_assoc();

    if (!$transaksi) {
        echo '<p class="muted">Transaksi tidak ditemukan.</p>';
    } else {
        $sql = 'SELECT d.*, b.kode_barang, b.nama_barang
                FROM transaksi_detail d
                JOIN barang b ON b.id = d.barang_id
                WHERE d.transaksi_id = ?
                ORDER BY b.nama_barang';
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $detail = $stmt->get_result();

        $sql = 'SELECT id, kode_barang, nama_barang, harga
                FROM barang
                WHERE id NOT IN (SELECT barang_id FROM transaksi_detail WHERE transaksi_id = ?)
                ORDER BY nama_barang';
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $pilihan = $stmt->get_result();
?>

<table>
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Harga</th>
            <th>Qty</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php while ($row = $detail->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= e($row['kode_barang']) ?></td>
                <td><?= e($row['nama_barang']) ?></td>
                <td class="num"><?= rupiah($row['harga']) ?></td>
                <td class="num"><?= (int) $row['qty'] ?></td>
                <td class="num"><?= rupiah($row['harga'] * $row['qty']) ?></td>
            </tr>
        <?php endwhile; ?>

        <?php if ($no === 1): ?>
            <tr>
                <td colspan="6" class="muted">Belum ada detail barang.</td>
            </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="num">Total Transaksi</td>
            <td class="num"><?= rupiah($transaksi['total']) ?></td>
        </tr>
    </tfoot>
</table>

<?php if ($pilihan->num_rows === 0): ?>
    <p class="muted">Semua barang sudah dimasukkan ke transaksi ini.</p>
<?php else: ?>
    <form method="post" class="inline">
        <?= csrfField() ?>
        <input type="hidden" name="aksi" value="tambah_detail">
        <input type="hidden" name="transaksi_id" value="<?= $id ?>">

        <strong>Tambah detail:</strong>
        <select name="barang_id" required>
            <option value="">[pilih barang]</option>
            <?php while ($row = $pilihan->fetch_assoc()): ?>
                <option value="<?= (int) $row['id'] ?>">
                    <?= e($row['kode_barang'] . ' - ' . $row['nama_barang'] . ' (' . rupiah($row['harga']) . ')') ?>
                </option>
            <?php endwhile; ?>
        </select>

        <input type="number" name="qty" min="1" value="1" style="width: 90px;" required>
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
<?php endif; ?>

<?php
    }
}
?>

<?php include 'footer.php'; ?>
