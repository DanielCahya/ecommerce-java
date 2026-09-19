<?php

require_once 'config/config.php';

$pageTitle = 'Utilities';
include 'views/header.php';
?>

<div class="utilities-container">
    <div class="page-header">
        <h1>🛠️ Utilities & Tools</h1>
        <p>Kalkulator dan tools berguna untuk Anda</p>
    </div>

    <div class="utils-grid">
        <!-- Currency Converter -->
        <div class="util-card">
            <h3>💱 Currency Converter</h3>
            <p class="util-desc">Convert antar mata uang dengan rate terkini</p>
            <form id="converter-form" onsubmit="convertCurrency(event)">
                <div class="form-group">
                    <label>Dari Currency:</label>
                    <select id="from-currency" class="form-control">
                        <option value="USD">🇺🇸 USD - US Dollar</option>
                        <option value="IDR" selected>🇮🇩 IDR - Indonesian Rupiah</option>
                        <option value="EUR">🇪🇺 EUR - Euro</option>
                        <option value="JPY">🇯🇵 JPY - Japanese Yen</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ke Currency:</label>
                    <select id="to-currency" class="form-control">
                        <option value="USD" selected>🇺🇸 USD - US Dollar</option>
                        <option value="IDR">🇮🇩 IDR - Indonesian Rupiah</option>
                        <option value="EUR">🇪🇺 EUR - Euro</option>
                        <option value="JPY">🇯🇵 JPY - Japanese Yen</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah:</label>
                    <input type="number" id="amount" class="form-control" value="1000000" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-primary btn-large">🔄 Convert</button>
            </form>
            <div id="converter-result" class="result-box"></div>
        </div>

        <!-- Discount Calculator -->
        <div class="util-card">
            <h3>🏷️ Discount Calculator</h3>
            <p class="util-desc">Hitung harga setelah diskon dengan mudah</p>
            <form id="discount-form" onsubmit="calculateDiscount(event)">
                <div class="form-group">
                    <label>Harga Asli (Rp):</label>
                    <input type="number" id="discount-price" class="form-control" value="1000000" required>
                </div>
                <div class="form-group">
                    <label>Diskon (%):</label>
                    <input type="number" id="discount-percent" class="form-control" value="20" min="0" max="100" required>
                </div>
                <button type="submit" class="btn btn-primary btn-large">💰 Hitung Diskon</button>
            </form>
            <div id="discount-result" class="result-box"></div>
        </div>

        <!-- Random Joke -->
        <div class="util-card util-card-fun">
            <h3>😄 Programming Jokes</h3>
            <p class="util-desc">Butuh sedikit hiburan? Get a random programming joke!</p>
            <button class="btn btn-primary btn-large" onclick="getRandomJoke()">
                🎲 Get Random Joke
            </button>
            <div id="joke-result" class="result-box"></div>
        </div>
    </div>
</div>

<script>
const API_BASE = '<?= API_BASE_URL ?>';

async function convertCurrency(event) {
    event.preventDefault();
    
    const from = document.getElementById('from-currency').value;
    const to = document.getElementById('to-currency').value;
    const amount = document.getElementById('amount').value;
    
    const resultBox = document.getElementById('converter-result');
    resultBox.innerHTML = '<div class="loading">⏳ Converting...</div>';
    
    try {
        const response = await fetch(`${API_BASE}/utils/convert?from=${from}&to=${to}&amount=${amount}`);
        const data = await response.json();
        
        if (response.ok) {
            resultBox.innerHTML = `
                <div class="alert alert-success">
                    <h4>💱 Hasil Konversi</h4>
                    <div class="conversion-result">
                        <div class="conversion-row">
                            <span>Jumlah Awal:</span>
                            <strong>${parseFloat(data.originalAmount).toLocaleString('id-ID', {minimumFractionDigits: 2})} ${data.from}</strong>
                        </div>
                        <div class="conversion-arrow">⬇️</div>
                        <div class="conversion-row highlight">
                            <span>Hasil Konversi:</span>
                            <strong>${parseFloat(data.convertedAmount).toLocaleString('id-ID', {minimumFractionDigits: 2})} ${data.to}</strong>
                        </div>
                        <div class="conversion-rate">
                            <small>Rate: 1 ${data.from} = ${data.rate} ${data.to}</small>
                        </div>
                    </div>
                </div>
            `;
        } else {
            resultBox.innerHTML = `
                <div class="alert alert-danger">
                    ❌ ${data.error}
                </div>
            `;
        }
    } catch (error) {
        resultBox.innerHTML = `
            <div class="alert alert-danger">
                ❌ Error: ${error.message}
            </div>
        `;
    }
}

