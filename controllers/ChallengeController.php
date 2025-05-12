<?php
// controllers/ChallengeController.php

class ChallengeController
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all challenges for navigation
     */
    public function getAllChallenges(): array
    {
        $stmt = $this->pdo->query("SELECT id, title FROM challenges ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get challenge details by ID
     */
    public function getChallenge(int $id): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                c.*,
                COALESCE(html.content, '') AS html_starter,
                COALESCE(css.content, '') AS css_starter,
                COALESCE(js.content, '') AS js_starter,
                c.starter_code AS php_starter
            FROM challenges c
            LEFT JOIN challenge_starter_codes html ON 
                html.challenge_id = c.id AND html.language = 'html'
            LEFT JOIN challenge_starter_codes css ON 
                css.challenge_id = c.id AND css.language = 'css'
            LEFT JOIN challenge_starter_codes js ON 
                js.challenge_id = c.id AND js.language = 'javascript'
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        $challenge = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$challenge) {
            throw new Exception("Challenge not found");
        }

        // Set default starter codes if empty
        $challenge['html_starter'] = $challenge['html_starter'] ?: '<!-- Add your HTML here -->';
        $challenge['css_starter'] = $challenge['css_starter'] ?: '/* Add your CSS here */';
        $challenge['js_starter'] = $challenge['js_starter'] ?: '// Add your JavaScript here';
        $challenge['php_starter'] = $challenge['php_starter'] ?: '<?php\n// Add your PHP here';

        return $challenge;
    }

    /**
     * Get requirements and examples for a challenge
     */
    public function getChallengeRequirements(int $challengeId, bool $isExample = false): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM challenge_requirements 
            WHERE challenge_id = ? AND is_example = ?
        ");
        $stmt->execute([$challengeId, $isExample]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get test cases for a challenge
     */
    public function getChallengeTests(int $challengeId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM challenge_tests WHERE challenge_id = ?");
        $stmt->execute([$challengeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get complete challenge data (challenge + requirements + tests)
     */
    public function getFullChallengeData(int $challengeId): array
    {
        try {
            // Get challenge details
            $challenge = $this->getChallenge($challengeId);
            error_log("Challenge loaded: " . print_r($challenge, true)); // Debug log

            // Get requirements
            $requirements = $this->getChallengeRequirements($challengeId, false);
            error_log("Requirements loaded: " . count($requirements)); // Debug log

            // Get examples
            $examples = $this->getChallengeRequirements($challengeId, true);
            error_log("Examples loaded: " . count($examples)); // Debug log

            // Get test cases
            $testCases = $this->getChallengeTests($challengeId);
            error_log("Test cases loaded: " . count($testCases)); // Debug log

            return [
                'challenge' => $challenge,
                'requirements' => $requirements,
                'examples' => $examples,
                'tests' => $testCases
            ];
        } catch (Exception $e) {
            error_log("Error in getFullChallengeData: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Get the first challenge ID
     */
    public function getFirstChallengeId(): int
    {
        $stmt = $this->pdo->query("SELECT id FROM challenges ORDER BY id LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'] ?? 0;
    }
}
