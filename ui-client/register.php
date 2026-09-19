<?php

require_once 'config/config.php';
require_once 'api/ApiClient.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Semua field harus diisi';
    } elseif ($password !== $confirmPassword) {
        $error = 'Password tidak cocok';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        $client = new ApiClient();
        $response = $client->post('register', [
            'name' => $username,
            'email' => $email,
            'password' => $password
        ]);
        
        if ($response['success']) {
            $success = 'Registrasi berhasil! Silakan login.';
            // Auto redirect after 2 seconds
            header('refresh:2;url=login.php');
        } else {
            $error = $response['data']['status'] ?? 'Registrasi gagal. Silakan coba lagi.';
        }
    }
}

$pageTitle = 'Register';
include 'views/header.php';
?>

<div class="login-container">
    <div class="login-box">
        <h1>Daftar Akun Baru</h1>
        <p>Buat akun untuk mengakses semua fitur e-commerce</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                ❌ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                ✅ <?= htmlspecialchars($success) ?>
                <br><small>Redirecting to login...</small>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required 
                       placeholder="Masukkan username" 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                <small>Username akan digunakan untuk login</small>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required 
                       placeholder="contoh@email.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Minimal 6 karakter">
                <small>Password minimal 6 karakter</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" required 
                       placeholder="Ketik ulang password">
            </div>
            
            <button type="submit" class="btn btn-primary btn-large">
                Daftar Sekarang
            </button>
        </form>
        
        <div class="login-help">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
        
        <a href="index.php" class="btn btn-secondary">
            ← Kembali ke Beranda
        </a>
    </div>
</div>

<?php include 'views/footer.php'; ?>