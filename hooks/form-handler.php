<?php
// form-handler.php
header('Content-Type: text/plain; charset=utf-8');

// Grab real POST data
$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$message) {
    echo "❌ Error: Missing form data.\n";
    exit;
}

// Simulate processing (e.g. send mail, write DB, etc.)
echo "✅ Form submitted successfully!\n\n";
echo "Name: "    . htmlspecialchars($name)    . "\n";
echo "Email: "   . htmlspecialchars($email)   . "\n";
echo "Message: " . htmlspecialchars($message) . "\n";
