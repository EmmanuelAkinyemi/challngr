<?php
require_once __DIR__ . '/../config/db.php';

class Challenge {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Create a new challenge
    public function create($title, $description, $difficulty, $starterCode) {
        $stmt = $this->pdo->prepare("
            INSERT INTO challenges (title, description, difficulty, starter_code, created_at, updated_at)
            VALUES (:title, :description, :difficulty, :starter_code, NOW(), NOW())
        ");
        
        return $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':difficulty' => $difficulty,
            ':starter_code' => $starterCode
        ]);
    }

    // Get challenge by ID with related data
    public function find($id) {
        // Get the base challenge
        $stmt = $this->pdo->prepare("SELECT * FROM challenges WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $challenge = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$challenge) {
            return null;
        }

        // Get requirements
        $challenge['requirements'] = $this->getRequirements($id, false);
        
        // Get examples
        $challenge['examples'] = $this->getRequirements($id, true);
        
        // Get tests
        $challenge['tests'] = $this->getTests($id);

        return $challenge;
    }

    // Get all challenges (basic info only)
    public function all() {
        $stmt = $this->pdo->query("SELECT * FROM challenges ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update a challenge
    public function update($id, $title, $description, $difficulty, $starterCode) {
        $stmt = $this->pdo->prepare("
            UPDATE challenges 
            SET title = :title, 
                description = :description, 
                difficulty = :difficulty,
                starter_code = :starter_code,
                updated_at = NOW()
            WHERE id = :id
        ");
        
        return $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':description' => $description,
            ':difficulty' => $difficulty,
            ':starter_code' => $starterCode
        ]);
    }

    // Delete a challenge (cascade will handle related records)
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM challenges WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Helper methods for relationships
    private function getRequirements($challengeId, $isExample) {
        $stmt = $this->pdo->prepare("
            SELECT content 
            FROM challenge_requirements 
            WHERE challenge_id = :challenge_id AND is_example = :is_example
        ");
        $stmt->execute([
            ':challenge_id' => $challengeId,
            ':is_example' => $isExample
        ]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    private function getTests($challengeId) {
        $stmt = $this->pdo->prepare("
            SELECT input, expected_output 
            FROM challenge_tests 
            WHERE challenge_id = :challenge_id
        ");
        $stmt->execute([':challenge_id' => $challengeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a requirement or example
    public function addRequirement($challengeId, $content, $isExample = false) {
        $stmt = $this->pdo->prepare("
            INSERT INTO challenge_requirements (challenge_id, content, is_example)
            VALUES (:challenge_id, :content, :is_example)
        ");
        return $stmt->execute([
            ':challenge_id' => $challengeId,
            ':content' => $content,
            ':is_example' => $isExample
        ]);
    }

    // Add a test case
    public function addTest($challengeId, $input, $expectedOutput) {
        $stmt = $this->pdo->prepare("
            INSERT INTO challenge_tests (challenge_id, input, expected_output)
            VALUES (:challenge_id, :input, :expected_output)
        ");
        return $stmt->execute([
            ':challenge_id' => $challengeId,
            ':input' => $input,
            ':expected_output' => $expectedOutput
        ]);
    }
}