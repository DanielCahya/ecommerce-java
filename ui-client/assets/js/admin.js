const API_BASE = 'http://192.168.1.3:4567/api';

// Tab switching
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    event.target.classList.add('active');
}

// PRODUCT MANAGEMENT

function showAddProductModal() {
    document.getElementById('product-modal-title').textContent = 'Tambah Produk';
    document.getElementById('product-id').value = '';
    document.getElementById('product-form').reset();
    document.getElementById('product-modal').style.display = 'flex';
}

function editProduct(product) {
    document.getElementById('product-modal-title').textContent = 'Edit Produk';
    document.getElementById('product-id').value = product.id;
    document.getElementById('product-name').value = product.name;
    document.getElementById('product-description').value = product.description;
    document.getElementById('product-price').value = product.price;
    document.getElementById('product-stock').value = product.stock;
    document.getElementById('product-category').value = product.category;
    document.getElementById('product-modal').style.display = 'flex';
}

function closeProductModal() {
    document.getElementById('product-modal').style.display = 'none';
}

async function saveProduct(event) {
    event.preventDefault();
    
    const id = document.getElementById('product-id').value;
    const productData = {
        name: document.getElementById('product-name').value,
        description: document.getElementById('product-description').value,
        price: parseFloat(document.getElementById('product-price').value),
        stock: parseInt(document.getElementById('product-stock').value),
        category: document.getElementById('product-category').value
    };
    
    try {
        let response;
        if (id) {
            // Update
            response = await fetch(`${API_BASE}/products/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(productData)
            });
        } else {
            // Create
            response = await fetch(`${API_BASE}/products`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(productData)
            });
        }
        
        if (response.ok) {
            alert(id ? 'Produk berhasil diupdate!' : 'Produk berhasil ditambahkan!');
            closeProductModal();
            location.reload();
        } else {
            alert('Gagal menyimpan produk');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

async function deleteProduct(id, name) {
    if (!confirm(`Yakin ingin menghapus produk "${name}"?`)) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/products/${id}`, {
            method: 'DELETE'
        });
        
        if (response.ok) {
            alert('Produk berhasil dihapus!');
            location.reload();
        } else {
            alert('Gagal menghapus produk');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

// USER MANAGEMENT

function showAddUserModal() {
    document.getElementById('user-modal-title').textContent = 'Tambah User';
    document.getElementById('user-id').value = '';
    document.getElementById('user-form').reset();
    document.getElementById('user-modal').style.display = 'flex';
}

function editUser(user) {
    document.getElementById('user-modal-title').textContent = 'Edit User';
    document.getElementById('user-id').value = user.id;
    document.getElementById('user-name').value = user.name;
    document.getElementById('user-email').value = user.email;
    document.getElementById('user-password').value = ''; // Don't show password
    document.getElementById('user-role').value = user.role;
    document.getElementById('user-modal').style.display = 'flex';
}

function closeUserModal() {
    document.getElementById('user-modal').style.display = 'none';
}

async function saveUser(event) {
    event.preventDefault();
    
    const id = document.getElementById('user-id').value;
    const password = document.getElementById('user-password').value;
    
    // If editing and password is empty, we need to handle it
    if (id && !password) {
        alert('Password harus diisi saat edit user');
        return;
    }
    
    const userData = {
        name: document.getElementById('user-name').value,
        email: document.getElementById('user-email').value,
        password: password,
        role: document.getElementById('user-role').value
    };
    
    try {
        let response;
        if (id) {
            // Update
            response = await fetch(`${API_BASE}/users/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(userData)
            });
        } else {
            // Create
            response = await fetch(`${API_BASE}/users`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(userData)
            });
        }
        
        if (response.ok) {
            alert(id ? 'User berhasil diupdate!' : 'User berhasil ditambahkan!');
            closeUserModal();
            location.reload();
        } else {
            const result = await response.json();
            alert('Gagal menyimpan user: ' + (result.status || 'Unknown error'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

async function deleteUser(id, name) {
    if (!confirm(`Yakin ingin menghapus user "${name}"?`)) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/users/${id}`, {
            method: 'DELETE'
        });
        
        if (response.ok) {
            alert('User berhasil dihapus!');
            location.reload();
        } else {
            alert('Gagal menghapus user');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

// UTILITIES

async function convertCurrency(event) {
    event.preventDefault();
    
    const from = document.getElementById('from-currency').value;
    const to = document.getElementById('to-currency').value;
    const amount = document.getElementById('amount').value;
    
    try {
        const response = await fetch(`${API_BASE}/utils/convert?from=${from}&to=${to}&amount=${amount}`);
        const data = await response.json();
        
        if (response.ok) {
            document.getElementById('converter-result').innerHTML = `
                <div class="alert alert-success">
                    <strong>${data.originalAmount} ${data.from}</strong> = 
                    <strong>${data.convertedAmount.toFixed(2)} ${data.to}</strong>
                    <br><small>Rate: ${data.rate}</small>
                </div>
            `;
        } else {
            document.getElementById('converter-result').innerHTML = `
                <div class="alert alert-danger">${data.error}</div>
            `;
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

async function getRandomJoke() {
    try {
        const response = await fetch(`${API_BASE}/fun/joke`);
        const data = await response.json();
        
        if (response.ok) {
            document.getElementById('joke-result').innerHTML = `
                <div class="alert alert-info">
                    😄 ${data.joke}
                </div>
            `;
        } else {
            alert('Gagal mendapatkan joke');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

async function calculateDiscount(event) {
    event.preventDefault();
    
    const price = document.getElementById('discount-price').value;
    const discount = document.getElementById('discount-percent').value;
    
    try {
        const response = await fetch(`${API_BASE}/utils/discount?price=${price}&discount=${discount}`);
        const data = await response.json();
        
        if (response.ok) {
            const savings = data.originalPrice - data.finalPrice;
            document.getElementById('discount-result').innerHTML = `
                <div class="alert alert-success">
                    <strong>Harga Asli:</strong> Rp ${data.originalPrice.toLocaleString('id-ID')}<br>
                    <strong>Diskon:</strong> ${data.discount}%<br>
                    <strong>Harga Final:</strong> Rp ${data.finalPrice.toLocaleString('id-ID')}<br>
                    <strong>Hemat:</strong> Rp ${savings.toLocaleString('id-ID')}
                </div>
            `;
        } else {
            alert('Gagal menghitung diskon');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}