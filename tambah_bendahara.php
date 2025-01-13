<?php
include 'db_connect.php';

// Proses Tambah Bendahara
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_bendahara = $_POST['nama_bendahara'];
    $kontak_bendahara = $_POST['kontak_bendahara'];

    if (!empty($nama_bendahara) && !empty($kontak_bendahara)) {
        $stmt = $conn->prepare("INSERT INTO Bendahara (Nama_Bendahara, Kontak_Bendahara) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama_bendahara, $kontak_bendahara);

        if ($stmt->execute()) {
            $success = "Bendahara berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan bendahara.";
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
    <title>Tambah Bendahara</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sidebar Menu -->
    <div id="sidebar" class="sidebar">
        <a href="javascript:void(0)" class="close-btn" onclick="closeSidebar()">×</a>
        <ul>
            <li><a href="index.php" class="menu-link">Dashboard</a></li>
            <li><a href="anggota.php" class="menu-link">Anggota</a></li>
            <li><a href="bendahara.php" class="menu-link">Bendahara</a></li>
            <li><a href="transaksi.php" class="menu-link">Transaksi</a></li>
            <li><a href="data_keuangan.php" class="menu-link">Data Keuangan</a></li>
        </ul>
    </div>
    <button class="open-btn" onclick="openSidebar()">☰ Menu</button>
    <div class="logo-background"></div>
    <div class="dashboard">
        <header>
            <h1>Tambah Bendahara</h1>
        </header>
        <div class="tambah-transaksi-container">
        <a href="bendahara.php" class="btn tambah-transaksi">Kembali ke Daftar Bendahara</a>
        <div class="tambah-anggota">
            <!-- Pesan -->
            <?php if (isset($error)): ?>
                <p class="error"><?= $error; ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p class="success"><?= $success; ?></p>
            <?php endif; ?>

            <form action="tambah_bendahara.php" method="POST">
                <label for="nama_bendahara">Nama Bendahara</label>
                <input type="text" name="nama_bendahara" id="nama_bendahara" placeholder="Masukkan nama bendahara" required>

                <label for="kontak_bendahara">Kontak Bendahara</label>
                <input type="text" name="kontak_bendahara" id="kontak_bendahara" placeholder="Masukkan kontak bendahara" required>

                <button type="submit" class="submit-btn">Tambah Bendahara</button>
            </form>
        </div>
    </div>

    <script>
        function openSidebar() {
            document.getElementById("sidebar").style.width = "250px";
        }

        function closeSidebar() {
            document.getElementById("sidebar").style.width = "0";
        }
    </script>
</body>
</html>
