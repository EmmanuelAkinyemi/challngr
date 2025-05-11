<?php
// config/db.php

declare(strict_types=1);

try {
    $host = 'localhost';
    $dbname = 'challngr';
    $username = 'root';
    $password = '';
    $charset = 'utf8mb4';

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=$charset",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    // Test connection immediately
    $pdo->query('SELECT 1');
    
    return $pdo;

} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    die('Database connection failed. Please try again later.');
}