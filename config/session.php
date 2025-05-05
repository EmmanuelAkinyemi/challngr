<?php
// config/session.php - Enhanced version with your structure in mind

// Basic security settings
session_name('SECURE_SESSION_' . md5(__DIR__));
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);

// Enable secure flag if using HTTPS
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

// Session lifetime (30 minutes)
ini_set('session.gc_maxlifetime', 1800);
ini_set('session.cookie_lifetime', 1800);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Anti-hijacking measures
if (!isset($_SESSION['security_check'])) {
    $_SESSION['security_check'] = [
        'ip' => $_SERVER['REMOTE_ADDR'],
        'ua' => $_SERVER['HTTP_USER_AGENT']
    ];
} else {
    $current_ip = $_SERVER['REMOTE_ADDR'];
    $current_ua = $_SERVER['HTTP_USER_AGENT'];
    
    if ($_SESSION['security_check']['ip'] !== $current_ip || 
        $_SESSION['security_check']['ua'] !== $current_ua) {
        session_regenerate_id(true);
        session_unset();
        session_destroy();
        die(json_encode(['error' => 'Session security check failed']));
    }
}

// CSRF token management
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function validate_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}