<?php
include 'db_connect.php';

// Ambil data bendahara berdasarkan ID
if (isset($_GET['id'])) {
    $id_bendahara = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM Bendahara WHERE ID_Bendahara = ?");
    $stmt->bind_param("i", $id_bendahara);
    $stmt->execute();
    $result = $stmt->get_result();
    $bendahara = $result->fetch_assoc();
}

// Proses Edit Bendahara
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_bendahara = $_POST['id_bendahara'];
    $nama_bendahara = $_POST['nama_bendahara'];
    $kontak_bendahara = $_POST['kontak_bendahara'];

    if (!empty($nama_bendahara) && !empty($kontak_bendahara)) {
        $stmt = $conn->prepare("UPDATE Bendahara SET Nama_Bendahara = ?, Kontak_Bendahara = ? WHERE ID_Bendahara = ?");
        $stmt->bind_param("ssi", $nama_bendahara, $kontak_bendahara, $id_bendahara);

        if ($stmt->execute()) {
            $success = "Bendahara berhasil diperbarui!";
            header("Location: bendahara.php");
            exit();
        } else {
            $error = "Gagal memperbarui data.";
        }
    } else {
        $error = "Semua field harus diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bendahara</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="logo-background"></div>
    <div class="dashboard">
        <header>
            <h1>Edit Bendahara</h1>
        </header>

        <div class="tambah-transaksi-container">
        <a href="bendahara.php" class="btn tambah-transaksi">Kembali ke Data Bendahara</a>
        </div>

        <div class="tambah-anggota">
            <!-- Pesan -->
            <?php if (isset($error)): ?>
                <p class="error"><?= $error; ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p class="success"><?= $success; ?></p>
            <?php endif; ?>

            <form action="edit_bendahara.php?id=<?= $bendahara['ID_Bendahara']; ?>" method="POST">
                <input type="hidden" name="id_bendahara" value="<?= $bendahara['ID_Bendahara']; ?>">

                <label for="nama_bendahara">Nama Bendahara</label>
                <input type="text" name="nama_bendahara" id="nama_bendahara" value="<?= $bendahara['Nama_Bendahara']; ?>" required>

                <label for="kontak_bendahara">Kontak Bendahara</label>
                <input type="text" name="kontak_bendahara" id="kontak_bendahara" value="<?= $bendahara['Kontak_Bendahara']; ?>" required>

                <button type="submit" class="submit-btn">Perbarui</button>
            </form>
        </div>
    </div>

</html>
