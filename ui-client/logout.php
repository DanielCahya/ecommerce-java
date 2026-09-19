<?php
require_once 'config/config.php';

// Clear session
session_unset();
session_destroy();

// Redirect to homepage
header('Location: index.php');
exit;