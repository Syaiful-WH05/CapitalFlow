<?php
include 'db_connect.php';

// Ambil data anggota berdasarkan ID
if (isset($_GET['id'])) {
    $id_anggota = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM Anggota WHERE ID_Anggota = ?");
    $stmt->bind_param("i", $id_anggota);
    $stmt->execute();
    $result = $stmt->get_result();
    $anggota = $result->fetch_assoc();

    if (!$anggota) {
        $error = "Anggota dengan ID tersebut tidak ditemukan.";
    }
}

// Proses update anggota
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_anggota = $_POST['id_anggota'];
    $nama_anggota = $_POST['nama_anggota'];

    if (empty($id_anggota) || empty($nama_anggota)) {
        $error = "ID Anggota dan Nama Anggota tidak boleh kosong.";
    } else {
        $stmt = $conn->prepare("UPDATE Anggota SET Nama_Anggota = ? WHERE ID_Anggota = ?");
        $stmt->bind_param("si", $nama_anggota, $id_anggota);

        if ($stmt->execute()) {
            $success = "Data anggota berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui data anggota.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="logo-background"></div>
    <div class="dashboard">
        <header>
            <h1>Edit Anggota</h1>
        </header>

        <div class="tambah-transaksi-container">
            <a href="anggota.php" class="btn tambah-transaksi">Kembali ke Data Anggota</a>
        </div>

        <!-- Form Edit Anggota -->
        <div class="tambah-anggota">
            <?php if (isset($error)): ?>
                <p class="error"><?= $error; ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p class="success"><?= $success; ?></p>
            <?php endif; ?>

            <?php if (isset($anggota)): ?>
                <form action="edit_anggota.php?id=<?= $anggota['ID_Anggota']; ?>" method="POST">
                    <label for="id_anggota">ID Anggota</label>
                    <input type="number" name="id_anggota" id="id_anggota" value="<?= $anggota['ID_Anggota']; ?>" readonly>

                    <label for="nama_anggota">Nama Anggota</label>
                    <input type="text" name="nama_anggota" id="nama_anggota" value="<?= $anggota['Nama_Anggota']; ?>" required>

                    <button type="submit" class="submit-btn">Simpan Perubahan</button>
                </form>
            <?php else: ?>
                <p class="error">Data anggota tidak ditemukan.</p>
            <?php endif; ?>
        </div>

        <footer>
            <p>&copy; <?= date('Y'); ?> Sistem Kas Keuangan</p>
        </footer>
    </div>

</body>
</html>
