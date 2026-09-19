<?php
require_once 'config/config.php';
require_once 'api/ProductService.php';
require_once 'services/ImplicitProductRules.php';

$productId = $_GET['id'] ?? 0;

if (!$productId) {
    header('Location: index.php');
    exit;
}

$productService = new ProductService();
$response = $productService->getProductById($productId);

if (!$response['success'] || empty($response['data'])) {
    header('Location: index.php');
    exit;
}

// Apply implicit rules
$product = ImplicitProductRules::applyRules($response['data']);
$pageTitle = htmlspecialchars($product['name']);

include 'views/header.php';
?>

<div class="product-detail">
    <div class="product-detail-image">
        <div class="image-placeholder-large">📦</div>
        
        <!-- Badges -->
        <div class="badges-large">
            <?php foreach ($product['badges'] as $badge): ?>
                <span class="badge badge-<?= $badge['color'] ?> badge-large">
                    <?= $badge['icon'] ?> <?= htmlspecialchars($badge['text']) ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="product-detail-info">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        
        <p class="category">
            📁 Kategori: <strong><?= htmlspecialchars($product['category']) ?></strong>
        </p>

        <!-- Price Section -->
        <div class="price-section-large">
            <?php if ($product['display_price']['has_discount']): ?>
                <div class="promo-info">
                    🏷️ <strong>Promo Aktif:</strong> <?= htmlspecialchars($product['promo']) ?>
                </div>
                <div class="price-comparison">
                    <span class="price-original-large">
                        Rp <?= number_format($product['display_price']['original'], 0, ',', '.') ?>
                    </span>
                    <span class="discount-badge">
                        -<?= $product['display_price']['discount'] ?>%
                    </span>
                </div>
                <div class="price-final-large">
                    Rp <?= number_format($product['display_price']['final'], 0, ',', '.') ?>
                </div>
                <div class="savings-large">
                    💰 Anda hemat: <strong>Rp <?= number_format($product['display_price']['savings'], 0, ',', '.') ?></strong>
                </div>
            <?php else: ?>
                <div class="price-large">
                    Rp <?= number_format($product['price'], 0, ',', '.') ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Stock Information -->
        <div class="stock-info">
            <?php if ($product['alert']): ?>
                <div class="alert alert-danger">
                    🚫 <strong>Stok Habis</strong> - Produk sedang tidak tersedia
                </div>
            <?php elseif ($product['stock'] <= 5): ?>
                <div class="alert alert-warning">
                    ⚠️ <strong>Stok Terbatas!</strong> Tersisa <?= $product['stock'] ?> unit
                </div>
            <?php else: ?>
                <div class="stock-available">
                    ✅ Stok tersedia: <strong><?= $product['stock'] ?> unit</strong>
                </div>
            <?php endif; ?>
        </div>

        <!-- Description -->
        <div class="description">
            <h3>📝 Deskripsi Produk</h3>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        </div>

        <!-- Availability Check -->
        <div class="availability-check availability-<?= $product['availability']['status'] ?>">
            <strong>Status:</strong> <?= htmlspecialchars($product['availability']['message']) ?>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <?php if ($product['maintenance']): ?>
                <button class="btn btn-disabled btn-large" disabled onclick="showMaintenancePopup()">
                    🔧 Kategori Sedang Maintenance
                </button>
                <p class="maintenance-note">
                    Produk ini tidak dapat dibeli saat ini karena kategori sedang dalam pemeliharaan.
                </p>
            <?php elseif ($product['alert']): ?>
                <button class="btn btn-disabled btn-large" disabled>
                    🚫 Stok Habis
                </button>
            <?php elseif ($product['auth_required'] && !isLoggedIn()): ?>
                <button class="btn btn-primary btn-large" onclick="redirectToLogin()">
                    🔒 Login untuk Membeli Produk Premium
                </button>
                <p class="auth-note">
                    Produk premium memerlukan akun terverifikasi untuk melakukan pembelian.
                </p>
            <?php else: ?>
                <div class="quantity-selector">
                    <label for="quantity">Jumlah:</label>
                    <input type="number" id="quantity" value="1" min="1" max="<?= $product['stock'] ?>">
                </div>
                <button class="btn btn-primary btn-large" 
                        onclick="addToCartWithQuantity(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>', <?= $product['display_price']['final'] ?>)">
                    🛒 Tambah ke Keranjang
                </button>
                <a href="index.php" class="btn btn-secondary btn-large">
                    ← Kembali ke Daftar Produk
                </a>
            <?php endif; ?>
        </div>

        <!-- Force Refresh Indicator -->
        <?php if ($product['force_refresh']): ?>
            <div class="alert alert-info">
                🔄 <strong>Produk Hot!</strong> Halaman akan otomatis refresh untuk menampilkan stok terbaru.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Maintenance Modal -->
<div id="maintenance-modal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeMaintenancePopup()">&times;</span>
        <h2>🔧 Mode Maintenance</h2>
        <p>Maaf, kategori produk ini sedang dalam pemeliharaan sistem.</p>
        <p>Silakan coba lagi nanti atau hubungi customer service kami.</p>
        <button class="btn btn-primary" onclick="closeMaintenancePopup()">OK</button>
    </div>
</div>

<script>
    const shouldForceRefresh = <?= $product['force_refresh'] ? 'true' : 'false' ?>;

    function redirectToLogin() {
        window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.href);
    }

    function showMaintenancePopup() {
        document.getElementById('maintenance-modal').style.display = 'flex';
    }

    function closeMaintenancePopup() {
        document.getElementById('maintenance-modal').style.display = 'none';
    }

    function addToCartWithQuantity(id, name, price) {
        const quantity = parseInt(document.getElementById('quantity').value);
        addToCart(id, name, price, quantity);
    }

    // Show maintenance banner if product category is in maintenance
    <?php if ($product['maintenance']): ?>
        document.getElementById('maintenance-banner').style.display = 'block';
    <?php endif; ?>
</script>

<?php include 'views/footer.php'; ?>