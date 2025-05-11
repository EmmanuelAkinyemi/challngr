<?php
class UserChallenge {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function recordSubmission(int $userId, float $score): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO user_challenges (user_id, score, submitted_at)
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$userId, $score]);
        return $this->pdo->lastInsertId();
    }
    
    public function getUserSubmissions(int $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT id, score, submitted_at
            FROM user_challenges
            WHERE user_id = ?
            ORDER BY submitted_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}