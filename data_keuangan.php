<?php
include 'db_connect.php';

// Inisialisasi variabel filter
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$filter_by = isset($_GET['filter_by']) ? $_GET['filter_by'] : '';
$where_clause = "1=1"; // Default untuk menampilkan semua data

if (!empty($search_query) && !empty($filter_by)) {
    $search_query = $conn->real_escape_string($search_query); // Hindari SQL injection
    $where_clause .= " AND $filter_by LIKE '%$search_query%'";
}


// Query untuk transaksi bulanan
$transaksi_bulanan = $conn->query("
    SELECT t.ID_Transaksi, t.Jenis_Transaksi, t.Jumlah, t.Tanggal, t.Kategori, t.Keterangan,
           a.Nama_Anggota, b.Nama_Bendahara
    FROM Transaksi t
    LEFT JOIN Anggota a ON t.ID_Anggota = a.ID_Anggota
    LEFT JOIN Bendahara b ON t.ID_Bendahara = b.ID_Bendahara
    WHERE DATE_FORMAT(t.Tanggal, '%Y-%m') = '$filter_bulan'
    ORDER BY t.Tanggal ASC
");


// Total transaksi bulanan
$total_bulanan = $conn->query("
    SELECT 
        SUM(CASE WHEN Jenis_Transaksi = 'Pemasukan' THEN Jumlah ELSE 0 END) - 
        SUM(CASE WHEN Jenis_Transaksi = 'Pengeluaran' THEN Jumlah ELSE 0 END) AS Total
    FROM Transaksi
    WHERE DATE_FORMAT(Tanggal, '%Y-%m') = '$filter_bulan'
")->fetch_assoc()['Total'] ?: 0;


$semua_transaksi = $conn->query("
    SELECT t.ID_Transaksi, t.Jenis_Transaksi, t.Jumlah, t.Tanggal, t.Kategori, t.Keterangan,
           a.Nama_Anggota, b.Nama_Bendahara
    FROM Transaksi t
    LEFT JOIN Anggota a ON t.ID_Anggota = a.ID_Anggota
    LEFT JOIN Bendahara b ON t.ID_Bendahara = b.ID_Bendahara
    WHERE $where_clause
    ORDER BY t.Tanggal ASC
");


// Total keseluruhan transaksi
$total_keseluruhan = $conn->query("
    SELECT 
        SUM(CASE WHEN Jenis_Transaksi = 'Pemasukan' THEN Jumlah ELSE 0 END) - 
        SUM(CASE WHEN Jenis_Transaksi = 'Pengeluaran' THEN Jumlah ELSE 0 END) AS Total
    FROM Transaksi
")->fetch_assoc()['Total'] ?: 0;


// Query untuk daftar bulan transaksi
$daftar_bulan = $conn->query("
    SELECT DISTINCT DATE_FORMAT(Tanggal, '%Y-%m') AS Bulan
    FROM Transaksi
    ORDER BY Bulan DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Keuangan</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .total-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #ff9800;
            border: 1px solid #ddd;
            border-radius: 3px;
            max-width: 500px; 
            margin: 0 auto; 
        }
        .total-section h2 {
            margin: 0;
            font-size: 18px;
        }
        .total-section p {
            margin: 5px 0 0;
            font-size: 16px;
            color: #333;
        }
    </style>
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
        <header>
            <h1>Data Keuangan</h1>
        </header>
        <header>
            <div class="total-section">
                <h2>Total Keseluruhan</h2>
                <p>Rp <?= number_format($total_keseluruhan, 0, ',', '.'); ?></p>
            </div>
        </header>

        <!-- Form Filter Bulanan -->
        <section>
            <h2>Transaksi Bulanan</h2>
            <form method="GET" class="filter-form">
                <label for="bulan">Pilih Bulan:</label>
                <select name="bulan" id="bulan">
                    <option value="">-- Semua Bulan --</option>
                    <?php while ($row = $daftar_bulan->fetch_assoc()): ?>
                        <option value="<?= $row['Bulan']; ?>" <?= $row['Bulan'] === $filter_bulan ? 'selected' : ''; ?>>
                            <?= $row['Bulan']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">Filter</button>
            </form>

            <!-- Tabel Transaksi Bulanan -->
            <table class="anggota-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                        <th>Nama Anggota</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th>Bendahara</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($transaksi_bulanan->num_rows > 0): ?>
                        <?php while ($row = $transaksi_bulanan->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['ID_Transaksi']; ?></td>
                                <td><?= $row['Jenis_Transaksi']; ?></td>
                                <td>Rp <?= number_format($row['Jumlah'], 0, ',', '.'); ?></td>
                                <td><?= $row['Tanggal']; ?></td>
                                <td><?= $row['Nama_Anggota'] ?: '-'; ?></td>
                                <td><?= $row['Kategori']; ?></td>
                                <td><?= $row['Keterangan'] ?: '-'; ?></td>
                                <td><?= $row['Nama_Bendahara']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8">Tidak ada transaksi untuk bulan ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if ($filter_bulan): ?>
                <div class="total-section">
                    <h2>Total Transaksi Bulan <?= $filter_bulan; ?></h2>
                    <p>Rp <?= number_format($total_bulanan, 0, ',', '.'); ?></p>
                </div>
            <?php endif; ?>

        </section>

        <!-- Form Pencarian -->
        <section>
            <h2>Semua Transaksi</h2>
            <form method="GET" class="search-form">
                <label for="search">Pencarian:</label>
                <input type="text" name="search" id="search" value="<?= $search_query; ?>" placeholder="Masukkan kata kunci">
                <label for="filter_by">Berdasarkan:</label>
                <select name="filter_by" id="filter_by">
                    <option value="">-- Pilih Filter --</option>
                    <option value="a.Nama_Anggota" <?= $filter_by === 'a.Nama_Anggota' ? 'selected' : ''; ?>>Anggota</option>
                    <option value="t.Jenis_Transaksi" <?= $filter_by === 't.Jenis_Transaksi' ? 'selected' : ''; ?>>Jenis Transaksi</option>
                    <option value="b.Nama_Bendahara" <?= $filter_by === 'b.Nama_Bendahara' ? 'selected' : ''; ?>>Bendahara</option>
                    <option value="t.Kategori" <?= $filter_by === 't.Kategori' ? 'selected' : ''; ?>>Kategori</option>
                </select>
                <button type="submit">Cari</button>
            </form>

            <!-- Tabel Semua Transaksi -->
            <table class="anggota-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                        <th>Nama Anggota</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th>Bendahara</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($semua_transaksi->num_rows > 0): ?>
                        <?php while ($row = $semua_transaksi->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['ID_Transaksi']; ?></td>
                                <td><?= $row['Jenis_Transaksi']; ?></td>
                                <td>Rp <?= number_format($row['Jumlah'], 0, ',', '.'); ?></td>
                                <td><?= $row['Tanggal']; ?></td>
                                <td><?= $row['Nama_Anggota'] ?: '-'; ?></td>
                                <td><?= $row['Kategori']; ?></td>
                                <td><?= $row['Keterangan'] ?: '-'; ?></td>
                                <td><?= $row['Nama_Bendahara']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8">Tidak ada transaksi ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <!-- Tombol Download PDF -->
            <form action="generate_pdf.php" method="POST">
            <button type="submit">Download PDF</button>
            </form>
        </section>
    </div>
    <script>
    function openSidebar() {
        document.getElementById("sidebar").style.width = "250px"; // Lebar sidebar ketika terbuka
    }

    function closeSidebar() {
        document.getElementById("sidebar").style.width = "0"; // Lebar sidebar ketika tertutup
    }
</script>

</body>
</html>
