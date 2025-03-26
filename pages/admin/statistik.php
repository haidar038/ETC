<?php
// Ensure no output before PDF generation
ob_clean();
ob_start();

include '../../config/config.php';
include '../../config/database.php';

// Disable any HTML output in the header
define('NO_OUTPUT', true);
include '../../templates/header.php';

// Ensure only admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Function to sanitize and validate export format
function validateExportFormat($format)
{
    $allowedFormats = ['excel', 'pdf'];
    return in_array($format, $allowedFormats) ? $format : null;
}

// Function to fetch transaction statistics
function getTransactionStatistics($conn)
{
    $sql = "SELECT 
                COUNT(*) AS total_transaksi, 
                SUM(CASE WHEN transaction_type = 'topup_mandiri' THEN 1 ELSE 0 END) AS total_topup, 
                SUM(CASE WHEN transaction_type = 'purchase' THEN 1 ELSE 0 END) AS total_pembelian,
                SUM(CASE WHEN status = 'completed' THEN nominal ELSE 0 END) AS total_nominal_completed,
                SUM(CASE WHEN status = 'verified' THEN nominal ELSE 0 END) AS total_nominal_verified,
                SUM(CASE WHEN status = 'pending' THEN nominal ELSE 0 END) AS total_nominal_pending,
                SUM(nominal) AS total_nominal,
                AVG(nominal) AS avg_transaction_value
            FROM transactions";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Function to prepare transaction data for export
function getTransactionData($conn)
{
    $sql = "SELECT t.*, u.name AS user_name, tenant.name AS tenant_name 
            FROM transactions t 
            LEFT JOIN users u ON t.user_id = u.id 
            LEFT JOIN users tenant ON t.tenant_id = tenant.id
            ORDER BY t.transaction_date DESC";
    $result = $conn->query($sql);
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    return $transactions;
}

// Handle export functionality
if (isset($_GET['export'])) {
    $exportFormat = validateExportFormat($_GET['export']);
    $transactions = getTransactionData($conn);

    if ($exportFormat == 'excel') {
        // Clear any previous output
        ob_clean();

        // CSV Export
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=transaksi_admin_' . date('YmdHis') . '.csv');
        $output = fopen('php://output', 'w');

        // Enhanced CSV headers
        fputcsv($output, [
            'ID',
            'User Name',
            'Tenant Name',
            'Nominal',
            'Transaction Type',
            'Status',
            'QR Token',
            'Transaction Date',
            'Processed Date'
        ]);

        foreach ($transactions as $tran) {
            fputcsv($output, [
                $tran['id'],
                $tran['user_name'],
                $tran['tenant_name'] ?? '-',
                number_format($tran['nominal'], 2, '.', ''),
                $tran['transaction_type'],
                $tran['status'],
                $tran['qr_token'] ?? '-',
                $tran['transaction_date'],
                $tran['processed_date'] ?? '-'
            ]);
        }
        fclose($output);
        exit();
    } elseif ($exportFormat == 'pdf') {
        // Clear any previous output
        ob_clean();

        // PDF Export with FPDF
        require_once '../../includes/fpdf/fpdf.php';

        class TransactionPDF extends FPDF
        {
            function Header()
            {
                $this->SetFont('Arial', 'B', 15);
                $this->Cell(0, 10, 'Detailed Transaction Report', 0, 1, 'C');
                $this->SetFont('Arial', 'I', 10);
                $this->Cell(0, 10, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 1, 'C');
                $this->Ln(5);
            }

            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
            }
        }

        $pdf = new TransactionPDF('P', 'mm', 'A4');
        $pdf->SetTitle('Transaction Report');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        // Table headers
        $headers = ['ID', 'User', 'Tenant', 'Nominal', 'Type', 'Status', 'QR Token', 'Transaction Date'];
        $widths = [15, 30, 30, 25, 25, 25, 40, 40];

        $pdf->SetFont('Arial', 'B', 10);
        for ($i = 0; $i < count($headers); $i++) {
            $pdf->Cell($widths[$i], 7, $headers[$i], 1, 0, 'C');
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 8);
        foreach ($transactions as $tran) {
            $pdf->Cell($widths[0], 6, $tran['id'], 1);
            $pdf->Cell($widths[1], 6, $tran['user_name'], 1);
            $pdf->Cell($widths[2], 6, $tran['tenant_name'] ?? '-', 1);
            $pdf->Cell($widths[3], 6, number_format($tran['nominal'], 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[4], 6, $tran['transaction_type'], 1);
            $pdf->Cell($widths[5], 6, $tran['status'], 1);
            $pdf->Cell($widths[6], 6, $tran['qr_token'] ?? '-', 1);
            $pdf->Cell($widths[7], 6, $tran['transaction_date'], 1);
            $pdf->Ln();
        }

        // Directly output the PDF
        $pdf->Output('F', 'transaction_report_' . date('YmdHis') . '.pdf');

        // Force download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="transaction_report_' . date('YmdHis') . '.pdf"');
        readfile('transaction_report_' . date('YmdHis') . '.pdf');
        exit();
    }
}

// Fetch transaction statistics
$stats = getTransactionStatistics($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Transaction Statistics</title>
    <style>
        .stat-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h2 class="mb-4">Transaction Statistics</h2>

        <div class="row">
            <div class="col-md-4">
                <div class="stat-card">
                    <h5>Transaction Overview</h5>
                    <p><strong>Total Transactions:</strong> <?= $stats['total_transaksi']; ?></p>
                    <p><strong>Total Top-ups:</strong> <?= $stats['total_topup']; ?></p>
                    <p><strong>Total Purchases:</strong> <?= $stats['total_pembelian']; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h5>Transaction Values</h5>
                    <p><strong>Total Nominal:</strong> Rp <?= number_format($stats['total_nominal'], 0, ',', '.'); ?></p>
                    <p><strong>Completed Transactions:</strong> Rp <?= number_format($stats['total_nominal_completed'], 0, ',', '.'); ?></p>
                    <p><strong>Avg Transaction Value:</strong> Rp <?= number_format($stats['avg_transaction_value'], 0, ',', '.'); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h5>Transaction Status</h5>
                    <p><strong>Verified Transactions:</strong> Rp <?= number_format($stats['total_nominal_verified'], 0, ',', '.'); ?></p>
                    <p><strong>Pending Transactions:</strong> Rp <?= number_format($stats['total_nominal_pending'], 0, ',', '.'); ?></p>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="?export=excel" class="btn btn-success me-2">
                <i class="fas fa-file-excel"></i> Export to Excel
            </a>
            <a href="?export=pdf" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Export to PDF
            </a>
        </div>
    </div>
</body>

</html>

<?php include '../../templates/footer.php'; ?>