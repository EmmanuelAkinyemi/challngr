<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/UserChallenge.php';

try {
    // Verify user is logged in
    if (empty($_SESSION['user_id'])) {
        throw new Exception('Authentication required');
    }

    $userId = $_SESSION['user_id'];
    $input = json_decode(file_get_contents('php://input'), true);

    // Initialize database connection
    $pdo = require __DIR__ . '/config/db.php';
    $userChallenge = new UserChallenge($pdo);

    // Calculate total score (in a real app, you'd query actual results)
    $totalChallenges = 4; // This should come from your database
    $completedChallenges = 4; // This should be calculated
    $score = round(($completedChallenges / $totalChallenges) * 100, 2);

    // Record submission
    $submissionId = $userChallenge->recordSubmission($userId, $score);

    // Return success
    echo json_encode([
        'success' => true,
        'score' => $score,
        'submission_id' => $submissionId,
        'message' => "Completed with score: {$score}%"
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}