<?php
require_once 'config/config.php';
require_once 'api/ApiClient.php';

$error = '';
$redirect = $_GET['redirect'] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $client = new ApiClient();
    $response = $client->post('login', [
        'username' => $username,
        'password' => $password
    ]);
    
    if ($response['success'] && isset($response['data']['token'])) {
        // Store user session
        $_SESSION['user'] = $response['data']['user'];
        $_SESSION['token'] = $response['data']['token'];
        
        // Redirect back
        header('Location: ' . $redirect);
        exit;
    } else {
        $error = $response['data']['status'] ?? 'Login gagal. Username atau password salah.';
    }
}

$pageTitle = 'Login';
include 'views/header.php';
?>

<div class="login-container">
    <div class="login-box">
        <h1>Login</h1>
        <p>Masuk untuk mengakses produk premium dan fitur eksklusif</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                ❌ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required 
                       placeholder="Masukkan username" 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Masukkan password">
            </div>
            
            <button type="submit" class="btn btn-primary btn-large">
                Login
            </button>
        </form>
        
        <div class="login-help">
            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        </div>
        
        <a href="index.php" class="btn btn-secondary">
            ← Kembali ke Beranda
        </a>
    </div>
</div>

<?php include 'views/footer.php'; ?>