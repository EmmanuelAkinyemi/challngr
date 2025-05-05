<?php
header('Content-Type: application/json');

// Include necessary configuration files
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/session.php';

// Initialize response array
$response = ['success' => false, 'message' => ''];

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate input
    if (empty($_POST['email']) || empty($_POST['password'])) {
        throw new Exception('Email and password are required');
    }

    // Sanitize username
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    // Prepare SQL statement
    $stmt = $pdo->prepare("SELECT id, email, password FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['password'])) {
            // Login successful
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            // $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;

            $response['success'] = true;
            $response['redirect'] = '../dashboard.php';
        } else {
            throw new Exception('Invalid username or password');
        }
    } else {
        throw new Exception('Invalid email or password');
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $response['message'] = 'A database error occurred. Please try again later.';
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
