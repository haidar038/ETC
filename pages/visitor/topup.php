<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../includes/functions.php';
include '../../templates/header.php';

// Pastikan hanya visitor yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'visitor') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Proses topup (hanya topup mandiri)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nominal'])) {
    $nominal = filter_input(INPUT_POST, 'nominal', FILTER_VALIDATE_INT);
    if ($nominal === false || $nominal <= 0) {
        echo "<div class='alert alert-danger'>Nominal topup tidak valid.</div>";
        exit();
    }

    $transaction_type = 'topup_mandiri';
    $qr_token = generateQRToken();

    $sql = "INSERT INTO transactions (user_id, nominal, transaction_type, status, qr_token) VALUES ($user_id, $nominal, '$transaction_type', 'pending', '$qr_token')";
    if ($conn->query($sql)) {
        // Simpan data penting jika perlu ditampilkan di halaman konfirmasi, misalnya melalui session
        $_SESSION['topup_success'] = [
            'qr_token' => $qr_token,
            'nominal' => $nominal
        ];
        header("Location: topup_confirmation.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

?>

<h2 class="fw-bold text-primary mb-4">Top Up Poin</h2>

<form method="POST">
    <div class="mb-3">
        <label>Nominal Topup (Rp)</label>
        <input type="number" name="nominal" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Proses Topup Mandiri</button>
</form>

<hr>

<h4 class="fw-bold text-primary mb-3">Riwayat Topup</h4>

<?php
// Query untuk mengambil riwayat topup visitor (hanya transaksi topup_mandiri)
$sqlHistory = "SELECT * FROM transactions WHERE user_id = $user_id AND transaction_type='topup_mandiri' ORDER BY transaction_date DESC";
$resultHistory = $conn->query($sqlHistory);
if ($resultHistory->num_rows > 0) :
?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nominal (Rp)</th>
                <th>Metode</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultHistory->fetch_assoc()) : ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= number_format($row['nominal'], 0, ',', '.'); ?></td>
                    <td>Mandiri</td>
                    <td>
                        <?php if ($row['status'] == 'pending'): ?>
                            <span class="badge text-bg-warning"><?= ucfirst($row['status']); ?></span>
                        <?php elseif ($row['status'] == 'verified'): ?>
                            <span class="badge text-bg-info"><?= ucfirst($row['status']); ?></span>
                        <?php elseif ($row['status'] == 'completed'): ?>
                            <span class="badge text-bg-success"><?= ucfirst($row['status']); ?></span>
                        <?php else: ?>
                            <span class="badge text-bg-secondary"><?= ucfirst($row['status']); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= $row['transaction_date']; ?></td>
                    <td>
                        <!-- Button untuk membuka modal detail -->
                        <button type="button" class="btn btn-info btn-sm detailBtn" data-id="<?= $row['id']; ?>"
                            data-nominal="<?= number_format($row['nominal'], 0, ',', '.'); ?>" data-method="Mandiri"
                            data-status="<?= ucfirst($row['status']); ?>" data-token="<?= $row['qr_token']; ?>"
                            data-date="<?= $row['transaction_date']; ?>">Detail</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else : ?>
    <div class="alert alert-info">Belum ada riwayat topup.</div>
<?php endif; ?>

<!-- Modal Detail Topup -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Topup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>ID Transaksi:</strong> <span id="modal-id"></span></p>
                <p><strong>Nominal:</strong> Rp <span id="modal-nominal"></span></p>
                <p><strong>Metode:</strong> <span id="modal-method"></span></p>
                <p><strong>Status:</strong> <span id="modal-status"></span></p>
                <p><strong>QR Token:</strong> <span id="modal-token"></span></p>
                <p><strong>Tanggal:</strong> <span id="modal-date"></span></p>
                <p><strong>Gambar QR:</strong></p>
                <img id="modal-qr" src="" alt="QR Code" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk mengisi modal dengan data dari button -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var detailButtons = document.querySelectorAll('.detailBtn');
        detailButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                // Ambil data dari atribut data-*
                var token = button.getAttribute('data-token');
                document.getElementById('modal-id').innerText = button.getAttribute('data-id');
                document.getElementById('modal-nominal').innerText = button.getAttribute(
                    'data-nominal');
                document.getElementById('modal-method').innerText = button.getAttribute(
                    'data-method');
                document.getElementById('modal-status').innerText = button.getAttribute(
                    'data-status');
                document.getElementById('modal-token').innerText = token;
                document.getElementById('modal-date').innerText = button.getAttribute('data-date');
                // Update gambar QR menggunakan API quickchart.io
                document.getElementById('modal-qr').src = "https://quickchart.io/qr?text=" + token +
                    "&size=200";

                // Tampilkan modal menggunakan Bootstrap 5
                var detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
                detailModal.show();
            });
        });
    });
</script>

<?php include '../../templates/footer.php'; ?>