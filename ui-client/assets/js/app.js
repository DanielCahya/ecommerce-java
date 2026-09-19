// Get cart from localStorage
function getCart() {
    const cart = localStorage.getItem('cart');
    return cart ? JSON.parse(cart) : [];
}

// Save cart to localStorage
function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Add item to cart
function addToCart(id, name, price, quantity = 1) {
    const cart = getCart();
    
    // Check if item already exists
    const existingIndex = cart.findIndex(item => item.id === id);
    
    if (existingIndex !== -1) {
        // Update quantity
        cart[existingIndex].quantity += quantity;
    } else {
        // Add new item
        cart.push({
            id: id,
            name: name,
            price: price,
            quantity: quantity
        });
    }
    
    saveCart(cart);
    updateCartCount();
    
    // Show notification
    showNotification(`✅ ${name} ditambahkan ke keranjang!`);
}

// Update cart count in navbar
function updateCartCount() {
    const cart = getCart();
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    const countElement = document.getElementById('cart-count');
    if (countElement) {
        countElement.textContent = totalItems;
        
        // Add animation
        if (totalItems > 0) {
            countElement.classList.add('badge-highlight');
            setTimeout(() => {
                countElement.classList.remove('badge-highlight');
            }, 300);
        }
    }
}

// Show notification toast
function showNotification(message, type = 'success') {
    // Remove existing notifications
    const existing = document.querySelector('.notification-toast');
    if (existing) {
        existing.remove();
    }
    
    // Create notification
    const toast = document.createElement('div');
    toast.className = `notification-toast notification-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Show notification
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    // Hide and remove after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    
    // Handle modal close on outside click
    window.onclick = function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    };
});

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}