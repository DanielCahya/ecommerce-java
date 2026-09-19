<?php

// define('API_BASE_URL', 'http://localhost:4567/api');
define('API_BASE_URL', 'http://192.168.1.3:4567/api');
define('API_TIMEOUT', 30);

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user']) && isset($_SESSION['token']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function getUser() {
    return $_SESSION['user'] ?? null;
}

function getToken() {
    return $_SESSION['token'] ?? null;
}