async function calculateDiscount(event) {
    event.preventDefault();
    
    const price = document.getElementById('discount-price').value;
    const discount = document.getElementById('discount-percent').value;
    
    const resultBox = document.getElementById('discount-result');
    resultBox.innerHTML = '<div class="loading">⏳ Calculating...</div>';
    
    try {
        const response = await fetch(`${API_BASE}/utils/discount?price=${price}&discount=${discount}`);
        const data = await response.json();
        
        if (response.ok) {
            const savings = data.originalPrice - data.finalPrice;
            resultBox.innerHTML = `
                <div class="alert alert-success">
                    <h4>💰 Hasil Perhitungan</h4>
                    <div class="discount-result">
                        <div class="discount-row">
                            <span>Harga Asli:</span>
                            <strong>Rp ${parseFloat(data.originalPrice).toLocaleString('id-ID')}</strong>
                        </div>
                        <div class="discount-row">
                            <span>Diskon:</span>
                            <strong class="discount-percent">${data.discount}%</strong>
                        </div>
                        <div class="discount-row">
                            <span>Potongan:</span>
                            <strong class="savings-amount">- Rp ${savings.toLocaleString('id-ID')}</strong>
                        </div>
                        <hr>
                        <div class="discount-row highlight">
                            <span>Harga Final:</span>
                            <strong class="final-price">Rp ${parseFloat(data.finalPrice).toLocaleString('id-ID')}</strong>
                        </div>
                    </div>
                </div>
            `;
        } else {
            resultBox.innerHTML = `
                <div class="alert alert-danger">
                    ❌ Gagal menghitung diskon
                </div>
            `;
        }
    } catch (error) {
        resultBox.innerHTML = `
            <div class="alert alert-danger">
                ❌ Error: ${error.message}
            </div>
        `;
    }
}

async function getRandomJoke() {
    const resultBox = document.getElementById('joke-result');
    resultBox.innerHTML = '<div class="loading">⏳ Getting joke...</div>';
    
    try {
        const response = await fetch(`${API_BASE}/fun/joke`);
        const data = await response.json();
        
        if (response.ok) {
            resultBox.innerHTML = `
                <div class="alert alert-info joke-box">
                    <div class="joke-icon">😄</div>
                    <p class="joke-text">${data.joke}</p>
                    <button class="btn btn-secondary btn-small" onclick="getRandomJoke()">
                        🔄 Another One!
                    </button>
                </div>
            `;
        } else {
            resultBox.innerHTML = `
                <div class="alert alert-danger">
                    ❌ Gagal mendapatkan joke
                </div>
            `;
        }
    } catch (error) {
        resultBox.innerHTML = `
            <div class="alert alert-danger">
                ❌ Error: ${error.message}
            </div>
        `;
    }
}
</script>

<style>
.utilities-container {
    max-width: 1200px;
    margin: 0 auto;
}

.util-desc {
    color: var(--secondary);
    margin-bottom: 1.5rem;
}

.util-card-fun {
    background: linear-gradient(135deg, #667eea22 0%, #764ba244 100%);
    border: 2px solid #667eea;
}

.loading {
    text-align: center;
    padding: 1rem;
    color: var(--secondary);
}

.conversion-result,
.discount-result {
    margin-top: 1rem;
}

.conversion-row,
.discount-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.conversion-row.highlight,
.discount-row.highlight {
    background: #f0fdf4;
    padding: 1rem;
    border-radius: 0.5rem;
    border: 2px solid var(--success);
    margin-top: 0.5rem;
}

.conversion-arrow {
    text-align: center;
    font-size: 1.5rem;
    margin: 0.5rem 0;
}

.conversion-rate {
    text-align: center;
    margin-top: 1rem;
    color: var(--secondary);
}

.discount-percent {
    color: var(--warning);
}

.savings-amount {
    color: var(--danger);
}

.final-price {
    color: var(--success);
    font-size: 1.5rem;
}

.joke-box {
    text-align: center;
}

.joke-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.joke-text {
    font-size: 1.1rem;
    font-style: italic;
    margin-bottom: 1.5rem;
    line-height: 1.8;
}
</style>

<?php include 'views/footer.php'; ?>