<?php
include 'db_connect.php';

if (isset($_GET['delete_id'])) {
    $id_bendahara = $_GET['delete_id'];

    if (filter_var($id_bendahara, FILTER_VALIDATE_INT)) {
        // Hapus data terkait di tabel Transaksi
        $conn->query("DELETE FROM Transaksi WHERE ID_Bendahara = $id_bendahara");

        // Hapus data di tabel Bendahara
        $stmt = $conn->prepare("DELETE FROM Bendahara WHERE ID_Bendahara = ?");
        $stmt->bind_param("i", $id_bendahara);

        if ($stmt->execute()) {
            $success = "Data bendahara berhasil dihapus.";
        } else {
            $error = "Gagal menghapus data bendahara. Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = "ID tidak valid.";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Bendahara</title>
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
            <li><a href="tambah_bendahara.php" class="menu-link sub-menu">Tambah Bendahara</a></li>
            <li><a href="transaksi.php" class="menu-link">Transaksi</a></li>
            <li><a href="data_keuangan.php" class="menu-link">Data Keuangan</a></li>
        </ul>
    </div>
    <!-- Open Sidebar Button -->
    <button class="open-btn" onclick="openSidebar()">☰ Menu</button>
    <div class="logo-background"></div>
    <div class="dashboard">
        <header>
            <h1>Data Bendahara</h1>
        </header>

        <div class="tambah-transaksi-container">
            <a href="tambah_bendahara.php" class="btn tambah-transaksi">Tambah Bendahara</a>
        </div>

        <!-- Notifikasi Sukses atau Error -->
        <?php if (isset($error)): ?>
            <p class="error"><?= $error; ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?= $success; ?></p>
        <?php endif; ?>

        <!-- Tabel Bendahara -->
        <table class="anggota-table">
            <thead>
                <tr>
                    <th>ID Bendahara</th>
                    <th>Nama Bendahara</th>
                    <th>Kontak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM Bendahara");
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['ID_Bendahara']); ?></td>
                        <td><?= htmlspecialchars($row['Nama_Bendahara']); ?></td>
                        <td><?= htmlspecialchars($row['Kontak_Bendahara']); ?></td>
                        <td>
                            <a href="edit_bendahara.php?id=<?= htmlspecialchars($row['ID_Bendahara']); ?>" class="btn btn-edit">Edit</a>
                            <a href="bendahara.php?delete_id=<?= htmlspecialchars($row['ID_Bendahara']); ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <footer>
            <p>&copy; <?= date('Y'); ?> Sistem Kas Keuangan</p>
        </footer>
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
