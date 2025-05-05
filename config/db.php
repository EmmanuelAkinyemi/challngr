<?php
// config/db.php

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 1 for debugging, 0 in production

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'challngr');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_PERSISTENT         => false,
    PDO::ATTR_TIMEOUT            => 3 // Connection timeout in seconds
];

// Connection string
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

try {
    // Create PDO instance
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Test connection immediately
    $pdo->query("SELECT 1");
    
} catch (PDOException $e) {
    // Log error securely (don't expose details to users)
    error_log("Database connection failed: " . $e->getMessage());
    
    // Custom error page or JSON response if API
    if (php_sapi_name() === 'cli') {
        die("Database connection failed");
    } else {
        header('Content-Type: application/json');
        die(json_encode([
            'success' => false,
            'message' => 'Service unavailable',
            'error_code' => 'DB_CONN_ERR'
        ]));
    }
}

// Optional: Database helper functions
function db_prepare($sql) {
    global $pdo;
    return $pdo->prepare($sql);
}

function db_execute($sql, $params = []) {
    $stmt = db_prepare($sql);
    $stmt->execute($params);
    return $stmt;
}