<?php
// MOCK SESSION & ENGINE HELPER FOR LOCAL SSTI LAB
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mock user session data
if (!isset($_SESSION['ssti_user'])) {
    $_SESSION['ssti_user'] = [
        'username' => 'alice',
        'role' => 'user',
        'bio' => 'Welcome to your user profile page. Feel free to customize your greeting name!',
        'theme' => 'dark'
    ];
}

// Reset session helper
if (isset($_GET['reset_session'])) {
    unset($_SESSION['ssti_user']);
    header("Location: index.php");
    exit();
}

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
$lab_root = $base_url . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
?>