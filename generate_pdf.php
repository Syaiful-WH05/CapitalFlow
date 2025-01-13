<?php
require('fpdf186/fpdf.php');
include 'db_connect.php';
$where_clause = "1=1"; // Default untuk menampilkan semua data

if (!empty($search_query) && !empty($filter_by)) {
    $search_query = $conn->real_escape_string($search_query); // Hindari SQL injection
    $where_clause .= " AND $filter_by LIKE '%$search_query%'";
}

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

// Initialize PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Laporan Data Keuangan', 0, 1, 'C');
$pdf->Ln(10);

// Tambahkan Total Keseluruhan
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Total Keseluruhan: Rp ' . number_format($total_keseluruhan, 0, ',', '.'), 0, 1);

// Tambahkan Tabel
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 10, 'ID', 1);
$pdf->Cell(40, 10, 'Jenis', 1);
$pdf->Cell(40, 10, 'Jumlah', 1);
$pdf->Cell(30, 10, 'Tanggal', 1);
$pdf->Cell(30, 10, 'Kategori', 1);
$pdf->Cell(30, 10, 'Bendahara', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
while ($row = $semua_transaksi->fetch_assoc()) {
    $pdf->Cell(20, 10, $row['ID_Transaksi'], 1);
    $pdf->Cell(40, 10, $row['Jenis_Transaksi'], 1);
    $pdf->Cell(40, 10, 'Rp ' . number_format($row['Jumlah'], 0, ',', '.'), 1);
    $pdf->Cell(30, 10, $row['Tanggal'], 1);
    $pdf->Cell(30, 10, $row['Kategori'], 1);
    $pdf->Cell(30, 10, $row['Nama_Bendahara'] ?: '-', 1);
    $pdf->Ln();
}

// Output PDF
$pdf->Output('D', 'Laporan_Keuangan.pdf');
?>
