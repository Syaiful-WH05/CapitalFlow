<?php
$host = "localhost"; // Ganti dengan host database Anda
$user = "root";      // Username database
$pass = "";          // Password database
$dbname = "kaskeuangan"; // Nama database

// Membuat koneksi
$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
