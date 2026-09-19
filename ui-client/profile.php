<?php
require_once 'config/config.php';
require_once 'api/ApiClient.php';

requireLogin();

$client = new ApiClient();
$error = '';
$success = '';

// Get current user data
$userId = $_SESSION['user']['id'] ?? 0;
$response = $client->get("users/$userId");

if (!$response['success']) {
    header('Location: index.php');
    exit;
}

$userData = $response['data'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = $_POST['username'] ?? '';
    $newEmail = $_POST['email'] ?? '';
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($newUsername) || empty($newEmail)) {
        $error = 'Username dan email harus diisi';
    } elseif (!empty($newPassword) && $newPassword !== $confirmPassword) {
        $error = 'Password baru tidak cocok';
    } elseif (!empty($newPassword) && strlen($newPassword) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        // Prepare update data
        $updateData = [
            'name' => $newUsername,
            'email' => $newEmail,
            'role' => $userData['role']
        ];
        
        // Only update password if provided
        if (!empty($newPassword)) {
            $updateData['password'] = $newPassword;
        } else {
            // Keep old password (we need to get it from somewhere or handle differently)
            $updateData['password'] = $_POST['old_password_hidden'] ?? '';
        }
        
        $updateResponse = $client->put("users/$userId", $updateData);
        
        if ($updateResponse['success']) {
            $success = 'Profil berhasil diperbarui!';
            // Update session
            $_SESSION['user']['name'] = $newUsername;
            $_SESSION['user']['email'] = $newEmail;
            $userData = $updateResponse['data'];
        } else {
            $error = 'Gagal memperbarui profil';
        }
    }
}

$pageTitle = 'Profil Saya';
include 'views/header.php';
?>

<div class="profile-container">
    <div class="profile-header">
        <h1>👤 Profil Saya</h1>
        <p>Kelola informasi akun Anda</p>
    </div>

    <div class="profile-content">
        <div class="profile-sidebar">
            <div class="profile-avatar-large">
                <?= strtoupper(substr($userData['name'], 0, 1)) ?>
            </div>
            <h2><?= htmlspecialchars($userData['name']) ?></h2>
            <p class="profile-role">
                <?php if ($userData['role'] === 'admin'): ?>
                    <span class="badge badge-purple">👑 Administrator</span>
                <?php else: ?>
                    <span class="badge badge-blue">👤 User</span>
                <?php endif; ?>
            </p>
            <p class="profile-email">✉️ <?= htmlspecialchars($userData['email']) ?></p>
        </div>

        <div class="profile-main">
            <h3>✏️ Edit Profil</h3>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    ❌ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    ✅ <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="profile-form">
                <input type="hidden" name="old_password_hidden" value="<?= htmlspecialchars($userData['password'] ?? '') ?>">
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required 
                           value="<?= htmlspecialchars($userData['name']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= htmlspecialchars($userData['email']) ?>">
                </div>
                
                <hr>
                
                <h4>🔒 Ganti Password</h4>
                <p class="form-note">Kosongkan jika tidak ingin mengganti password</p>
                
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" id="password" name="password" 
                           placeholder="Minimal 6 karakter (opsional)">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           placeholder="Ketik ulang password baru">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-large">
                        💾 Simpan Perubahan
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        ← Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>