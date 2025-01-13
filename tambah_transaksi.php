<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_anggota = isset($_POST['id_anggota']) && $_POST['id_anggota'] !== "" ? $_POST['id_anggota'] : null;
    $id_bendahara = $_POST['id_bendahara'];
    $jenis_transaksi = $_POST['jenis_transaksi'];
    $tanggal = $_POST['tanggal'];
    $kategori = $_POST['kategori'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    // Validasi jika kategori adalah "Iuran", ID_Anggota harus diisi
    if ($kategori === 'Iuran' && $id_anggota === null) {
        die("Kategori 'Iuran' memerlukan ID Anggota.");
    }

    // Persiapan query berdasarkan kategori
    if ($kategori === 'Iuran') {
        $stmt = $conn->prepare("INSERT INTO Transaksi (ID_Anggota, ID_Bendahara, Jenis_Transaksi, Tanggal, Kategori, Jumlah, Keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssds", $id_anggota, $id_bendahara, $jenis_transaksi, $tanggal, $kategori, $jumlah, $keterangan);
    } else {
        $stmt = $conn->prepare("INSERT INTO Transaksi (ID_Anggota, ID_Bendahara, Jenis_Transaksi, Tanggal, Kategori, Jumlah, Keterangan) VALUES (NULL, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssds", $id_bendahara, $jenis_transaksi, $tanggal, $kategori, $jumlah, $keterangan);
    }

    // Eksekusi query
    if ($stmt->execute()) {
        header("Location: transaksi.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
        exit;
    }
}

// Query data untuk form
$anggota = $conn->query("SELECT ID_Anggota, Nama_Anggota FROM Anggota");
$bendahara = $conn->query("SELECT ID_Bendahara, Nama_Bendahara FROM Bendahara");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi</title>
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
    <div class="logo-background"></div>

    <!-- Formulir Tambah Transaksi -->
    <div class="dashboard">
        <header>
            <h1 class="page-title">Tambah Transaksi</h1>
        </header>

        <div class="tambah-transaksi-container">
            <a href="transaksi.php" class="btn tambah-transaksi">Kembali ke Daftar Transaksi</a>
        </div>

        <form action="" method="POST" class="form-transaksi">
            <label for="id_bendahara">Bendahara:</label>
            <select name="id_bendahara" id="id_bendahara" required>
                <option value="" disabled selected>Pilih Bendahara</option>
                <?php while ($row = $bendahara->fetch_assoc()): ?>
                    <option value="<?= $row['ID_Bendahara']; ?>">[<?= $row['ID_Bendahara']; ?>] <?= $row['Nama_Bendahara']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="jenis_transaksi">Jenis Transaksi:</label>
            <select name="jenis_transaksi" id="jenis_transaksi" required onchange="updateForm()">
                <option value="" disabled selected>Pilih Jenis Transaksi</option>
                <option value="Pemasukan">Pemasukan</option>
                <option value="Pengeluaran">Pengeluaran</option>
            </select>

            <div id="anggota-group" style="display: none;">
                <label for="id_anggota">Anggota (Iuran):</label>
                <select name="id_anggota" id="id_anggota">
                    <option value="" disabled selected>Pilih Anggota</option>
                    <?php while ($row = $anggota->fetch_assoc()): ?>
                        <option value="<?= $row['ID_Anggota']; ?>">[<?= $row['ID_Anggota']; ?>] <?= $row['Nama_Anggota']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <label for="kategori">Kategori:</label>
            <select name="kategori" id="kategori" required>
                <option value="" disabled selected>Pilih Kategori</option>
            </select>

            <label for="tanggal">Tanggal:</label>
            <input type="date" name="tanggal" id="tanggal" required>

            <label for="jumlah">Jumlah (Rp):</label>
            <input type="number" name="jumlah" id="jumlah" step="0.01" required>

            <label for="keterangan">Keterangan:</label>
            <textarea name="keterangan" id="keterangan" rows="4"></textarea>

            <button type="submit" class="submit-btn">Tambah Transaksi</button>
        </form>
    </div>

    <script>
        function updateForm() {
            const jenisTransaksi = document.getElementById('jenis_transaksi').value;
            const kategori = document.getElementById('kategori');
            const anggotaGroup = document.getElementById('anggota-group');

            // Reset kategori options
            kategori.innerHTML = '<option value="" disabled selected>Pilih Kategori</option>';
            
            if (jenisTransaksi === 'Pemasukan') {
                kategori.innerHTML += `
                    <option value="Iuran">Iuran</option>
                    <option value="Sumbangan">Sumbangan</option>
                    <option value="Hasil Kegiatan">Hasil Kegiatan</option>
                    <option value="Donasi">Donasi</option>
                `;
            } else if (jenisTransaksi === 'Pengeluaran') {
                kategori.innerHTML += `
                    <option value="Kegiatan Kelas">Kegiatan Kelas</option>
                    <option value="Perlengkapan">Perlengkapan</option>
                    <option value="Administrasi">Administrasi</option>
                    <option value="Dana Sosial">Dana Sosial</option>
                    <option value="Biaya Operasional">Biaya Operasional</option>
                `;
            }

            // Sembunyikan field anggota jika jenis transaksi berubah
            anggotaGroup.style.display = 'none';
        }

        document.getElementById('kategori').addEventListener('change', function () {
            const kategori = document.getElementById('kategori').value;
            const anggotaGroup = document.getElementById('anggota-group');

            if (kategori === 'Iuran') {
                anggotaGroup.style.display = 'block';
            } else {
                anggotaGroup.style.display = 'none';
                document.getElementById('id_anggota').value = ""; // Reset ID Anggota
            }
        });

    </script>
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
