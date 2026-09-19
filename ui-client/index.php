<?php
require_once 'config/config.php';
require_once 'api/ProductService.php';
require_once 'services/ImplicitProductRules.php';

$pageTitle = 'Daftar Produk';
$productService = new ProductService();

// Get search/filter parameters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// Fetch products
if ($search) {
    $response = $productService->searchProducts($search);
} elseif ($category) {
    $response = $productService->getProductsByCategory($category);
} else {
    $response = $productService->getAllProducts();
}

$products = [];
$shouldForceRefresh = false;

if ($response['success'] && !empty($response['data'])) {
    // Apply implicit rules to all products
    $products = ImplicitProductRules::applyRulesToList($response['data']);
    
    // Check if any product requires force refresh
    foreach ($products as $product) {
        if ($product['force_refresh']) {
            $shouldForceRefresh = true;
            break;
        }
    }
}

include 'views/header.php';
?>

<div class="page-header">
    <h1>🛍️ Daftar Produk</h1>
    <p>Temukan produk terbaik dengan harga terbaik!</p>
</div>

<!-- Search & Filter -->
<div class="search-filter">
    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Cari produk..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">🔍 Cari</button>
    </form>
    
    <div class="filter-buttons">
        <a href="index.php" class="btn <?= empty($category) ? 'btn-active' : '' ?>">Semua</a>
        <a href="?category=Electronics" class="btn <?= $category === 'Electronics' ? 'btn-active' : '' ?>">Electronics</a>
        <a href="?category=Fashion" class="btn <?= $category === 'Fashion' ? 'btn-active' : '' ?>">Fashion</a>
        <a href="?category=Furniture" class="btn <?= $category === 'Furniture' ? 'btn-active' : '' ?>">Furniture</a>
    </div>
</div>

<!-- Product Grid -->
<div class="product-grid">
    <?php if (empty($products)): ?>
        <div class="empty-state">
            <p>😕 Tidak ada produk ditemukan.</p>
        </div>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <div class="product-card <?= $product['alert'] ? 'out-of-stock' : '' ?>" 
                 data-product-id="<?= $product['id'] ?>">
                
                <!-- Badges -->
                <div class="badges">
                    <?php foreach ($product['badges'] as $badge): ?>
                        <span class="badge badge-<?= $badge['color'] ?>">
                            <?= $badge['icon'] ?> <?= htmlspecialchars($badge['text']) ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <!-- Product Image Placeholder -->
                <div class="product-image">
                    <div class="image-placeholder">📦</div>
                </div>

                <!-- Product Info -->
                <div class="product-info">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="category">📁 <?= htmlspecialchars($product['category']) ?></p>
                    
                    <!-- Price Display with Promo -->
                    <div class="price-section">
                        <?php if ($product['display_price']['has_discount']): ?>
                            <span class="price-original">
                                Rp <?= number_format($product['display_price']['original'], 0, ',', '.') ?>
                            </span>
                            <span class="price-final">
                                Rp <?= number_format($product['display_price']['final'], 0, ',', '.') ?>
                            </span>
                            <span class="savings">
                                Hemat Rp <?= number_format($product['display_price']['savings'], 0, ',', '.') ?>
                            </span>
                        <?php else: ?>
                            <span class="price">
                                Rp <?= number_format($product['price'], 0, ',', '.') ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Stock Info -->
                    <p class="stock">
                        Stok: <strong><?= $product['stock'] ?></strong>
                    </p>

                    <!-- Availability Status -->
                    <div class="availability availability-<?= $product['availability']['status'] ?>">
                        <?= htmlspecialchars($product['availability']['message']) ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="product-actions">
                    <?php if ($product['maintenance']): ?>
                        <button class="btn btn-disabled" disabled 
                                onclick="showMaintenancePopup()">
                            🔧 Maintenance
                        </button>
                    <?php elseif ($product['alert']): ?>
                        <button class="btn btn-disabled" disabled>
                            🚫 Stok Habis
                        </button>
                    <?php elseif ($product['auth_required'] && !isLoggedIn()): ?>
                        <button class="btn btn-primary" 
                                onclick="redirectToLogin()">
                            🔒 Login untuk Beli
                        </button>
                    <?php else: ?>
                        <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-secondary">
                            👁️ Detail
                        </a>
                        <button class="btn btn-primary" 
                                onclick="addToCart(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>', <?= $product['display_price']['final'] ?>)">
                            🛒 Keranjang
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Maintenance Popup Modal -->
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
    // Pass force_refresh flag to footer script
    const shouldForceRefresh = <?= $shouldForceRefresh ? 'true' : 'false' ?>;

    function redirectToLogin() {
        window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.href);
    }

    function showMaintenancePopup() {
        document.getElementById('maintenance-modal').style.display = 'flex';
    }

    function closeMaintenancePopup() {
        document.getElementById('maintenance-modal').style.display = 'none';
    }

    // Show maintenance banner if any products are in maintenance
    const maintenanceProducts = <?= json_encode(array_filter($products, fn($p) => $p['maintenance'])) ?>;
    if (maintenanceProducts.length > 0) {
        document.getElementById('maintenance-banner').style.display = 'block';
    }
</script>

<?php include 'views/footer.php'; ?>