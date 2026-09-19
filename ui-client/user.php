<?php

require_once 'config/config.php';
require_once 'api/ApiClient.php';
require_once 'services/ImplicitUserRules.php';

$pageTitle = 'Profil Pengguna';

$client = new ApiClient();
$response = $client->get('users');

$users = [];
if ($response['success'] && !empty($response['data'])) {
    // Apply implicit rules to users
    $users = array_map([ImplicitUserRules::class, 'applyRules'], $response['data']);
}

include 'views/header.php';
?>

<div class="user-container">
    <h1>👥 Daftar Pengguna</h1>
    
    <?php if (isLoggedIn()): ?>
        <div class="current-user-info">
            <h3>👤 Profil Anda</h3>
            <p>Selamat datang, <strong><?= htmlspecialchars(getUser()['name']) ?></strong>!</p>
        </div>
    <?php endif; ?>

    <div class="users-grid">
        <?php if (empty($users)): ?>
            <p>Tidak ada data pengguna.</p>
        <?php else: ?>
            <?php foreach ($users as $user): ?>
                <div class="user-card">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                    
                    <div class="user-info">
                        <h3><?= htmlspecialchars($user['name']) ?></h3>
                        <p class="user-email">✉️ <?= htmlspecialchars($user['email']) ?></p>
                        
                        <!-- User Status Badge -->
                        <div class="user-badges">
                            <span class="badge badge-<?= $user['status'] === 'VIP' ? 'purple' : 'blue' ?>">
                                <?= $user['status'] === 'VIP' ? '👑' : '👤' ?> 
                                <?= htmlspecialchars($user['status']) ?>
                            </span>
                            
                            <span class="badge badge-<?= $user['level'] === 'Gold' ? 'yellow' : ($user['level'] === 'Silver' ? 'gray' : 'orange') ?>">
                                <?= htmlspecialchars($user['level']) ?> Level
                            </span>
                            
                            <?php if ($user['verified']): ?>
                                <span class="badge badge-green">
                                    ✓ Verified
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Profile Completion -->
                        <div class="profile-completion">
                            <label>Profil Lengkap:</label>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $user['profile_completion'] ?>%">
                                    <?= $user['profile_completion'] ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (!isLoggedIn()): ?>
        <div class="cta-section">
            <h3>💡 Belum punya akun?</h3>
            <p>Daftar sekarang untuk mendapatkan akses ke produk premium dan promo eksklusif!</p>
            <a href="login.php" class="btn btn-primary">🚀 Login / Daftar</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'views/footer.php'; ?>