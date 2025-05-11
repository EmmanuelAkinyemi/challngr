<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/ChallengeController.php';

try {
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    $challengeId = $input['challenge_id'] ?? 0;
    $userCode = [
        'html' => $input['html'] ?? '',
        'css' => $input['css'] ?? '',
        'js' => $input['js'] ?? '',
        'php' => $input['php'] ?? ''
    ];

    // Validate input
    if (empty($challengeId)) {
        throw new Exception('No challenge specified');
    }

    // Initialize controller
    $pdo = require __DIR__ . '/config/db.php';
    $controller = new ChallengeController($pdo);

    // Get test cases for this challenge
    $testCases = $controller->getChallengeTests($challengeId);

    // Execute tests (simplified example - implement your actual testing logic)
    $results = [];
    $passed = 0;
    
    foreach ($testCases as $test) {
        // This is where you'd actually execute the code against the test cases
        // For demonstration, we'll just do simple string matching
        $expected = trim($test['expected_output']);
        $actual = "Test output"; // Replace with actual execution
        
        $isCorrect = ($actual === $expected);
        if ($isCorrect) $passed++;
        
        $results[] = [
            'input' => $test['input'],
            'expected' => $expected,
            'actual' => $actual,
            'passed' => $isCorrect
        ];
    }

    // Return results
    echo json_encode([
        'success' => true,
        'passed' => $passed,
        'total' => count($testCases),
        'message' => "Passed $passed of " . count($testCases) . " tests",
        'results' => $results
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}