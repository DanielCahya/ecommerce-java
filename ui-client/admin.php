<?php
require_once 'config/config.php';
require_once 'api/ApiClient.php';
require_once 'api/ProductService.php';

requireLogin();

// Check if user is admin
$userRole = $_SESSION['user']['role'] ?? 'user';
if ($userRole !== 'admin') {
    header('Location: index.php');
    exit;
}

$client = new ApiClient();
$productService = new ProductService();

// Get all products and users
$productsResponse = $productService->getAllProducts();
$usersResponse = $client->get('users');

$products = $productsResponse['success'] ? $productsResponse['data'] : [];
$users = $usersResponse['success'] ? $usersResponse['data'] : [];

$pageTitle = 'Admin Panel';
include 'views/header.php';
?>

<div class="admin-container">
    <div class="admin-header">
        <h1>👑 Admin Dashboard</h1>
        <p>Kelola produk, user, dan sistem</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-info">
                <h3><?= count($products) ?></h3>
                <p>Total Produk</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?= count($users) ?></h3>
                <p>Total User</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">⚠️</div>
            <div class="stat-info">
                <h3><?= count(array_filter($products, fn($p) => $p['stock'] <= 5)) ?></h3>
                <p>Stok Rendah</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <h3>Rp <?= number_format(array_sum(array_map(fn($p) => $p['price'] * $p['stock'], $products)), 0, ',', '.') ?></h3>
                <p>Total Nilai Stok</p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="admin-tabs">
        <button class="tab-btn active" onclick="showTab('products')">📦 Kelola Produk</button>
        <button class="tab-btn" onclick="showTab('users')">👥 Kelola User</button>
        <button class="tab-btn" onclick="showTab('utils')">🛠️ Utilities</button>
    </div>

    <!-- Products Tab -->
    <div id="products-tab" class="tab-content active">
        <div class="section-header">
            <h2>📦 Manajemen Produk</h2>
            <button class="btn btn-primary" onclick="showAddProductModal()">
                ➕ Tambah Produk
            </button>
        </div>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="products-table-body">
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product['id'] ?></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><span class="badge badge-blue"><?= htmlspecialchars($product['category']) ?></span></td>
                            <td>Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($product['stock'] <= 0): ?>
                                    <span class="badge badge-red"><?= $product['stock'] ?></span>
                                <?php elseif ($product['stock'] <= 5): ?>
                                    <span class="badge badge-orange"><?= $product['stock'] ?></span>
                                <?php else: ?>
                                    <span class="badge badge-green"><?= $product['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-small btn-secondary" 
                                        onclick='editProduct(<?= json_encode($product) ?>)'>
                                    ✏️ Edit
                                </button>
                                <button class="btn btn-small btn-danger" 
                                        onclick="deleteProduct(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>')">
                                    🗑️ Hapus
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Users Tab -->
    <div id="users-tab" class="tab-content">
        <div class="section-header">
            <h2>👥 Manajemen User</h2>
            <button class="btn btn-primary" onclick="showAddUserModal()">
                ➕ Tambah User
            </button>
        </div>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="users-table-body">
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="badge badge-purple">👑 Admin</span>
                                <?php else: ?>
                                    <span class="badge badge-blue">👤 User</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-small btn-secondary" 
                                        onclick='editUser(<?= json_encode($user) ?>)'>
                                    ✏️ Edit
                                </button>
                                <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                                    <button class="btn btn-small btn-danger" 
                                            onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['name']) ?>')">
                                        🗑️ Hapus
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Utilities Tab -->
    <div id="utils-tab" class="tab-content">
        <h2>🛠️ Utilities & Tools</h2>
        
        <div class="utils-grid">
            <!-- Currency Converter -->
            <div class="util-card">
                <h3>💱 Currency Converter</h3>
                <form id="converter-form" onsubmit="convertCurrency(event)">
                    <div class="form-group">
                        <label>Dari:</label>
                        <select id="from-currency" class="form-control">
                            <option value="USD">USD</option>
                            <option value="IDR" selected>IDR</option>
                            <option value="EUR">EUR</option>
                            <option value="JPY">JPY</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ke:</label>
                        <select id="to-currency" class="form-control">
                            <option value="USD" selected>USD</option>
                            <option value="IDR">IDR</option>
                            <option value="EUR">EUR</option>
                            <option value="JPY">JPY</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jumlah:</label>
                        <input type="number" id="amount" class="form-control" value="100" required>
                    </div>
                    <button type="submit" class="btn btn-primary">🔄 Convert</button>
                </form>
                <div id="converter-result" class="result-box"></div>
            </div>

            <!-- Random Joke -->
            <div class="util-card">
                <h3>😄 Random Programming Joke</h3>
                <button class="btn btn-primary" onclick="getRandomJoke()">
                    🎲 Get Joke
                </button>
                <div id="joke-result" class="result-box"></div>
            </div>

            <!-- Discount Calculator -->
            <div class="util-card">
                <h3>🏷️ Discount Calculator</h3>
                <form id="discount-form" onsubmit="calculateDiscount(event)">
                    <div class="form-group">
                        <label>Harga Asli:</label>
                        <input type="number" id="discount-price" class="form-control" value="1000000" required>
                    </div>
                    <div class="form-group">
                        <label>Diskon (%):</label>
                        <input type="number" id="discount-percent" class="form-control" value="20" required>
                    </div>
                    <button type="submit" class="btn btn-primary">💰 Hitung</button>
                </form>
                <div id="discount-result" class="result-box"></div>
            </div>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div id="product-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeProductModal()">&times;</span>
        <h2 id="product-modal-title">Tambah Produk</h2>
        <form id="product-form" onsubmit="saveProduct(event)">
            <input type="hidden" id="product-id" value="">
            <div class="form-group">
                <label>Nama Produk *</label>
                <input type="text" id="product-name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea id="product-description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Harga *</label>
                <input type="number" id="product-price" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Stok *</label>
                <input type="number" id="product-stock" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select id="product-category" class="form-control">
                    <option value="Electronics">Electronics</option>
                    <option value="Fashion">Fashion</option>
                    <option value="Books">Books</option>
                    <option value="Furniture">Furniture</option>
                    <option value="General">General</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
        </form>
    </div>
</div>

<!-- User Modal -->
<div id="user-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeUserModal()">&times;</span>
        <h2 id="user-modal-title">Tambah User</h2>
        <form id="user-form" onsubmit="saveUser(event)">
            <input type="hidden" id="user-id" value="">
            <div class="form-group">
                <label>Username *</label>
                <input type="text" id="user-name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" id="user-email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password *</label>
                <input type="password" id="user-password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select id="user-role" class="form-control">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
        </form>
    </div>
</div>

<script src="assets/js/admin.js"></script>

<?php include 'views/footer.php'; ?>