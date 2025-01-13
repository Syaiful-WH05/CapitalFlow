<?php
include 'db_connect.php';

// Tambah Data Anggota
if (isset($_POST['add'])) {
    $nama_anggota = $_POST['nama_anggota'];
    $stmt = $conn->prepare("INSERT INTO Anggota (Nama_Anggota) VALUES (?)");
    $stmt->bind_param("s", $nama_anggota);
    $stmt->execute();
    header("Location: anggota.php");
    exit();
}


if (isset($_GET['delete'])) {
    $id_anggota = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM Anggota WHERE ID_Anggota = ?");
    $stmt->bind_param("i", $id_anggota);
    
    if ($stmt->execute()) {
        echo "<script>alert('Data anggota berhasil dihapus!'); window.location.href = 'anggota.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data anggota: " . $conn->error . "'); window.location.href = 'anggota.php';</script>";
    }
    $stmt->close();
    exit();
}


// Update Data Anggota
if (isset($_POST['update'])) {
    $id_anggota = $_POST['id_anggota'];
    $nama_anggota = $_POST['nama_anggota'];
    $stmt = $conn->prepare("UPDATE Anggota SET Nama_Anggota = ? WHERE ID_Anggota = ?");
    $stmt->bind_param("si", $nama_anggota, $id_anggota);
    $stmt->execute();
    header("Location: anggota.php");
    exit();
}

// Ambil semua data anggota
$query = "SELECT * FROM Anggota";
$result = $conn->query($query);

if (!$result) {
    die("Query gagal: " . $conn->error); // Debugging jika query gagal
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Anggota</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sidebar Menu -->
    <div id="sidebar" class="sidebar">
        <a href="javascript:void(0)" class="close-btn" onclick="closeSidebar()">×</a>
        <ul>
            <li><a href="index.php" class="menu-link">Dashboard</a></li>
            <li><a href="anggota.php" class="menu-link">Anggota</a></li>
            <li><a href="tambah_anggota.php" class="menu-link sub-menu">Tambah Anggota</a></li>
            <li><a href="bendahara.php" class="menu-link">Bendahara</a></li>
            <li><a href="transaksi.php" class="menu-link">Transaksi</a></li>
            <li><a href="data_keuangan.php" class="menu-link">Data Keuangan</a></li>
        </ul>
    </div>

    <!-- Tombol untuk membuka sidebar -->
    <button class="open-btn" onclick="openSidebar()">☰ Menu</button>
    <div class="logo-background"></div>
    <!-- Konten Halaman -->
    <div class="dashboard">
        <header>
            <h1>Kelola Anggota</h1>
        </header>
        
        <div class="tambah-transaksi-container">
        <a href="tambah_anggota.php" class="btn tambah-transaksi">Tambah Anggota</a>
        </div>

        <div class="container">
            <!-- Tabel Anggota -->
            <h2>Daftar Anggota</h2>
            <table class="anggota-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Anggota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['ID_Anggota']; ?></td>
                        <td><?= $row['Nama_Anggota']; ?></td>
                        <td>
                            <a href="edit_anggota.php?id=<?= $row['ID_Anggota']; ?>" class="btn btn-edit">Edit</a>
                            <a href="anggota.php?delete=<?= $row['ID_Anggota']; ?>" class="btn btn-delete" onclick="return confirm('Hapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Fungsi untuk membuka sidebar
        function openSidebar() {
            document.getElementById("sidebar").style.width = "250px";
        }

        // Fungsi untuk menutup sidebar
        function closeSidebar() {
            document.getElementById("sidebar").style.width = "0";
        }
    </script>
</body>
</html>
