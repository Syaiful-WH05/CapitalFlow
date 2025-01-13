<?php
include 'db_connect.php';

// Fetch data untuk total anggota
$result = $conn->query("SELECT COUNT(*) AS total FROM Anggota");
$total_anggota = $result->fetch_assoc()['total'] ?? 0;

// Fetch data untuk total bendahara
$result = $conn->query("SELECT COUNT(*) AS total FROM Bendahara");
$total_bendahara = $result->fetch_assoc()['total'] ?? 0;

// Fetch data untuk total transaksi
$result = $conn->query("SELECT COUNT(*) AS total FROM Transaksi");
$total_transaksi = $result->fetch_assoc()['total'] ?? 0;

// Fetch data untuk total saldo
$result = $conn->query("SELECT SUM(Jumlah) AS pemasukan FROM Transaksi WHERE Jenis_Transaksi='Pemasukan'");
$total_pemasukan = $result->fetch_assoc()['pemasukan'] ?? 0;

$result = $conn->query("SELECT SUM(Jumlah) AS pengeluaran FROM Transaksi WHERE Jenis_Transaksi='Pengeluaran'");
$total_pengeluaran = $result->fetch_assoc()['pengeluaran'] ?? 0;

$total_saldo = $total_pemasukan - $total_pengeluaran;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kas Keuangan</title>
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

    <!-- Open Sidebar Button -->
    <button class="open-btn" onclick="openSidebar()">☰ Menu</button>
    <div class="logo-background"></div>

    <div class="dashboard">
        <!-- Header -->
        <header>
            <h1>Dashboard Kas Keuangan</h1>
        </header>

        <!-- Statistik -->
        <div class="stats">
            <div id="anggota" class="stat-item anggota">
                <h2>Anggota</h2>
                <p><?= $total_anggota; ?> Orang</p>
            </div>
            <div id="bendahara" class="stat-item bendahara">
                <h2>Bendahara</h2>
                <p><?= $total_bendahara; ?> Orang</p>
            </div>
            <div id="transaksi" class="stat-item transaksi">
                <h2>Transaksi</h2>
                <p><?= $total_transaksi; ?> Transaksi</p>
            </div>
            <div id="total" class="stat-item saldo">
                <h2>Total Saldo</h2>
                <p>Rp <?= number_format($total_saldo, 2, ',', '.'); ?></p>
            </div>
        </div>

        <!-- Footer -->
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
