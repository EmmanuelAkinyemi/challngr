<?php
class ChallengeModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getFirstChallengeId() {
        $stmt = $this->pdo->query("SELECT id FROM challenges ORDER BY id ASC LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : 1; // Default to 1 if no challenges exist
    }

    public function getAllChallenges() {
        $stmt = $this->pdo->query("SELECT * FROM challenges ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChallengeById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM challenges WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTestsForChallenge($challengeId) {
        $stmt = $this->pdo->prepare("SELECT * FROM challenge_tests WHERE challenge_id = ?");
        $stmt->execute([$challengeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveSubmission($challengeId, $userId, $code, $passed, $testResults, $score) {
        $stmt = $this->pdo->prepare("
            INSERT INTO challenge_submissions 
            (challenge_id, user_id, code, passed, test_results, score, submitted_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([
            $challengeId,
            $userId,
            $code,
            $passed ? 1 : 0,
            json_encode($testResults),
            $score
        ]);
    }
}