<?php
include 'db_connect.php';

// Proses tambah anggota
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_anggota = $_POST['id_anggota'];
    $nama_anggota = $_POST['nama_anggota'];

    // Validasi input
    if (empty($id_anggota) || empty($nama_anggota)) {
        $error = "ID Anggota dan Nama Anggota tidak boleh kosong.";
    } else {
        // Query untuk menambahkan data anggota
        $stmt = $conn->prepare("INSERT INTO Anggota (ID_Anggota, Nama_Anggota) VALUES (?, ?)");
        $stmt->bind_param("is", $id_anggota, $nama_anggota);

        if ($stmt->execute()) {
            $success = "Anggota berhasil ditambahkan.";
        } else {
            $error = "Gagal menambahkan anggota. Pastikan ID tidak duplikat.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Anggota</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <button class="close-btn" onclick="closeSidebar()">×</button>
        <ul>
        <li><a href="index.php" class="menu-link">Dashboard</a></li>
            <li><a href="anggota.php" class="menu-link">Anggota</a></li>
            <li><a href="tambah_anggota.php" class="menu-link sub-menu">Tambah Anggota</a></li>
            <li><a href="bendahara.php" class="menu-link">Bendahara</a></li>
            <li><a href="transaksi.php" class="menu-link">Transaksi</a></li>
            <li><a href="data_keuangan.php" class="menu-link">Data Keuangan</a></li>
        </ul>
    </div>
    <!-- Open Sidebar Button -->
    <button class="open-btn" onclick="openSidebar()">☰ Menu</button>
    <div class="logo-background"></div>
    <div class="dashboard">
        <header>
            <h1>Tambah Anggota</h1>
        </header>

        <div class="tambah-transaksi-container">
        <a href="anggota.php" class="btn tambah-transaksi">Kembali ke Data Anggota</a>
        </div>

        <!-- Form Tambah Anggota -->
        <div class="tambah-anggota">
            <?php if (isset($error)): ?>
                <p class="error"><?= $error; ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p class="success"><?= $success; ?></p>
            <?php endif; ?>

            <form action="tambah_anggota.php" method="POST">
                <label for="id_anggota">ID Anggota</label>
                <input type="number" name="id_anggota" id="id_anggota" required>

                <label for="nama_anggota">Nama Anggota</label>
                <input type="text" name="nama_anggota" id="nama_anggota" required>

                <button type="submit" class="submit-btn">Tambah Anggota</button>
            </form>
        </div>

        <footer>
            <p>&copy; <?= date('Y'); ?> Sistem Kas Keuangan</p>
        </footer>
    </div>

    <script>
        // Buka sidebar
        function openSidebar() {
            document.getElementById("sidebar").style.width = "250px";
        }

        // Tutup sidebar
        function closeSidebar() {
            document.getElementById("sidebar").style.width = "0";
        }
    </script>
</body>
</html>
