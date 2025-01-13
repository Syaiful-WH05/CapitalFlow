<?php
include 'db_connect.php'; // Sambungkan ke database

if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']); // Amankan data input
    $deleteQuery = "DELETE FROM Transaksi WHERE ID_Transaksi = $id";

    if ($conn->query($deleteQuery) === TRUE) {
        echo "<script>alert('Transaksi berhasil dihapus'); window.location.href = 'transaksi.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus transaksi: " . $conn->error . "');</script>";
    }
}


// Menampilkan notifikasi
$status_message = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'deleted') {
        $status_message = "<p class='notif success'>Transaksi berhasil dihapus.</p>";
    } elseif ($_GET['status'] == 'error') {
        $status_message = "<p class='notif error'>Terjadi kesalahan saat menghapus transaksi.</p>";
    }
}

// Query untuk pemasukan dan pengeluaran
$pemasukan = $conn->query("
    SELECT t.ID_Transaksi, t.Jenis_Transaksi, t.Jumlah, t.Tanggal, t.Kategori, t.Keterangan,
           a.Nama_Anggota, b.Nama_Bendahara
    FROM Transaksi t
    LEFT JOIN Anggota a ON t.ID_Anggota = a.ID_Anggota
    JOIN Bendahara b ON t.ID_Bendahara = b.ID_Bendahara
    WHERE t.Jenis_Transaksi = 'Pemasukan'
    ORDER BY t.Tanggal DESC
");

$pengeluaran = $conn->query("
    SELECT t.ID_Transaksi, t.Jenis_Transaksi, t.Jumlah, t.Tanggal, t.Kategori, t.Keterangan,
           a.Nama_Anggota, b.Nama_Bendahara
    FROM Transaksi t
    LEFT JOIN Anggota a ON t.ID_Anggota = a.ID_Anggota
    JOIN Bendahara b ON t.ID_Bendahara = b.ID_Bendahara
    WHERE t.Jenis_Transaksi = 'Pengeluaran'
    ORDER BY t.Tanggal DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi</title>
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
            <h1>Daftar Transaksi</h1>
        </header>

        <!-- Notifikasi -->
        <?= $status_message; ?>

        <!-- Tombol Tambah Transaksi -->
        <div class="tambah-transaksi-container">
            <a href="tambah_transaksi.php" class="btn tambah-transaksi">Tambah Transaksi</a>
        </div>

        <!-- Tabel Pemasukan -->
        <h2>Pemasukan</h2>
        <table class="anggota-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Nama Anggota</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th>Bendahara</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $pemasukan->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['ID_Transaksi']; ?></td>
                        <td>Rp <?= number_format($row['Jumlah'], 0, ',', '.'); ?></td>
                        <td><?= $row['Tanggal']; ?></td>
                        <td><?= $row['Nama_Anggota'] ?: 'Tidak Ada'; ?></td>
                        <td><?= $row['Kategori']; ?></td>
                        <td><?= $row['Keterangan'] ?: '-'; ?></td>
                        <td><?= $row['Nama_Bendahara']; ?></td>
                        <td>
                            <a href="edit_transaksi.php?id=<?= $row['ID_Transaksi']; ?>" class="btn btn-edit">Edit</a>
                            <a href="transaksi.php?delete_id=<?= $row['ID_Transaksi']; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus transaksi ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Tabel Pengeluaran -->
        <h2>Pengeluaran</h2>
        <table class="anggota-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Nama Anggota</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th>Bendahara</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $pengeluaran->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['ID_Transaksi']; ?></td>
                        <td>Rp <?= number_format($row['Jumlah'], 0, ',', '.'); ?></td>
                        <td><?= $row['Tanggal']; ?></td>
                        <td><?= $row['Nama_Anggota'] ?: 'Tidak Ada'; ?></td>
                        <td><?= $row['Kategori']; ?></td>
                        <td><?= $row['Keterangan'] ?: '-'; ?></td>
                        <td><?= $row['Nama_Bendahara']; ?></td>
                        <td>
                            <a href="edit_transaksi.php?id=<?= $row['ID_Transaksi']; ?>" class="btn btn-edit">Edit</a>
                            <a href="transaksi.php?delete_id=<?= $row['ID_Transaksi']; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus transaksi ini?')">Hapus</a>

                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
