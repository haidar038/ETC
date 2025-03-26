<?php
// pages/tenant/statistik.php

include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';

// Pastikan hanya tenant yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'tenant') {
    header("Location: ../login.php");
    exit();
}

$tenant_id = $_SESSION['user_id'];

// Jika parameter export disediakan, lakukan export data untuk tenant
if (isset($_GET['export'])) {
    $exportFormat = $_GET['export'];
    // Ambil transaksi pembelian yang diterima oleh tenant
    $sql = "SELECT t.*, u.name AS visitor_name 
            FROM transactions t 
            LEFT JOIN users u ON t.user_id = u.id 
            WHERE t.tenant_id = $tenant_id AND t.transaction_type = 'purchase'
            ORDER BY t.transaction_date DESC";
    $result = $conn->query($sql);
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    if ($exportFormat == 'excel') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=transaksi_tenant.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Visitor Name', 'Nominal', 'Type', 'Status', 'QR Token', 'Transaction Date']);
        foreach ($transactions as $tran) {
            fputcsv($output, [
                $tran['id'],
                $tran['visitor_name'],
                $tran['nominal'],
                $tran['transaction_type'],
                $tran['status'],
                $tran['qr_token'],
                $tran['transaction_date']
            ]);
        }
        fclose($output);
        exit();
    } elseif ($exportFormat == 'pdf') {
        require_once('../../includes/fpdf/fpdf.php');
        class PDF extends FPDF
        {
            function Header()
            {
                $this->SetFont('Arial', 'B', 15);
                $this->Cell(0, 10, 'Laporan Transaksi Tenant', 0, 1, 'C');
                $this->Ln(5);
            }
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
            }
        }
        $pdf = new PDF('L');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 10);
        $header = ['ID', 'Visitor Name', 'Nominal', 'Type', 'Status', 'QR Token', 'Transaction Date'];
        $w = [15, 40, 30, 30, 25, 40, 40];
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
        $pdf->Output('D', 'transaksi_tenant.pdf');
        exit();
    } else {
        echo "Format export tidak valid.";
        exit();
    }
}

// Tampilkan statistik untuk tenant
$sql = "SELECT 
            COUNT(*) AS total_transaksi, 
            SUM(nominal) AS total_nominal
        FROM transactions
        WHERE tenant_id = $tenant_id AND transaction_type = 'purchase'";
$result = $conn->query($sql);
$stats = $result->fetch_assoc();
?>

<h2>Statistik Transaksi Tenant</h2>
<div class="mb-4">
    <p><strong>Total Transaksi:</strong> <?= $stats['total_transaksi']; ?></p>
    <p><strong>Total Nominal (Rp):</strong> Rp <?= number_format($stats['total_nominal'], 0, ',', '.'); ?></p>
</div>

<div class="mb-4">
    <a href="?export=excel" class="btn btn-success">Export ke Excel</a>
    <a href="?export=pdf" class="btn btn-danger">Export ke PDF</a>
</div>

<?php include '../../templates/footer.php'; ?>