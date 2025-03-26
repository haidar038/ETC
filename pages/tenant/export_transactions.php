<?php
include '../../config/config.php';
include '../../config/database.php';

// Pastikan hanya tenant yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'tenant') {
    header("Location: ../login.php");
    exit();
}

$tenant_id = $_SESSION['user_id'];

// Ambil transaksi yang terkait dengan tenant (misalnya transaksi pembelian)
$sql = "SELECT t.*, u.name AS visitor_name FROM transactions t 
        LEFT JOIN users u ON t.user_id = u.id 
        WHERE t.tenant_id = $tenant_id AND t.transaction_type = 'purchase'
        ORDER BY t.transaction_date DESC";
$result = $conn->query($sql);

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

$format = isset($_GET['format']) ? $_GET['format'] : 'excel';

if ($format == 'excel') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=tenant_transactions.csv');
    $output = fopen('php://output', 'w');
    // Header kolom
    fputcsv($output, array('ID', 'Visitor Name', 'Nominal (Rupiah)', 'Transaction Type', 'Status', 'QR Token', 'Transaction Date'));
    foreach ($transactions as $tran) {
        fputcsv($output, array(
            $tran['id'],
            $tran['visitor_name'],
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
    require_once('../../includes/fpdf/fpdf.php');
    class PDF extends FPDF
    {
        function Header()
        {
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(0, 10, 'Tenant Transactions Report', 0, 1, 'C');
            $this->Ln(5);
        }
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

    $header = array('ID', 'Visitor Name', 'Nominal (Rupiah)', 'Transaction Type', 'Status', 'QR Token', 'Transaction Date');
    $w = array(10, 40, 30, 30, 20, 40, 40);
    for ($i = 0; $i < count($header); $i++) {
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
    }
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 8);
    foreach ($transactions as $tran) {
        $pdf->Cell($w[0], 6, $tran['id'], 1);
        $pdf->Cell($w[1], 6, $tran['visitor_name'], 1);
        $pdf->Cell($w[2], 6, number_format($tran['nominal'], 0, ',', '.'), 1, 0, 'R');
        $pdf->Cell($w[3], 6, $tran['transaction_type'], 1);
        $pdf->Cell($w[4], 6, $tran['status'], 1);
        $pdf->Cell($w[5], 6, $tran['qr_token'], 1);
        $pdf->Cell($w[6], 6, $tran['transaction_date'], 1);
        $pdf->Ln();
    }
    $pdf->Output('D', 'tenant_transactions.pdf');
    exit();
} else {
    echo "Format tidak valid.";
}
