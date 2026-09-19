<?php
require_once 'config/config.php';
require_once 'api/ProductService.php';

$pageTitle = 'Keranjang Belanja';
include 'views/header.php';
?>

<div class="cart-container">
    <h1>🛒 Keranjang Belanja</h1>
    
    <div id="cart-empty" class="empty-state" style="display:none;">
        <p>😕 Keranjang Anda masih kosong.</p>
        <a href="index.php" class="btn btn-primary">🛍️ Mulai Belanja</a>
    </div>

    <div id="cart-content" style="display:none;">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="cart-items">
                <!-- Cart items will be inserted here by JavaScript -->
            </tbody>
        </table>

        <div class="cart-summary">
            <div class="summary-row">
                <span>Total Item:</span>
                <strong id="total-items">0</strong>
            </div>
            <div class="summary-row">
                <span>Total Harga:</span>
                <strong id="total-price">Rp 0</strong>
            </div>
            
            <div class="cart-actions">
                <button class="btn btn-secondary" onclick="clearCart()">
                    🗑️ Kosongkan Keranjang
                </button>
                <button class="btn btn-primary btn-large" onclick="checkout()">
                    💳 Checkout
                </button>
            </div>
        </div>
    </div>

    <div id="checkout-result" style="display:none;">
        <div class="alert" id="checkout-alert"></div>
    </div>
</div>

<script>
// Load and display cart
function loadCart() {
    const cart = getCart();
    
    if (cart.length === 0) {
        document.getElementById('cart-empty').style.display = 'block';
        document.getElementById('cart-content').style.display = 'none';
        return;
    }
    
    document.getElementById('cart-empty').style.display = 'none';
    document.getElementById('cart-content').style.display = 'block';
    
    const tbody = document.getElementById('cart-items');
    tbody.innerHTML = '';
    
    let totalItems = 0;
    let totalPrice = 0;
    
    cart.forEach((item, index) => {
        const subtotal = item.price * item.quantity;
        totalItems += item.quantity;
        totalPrice += subtotal;
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.name}</td>
            <td>Rp ${formatPrice(item.price)}</td>
            <td>
                <input type="number" value="${item.quantity}" min="1" max="99"
                       onchange="updateQuantity(${index}, this.value)"
                       class="quantity-input">
            </td>
            <td>Rp ${formatPrice(subtotal)}</td>
            <td>
                <button class="btn btn-danger btn-small" onclick="removeFromCart(${index})">
                    🗑️
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
    
    document.getElementById('total-items').textContent = totalItems;
    document.getElementById('total-price').textContent = 'Rp ' + formatPrice(totalPrice);
}

// Update quantity
function updateQuantity(index, newQuantity) {
    const cart = getCart();
    cart[index].quantity = parseInt(newQuantity);
    saveCart(cart);
    loadCart();
    updateCartCount();
}

// Remove item
function removeFromCart(index) {
    const cart = getCart();
    cart.splice(index, 1);
    saveCart(cart);
    loadCart();
    updateCartCount();
}

// Clear cart
function clearCart() {
    if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
        localStorage.removeItem('cart');
        loadCart();
        updateCartCount();
    }
}

// Checkout
async function checkout() {
    const cart = getCart();
    
    if (cart.length === 0) {
        alert('Keranjang kosong!');
        return;
    }
    
    // Prepare checkout data
    const checkoutData = cart.map(item => ({
        id: item.id,
        quantity: item.quantity
    }));
    
    try {
        const response = await fetch('<?= API_BASE_URL ?>/products/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(checkoutData)
        });
        
        const result = await response.json();
        const alertDiv = document.getElementById('checkout-alert');
        const resultDiv = document.getElementById('checkout-result');
        
        if (response.ok) {
            alertDiv.className = 'alert alert-success';
            alertDiv.innerHTML = '✅ <strong>Checkout Berhasil!</strong><br>' + result.status;
            
            // Clear cart after successful checkout
            localStorage.removeItem('cart');
            loadCart();
            updateCartCount();
        } else {
            alertDiv.className = 'alert alert-danger';
            alertDiv.innerHTML = '❌ <strong>Checkout Gagal!</strong><br>' + result.status;
        }
        
        resultDiv.style.display = 'block';
        
        // Scroll to result
        resultDiv.scrollIntoView({ behavior: 'smooth' });
        
    } catch (error) {
        alert('Error saat checkout: ' + error.message);
    }
}

// Format price helper
function formatPrice(price) {
    return price.toLocaleString('id-ID');
}

// Load cart on page load
document.addEventListener('DOMContentLoaded', loadCart);
</script>

<?php include 'views/footer.php'; ?>