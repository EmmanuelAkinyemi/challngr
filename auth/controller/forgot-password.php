<?php
header('Content-Type: application/json');

// Include configuration files
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/session.php';

$response = ['success' => false, 'message' => ''];

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and sanitize email
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please provide a valid email address');
    }

    // Check if email exists in database
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('No account found with that email address');
    }

    // Generate reset token (valid for 1 hour)
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiration
    
    // Store token in database
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
    $stmt->execute([$token, $expires, $email]);

    // In a real application, you would send an email here
    // For demo purposes, we'll just return the token
    $resetLink = "http://localhost/auth/reset-password.html?token=$token";
    
    $response['success'] = true;
    $response['message'] = 'Password reset link sent';
    $response['debug_link'] = $resetLink; // Remove this in production

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $response['message'] = 'A database error occurred';
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>