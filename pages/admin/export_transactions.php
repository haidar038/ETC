<?php
include '../../config/config.php';
include '../../config/database.php';
session_start();

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Ambil data transaksi beserta nama user (visitor) dari tabel users
$sql = "SELECT t.*, u.name AS user_name FROM transactions t LEFT JOIN users u ON t.user_id = u.id ORDER BY t.transaction_date DESC";
$result = $conn->query($sql);

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

$format = isset($_GET['format']) ? $_GET['format'] : 'excel'; // default export ke Excel

if ($format == 'excel') {
    // Export sebagai CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=transactions.csv');
    $output = fopen('php://output', 'w');
    // Tulis header kolom
    fputcsv($output, array('ID', 'User Name', 'Tenant ID', 'Nominal (Rupiah)', 'Transaction Type', 'Status', 'QR Token', 'Transaction Date'));
    foreach ($transactions as $tran) {
        fputcsv($output, array(
            $tran['id'],
            $tran['user_name'],
            $tran['tenant_id'],
            $tran['nominal'],
            $tran['transaction_type'],
            $tran['status'],
            $tran['qr_token'],
            $tran['transaction_date']
        ));
    }
    fclose($output);
    exit();
} elseif ($format == 'pdf') {
    // Export sebagai PDF menggunakan FPDF
    require_once('../../includes/fpdf/fpdf.php');
    class PDF extends FPDF
    {
        // Header
        function Header()
        {
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(0, 10, 'Transactions Report', 0, 1, 'C');
            $this->Ln(5);
        }
        // Footer
        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }
    }
    $pdf = new PDF('L');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 10);

    // Header kolom
    $header = array('ID', 'User Name', 'Tenant ID', 'Nominal (Rupiah)', 'Transaction Type', 'Status', 'QR Token', 'Transaction Date');
    $w = array(10, 40, 20, 30, 30, 20, 40, 40);
    for ($i = 0; $i < count($header); $i++) {
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
    }
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 8);
    foreach ($transactions as $tran) {
        $pdf->Cell($w[0], 6, $tran['id'], 1);
        $pdf->Cell($w[1], 6, $tran['user_name'], 1);
        $pdf->Cell($w[2], 6, $tran['tenant_id'], 1);
        $pdf->Cell($w[3], 6, number_format($tran['nominal'], 0, ',', '.'), 1, 0, 'R');
        $pdf->Cell($w[4], 6, $tran['transaction_type'], 1);
        $pdf->Cell($w[5], 6, $tran['status'], 1);
        $pdf->Cell($w[6], 6, $tran['qr_token'], 1);
        $pdf->Cell($w[7], 6, $tran['transaction_date'], 1);
        $pdf->Ln();
    }
    $pdf->Output('D', 'transactions.pdf');
    exit();
} else {
    echo "Format tidak valid.";
}
