<?php
// run-php.php
// Safely execute user-submitted PHP code after stripping PHP tags
header('Content-Type: text/plain');

$data = file_get_contents("php://input");
$request = json_decode($data, true);
$code = $request['code'] ?? '';

// Remove opening/closing PHP tags if present
$code = preg_replace('/^\s*<\?(php)?/i', '', $code);
$code = preg_replace('/\?>\s*$/', '', $code);

if (trim($code) === '') {
    echo "No PHP code provided.";
    exit;
}

// Capture output
ob_start();
try {
    // Evaluate the code in current scope
    eval($code);
} catch (\Throwable $e) {
    // Catch parse/runtime errors
    echo "Error: " . $e->getMessage();
}
$output = ob_get_clean();

echo $output;
