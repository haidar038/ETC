<?php
// pages/visitor/catalog.php

include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';

// Cek parameter token
if (!isset($_GET['token'])) {
    echo "<div class='alert alert-danger'>Token tidak ditemukan. Akses ditolak.</div>";
    include '../../templates/footer.php';
    exit();
}

$token = $conn->real_escape_string($_GET['token']);

// Cari tenant berdasarkan catalog_token
$sql = "SELECT * FROM users WHERE catalog_token = '$token' AND user_type = 'tenant'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>Tenant tidak ditemukan.</div>";
    include '../../templates/footer.php';
    exit();
}
$tenant = $result->fetch_assoc();

// Ambil produk dari tenant tersebut
$sqlProducts = "SELECT * FROM products WHERE tenant_id = " . $tenant['id'] . " ORDER BY created_at DESC";
$resultProducts = $conn->query($sqlProducts);

// Misal konversi: 1 poin = Rp 1000
?>
<h2>Katalog Produk Tenant: <?= $tenant['name']; ?></h2>
<div class="row">
    <?php while ($row = $resultProducts->fetch_assoc()):
        $poin = $row['price'] / 1000; // konversi harga
    ?>
    <div class="col-md-4">
        <div class="card mb-3">
            <?php if ($row['image']): ?>
            <img src="../../public/assets/uploads/<?= $row['image']; ?>" class="card-img-top" alt="Gambar Produk">
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?= $row['product_name']; ?></h5>
                <p class="card-text"><?= $row['description']; ?></p>
                <p class="card-text"><strong><?= number_format($poin, 0, ',', '.'); ?> poin</strong></p>
                <div class="input-group mb-3" style="max-width:150px;">
                    <input type="number" min="1" value="1" class="form-control quantity" id="qty_<?= $row['id']; ?>">
                    <span class="input-group-text">pcs</span>
                </div>
                <button class="btn btn-success addToCartBtn" data-id="<?= $row['id']; ?>"
                    data-name="<?= htmlspecialchars($row['product_name']); ?>" data-price="<?= $poin; ?>">
                    Add to Cart
                </button>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<!-- Tombol untuk melihat cart -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cartModal">
    View Cart (<span id="cartCount">0</span>)
</button>

<!-- Modal Cart -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="checkoutForm" action="../visitor/checkout.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Cart Belanja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="cartItems"></div>
                    <hr>
                    <p class="text-end"><strong>Total Poin: <span id="cartTotal">0</span></strong></p>
                    <!-- Field hidden untuk mengirim data cart ke checkout -->
                    <input type="hidden" name="cartData" id="cartData">
                    <!-- Kirim juga token tenant agar checkout tahu produk dari tenant mana -->
                    <input type="hidden" name="tenant_token" value="<?= $tenant['catalog_token']; ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Lanjut Belanja</button>
                    <button type="submit" class="btn btn-primary">Checkout</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Gunakan object cart untuk menyimpan produk yang dipilih
let cart = {};

// Fungsi untuk update tampilan cart
function updateCartDisplay() {
    let cartItemsDiv = document.getElementById('cartItems');
    let cartCount = document.getElementById('cartCount');
    let cartTotalEl = document.getElementById('cartTotal');
    let total = 0;
    let html =
        '<table class="table"><thead><tr><th>Produk</th><th>Jumlah</th><th>Harga per pcs (poin)</th><th>Subtotal (poin)</th><th>Aksi</th></tr></thead><tbody>';
    for (let id in cart) {
        let item = cart[id];
        let subtotal = item.quantity * item.price;
        total += subtotal;
        html += `<tr>
                <td>${item.name}</td>
                <td>${item.quantity}</td>
                <td>${item.price}</td>
                <td>${subtotal}</td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeFromCart('${id}')">Remove</button></td>
             </tr>`;
    }
    html += '</tbody></table>';
    cartItemsDiv.innerHTML = html;
    cartCount.innerText = Object.keys(cart).length;
    cartTotalEl.innerText = total;
    // Simpan data cart ke field hidden untuk dikirim ke checkout
    document.getElementById('cartData').value = JSON.stringify(cart);
}

// Fungsi untuk menambah produk ke cart
document.querySelectorAll('.addToCartBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        let id = this.getAttribute('data-id');
        let name = this.getAttribute('data-name');
        let price = parseFloat(this.getAttribute('data-price'));
        // Ambil nilai quantity dari input yang sesuai
        let qtyInput = document.getElementById('qty_' + id);
        let quantity = parseInt(qtyInput.value);
        if (cart[id]) {
            cart[id].quantity += quantity;
        } else {
            cart[id] = {
                id,
                name,
                price,
                quantity
            };
        }
        updateCartDisplay();
    });
});

// Fungsi untuk menghapus produk dari cart
function removeFromCart(id) {
    delete cart[id];
    updateCartDisplay();
}
</script>

<?php include '../../templates/footer.php'; ?>