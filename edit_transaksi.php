<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id_transaksi = $_GET['id'];

    // Ambil data transaksi berdasarkan ID
    $query = $conn->prepare("SELECT * FROM Transaksi WHERE ID_Transaksi = ?");
    $query->bind_param("i", $id_transaksi);
    $query->execute();
    $result = $query->get_result();
    $transaksi = $result->fetch_assoc();

    if (!$transaksi) {
        die("Transaksi tidak ditemukan.");
    }
} else {
    die("ID Transaksi tidak ditemukan.");
}

// Proses update transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_bendahara = $_POST['id_bendahara'];
    $id_anggota = isset($_POST['id_anggota']) && $_POST['id_anggota'] !== "" ? $_POST['id_anggota'] : null;
    $jenis_transaksi = $_POST['jenis_transaksi'];
    $tanggal = $_POST['tanggal'];
    $kategori = $_POST['kategori'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    $update = $conn->prepare("
        UPDATE Transaksi
        SET ID_Bendahara = ?, ID_Anggota = ?, Jenis_Transaksi = ?, Tanggal = ?, Kategori = ?, Jumlah = ?, Keterangan = ?
        WHERE ID_Transaksi = ?
    ");
    $update->bind_param("iisssdsi", $id_bendahara, $id_anggota, $jenis_transaksi, $tanggal, $kategori, $jumlah, $keterangan, $id_transaksi);

    if ($update->execute()) {
        header("Location: transaksi.php");
        exit;
    } else {
        echo "Error: " . $update->error;
    }
}

// Query untuk dropdown Anggota dan Bendahara
$anggota = $conn->query("SELECT ID_Anggota, Nama_Anggota FROM Anggota");
$bendahara = $conn->query("SELECT ID_Bendahara, Nama_Bendahara FROM Bendahara");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="logo-background"></div>
    <div class="dashboard">
        <header>
            <h1>Edit Transaksi</h1>
        </header>
        <div class="tambah-transaksi-container">
            <a href="transaksi.php" class="btn tambah-transaksi">Kembali ke Daftar Transaksi</a>
        </div>
        <form action="" method="POST" class="form-transaksi">
            <label for="id_bendahara">Bendahara:</label>
            <select name="id_bendahara" id="id_bendahara" required>
                <?php while ($row = $bendahara->fetch_assoc()): ?>
                    <option value="<?= $row['ID_Bendahara']; ?>" <?= $row['ID_Bendahara'] == $transaksi['ID_Bendahara'] ? 'selected' : ''; ?>>
                        [<?= $row['ID_Bendahara']; ?>] <?= $row['Nama_Bendahara']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="jenis_transaksi">Jenis Transaksi:</label>
            <select name="jenis_transaksi" id="jenis_transaksi" required>
                <option value="Pemasukan" <?= $transaksi['Jenis_Transaksi'] == 'Pemasukan' ? 'selected' : ''; ?>>Pemasukan</option>
                <option value="Pengeluaran" <?= $transaksi['Jenis_Transaksi'] == 'Pengeluaran' ? 'selected' : ''; ?>>Pengeluaran</option>
            </select>

            <label for="id_anggota">Anggota:</label>
            <select name="id_anggota" id="id_anggota">
                <option value="" <?= is_null($transaksi['ID_Anggota']) ? 'selected' : ''; ?>>Tidak Ada</option>
                <?php while ($row = $anggota->fetch_assoc()): ?>
                    <option value="<?= $row['ID_Anggota']; ?>" <?= $row['ID_Anggota'] == $transaksi['ID_Anggota'] ? 'selected' : ''; ?>>
                        [<?= $row['ID_Anggota']; ?>] <?= $row['Nama_Anggota']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="kategori">Kategori:</label>
            <input type="text" name="kategori" id="kategori" value="<?= $transaksi['Kategori']; ?>" required>

            <label for="tanggal">Tanggal:</label>
            <input type="date" name="tanggal" id="tanggal" value="<?= $transaksi['Tanggal']; ?>" required>

            <label for="jumlah">Jumlah (Rp):</label>
            <input type="number" name="jumlah" id="jumlah" step="0.01" value="<?= $transaksi['Jumlah']; ?>" required>

            <label for="keterangan">Keterangan:</label>
            <textarea name="keterangan" id="keterangan" rows="4"><?= $transaksi['Keterangan']; ?></textarea>

            <button type="submit" class="submit-btn">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>
