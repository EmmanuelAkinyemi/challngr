<?php
require_once __DIR__ . '/../../config/db.php';

class ChallengeSeeder {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function run() {
        try {
            $this->truncateTables();
            $this->seedChallenges();
            echo "Successfully seeded 4 programming challenges!\n";
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage() . "\n";
        }
    }
    
    private function truncateTables() {
        $tables = ['challenges', 'challenge_requirements', 'challenge_tests'];
        
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        
        foreach ($tables as $table) {
            $this->pdo->exec("TRUNCATE TABLE $table");
        }
        
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    }
    
    private function seedChallenges() {
        $challenges = [
            [
                'title' => 'Factorial Calculator',
                'description' => 'Write a function that calculates the factorial of a non-negative integer.',
                'difficulty' => 'easy',
                'starter_code' => 'function factorial($n) {
    // Your code here
}',
                'requirements' => [
                    'Function should accept one integer parameter',
                    'Should return the factorial of the input number',
                    'Factorial of 0 should return 1',
                    'Should handle positive integers only'
                ],
                'examples' => [
                    'factorial(5) → 120',
                    'factorial(3) → 6',
                    'factorial(0) → 1'
                ],
                'tests' => [
                    ['input' => '5', 'expected_output' => '120'],
                    ['input' => '3', 'expected_output' => '6'],
                    ['input' => '0', 'expected_output' => '1'],
                    ['input' => '1', 'expected_output' => '1']
                ]
            ],
            [
                'title' => 'Palindrome Checker',
                'description' => 'Create a function that checks if a given string is a palindrome (reads the same backward as forward).',
                'difficulty' => 'easy',
                'starter_code' => 'function isPalindrome($str) {
    // Your code here
}',
                'requirements' => [
                    'Function should accept one string parameter',
                    'Should return true if the string is a palindrome, false otherwise',
                    'Should ignore case differences',
                    'Should ignore spaces and punctuation'
                ],
                'examples' => [
                    'isPalindrome("racecar") → true',
                    'isPalindrome("Hello") → false',
                    'isPalindrome("A man, a plan, a canal, Panama") → true'
                ],
                'tests' => [
                    ['input' => '"racecar"', 'expected_output' => 'true'],
                    ['input' => '"hello"', 'expected_output' => 'false'],
                    ['input' => '"A man, a plan, a canal, Panama"', 'expected_output' => 'true'],
                    ['input' => '"12321"', 'expected_output' => 'true']
                ]
            ],
            [
                'title' => 'Prime Number Checker',
                'description' => 'Implement a function that checks if a number is prime.',
                'difficulty' => 'medium',
                'starter_code' => 'function isPrime($num) {
    // Your code here
}',
                'requirements' => [
                    'Function should accept one integer parameter',
                    'Should return true if the number is prime, false otherwise',
                    'Should handle numbers greater than 1',
                    'Should be optimized to not check all numbers up to n'
                ],
                'examples' => [
                    'isPrime(7) → true',
                    'isPrime(10) → false',
                    'isPrime(2) → true'
                ],
                'tests' => [
                    ['input' => '7', 'expected_output' => 'true'],
                    ['input' => '10', 'expected_output' => 'false'],
                    ['input' => '2', 'expected_output' => 'true'],
                    ['input' => '1', 'expected_output' => 'false']
                ]
            ],
            [
                'title' => 'Array Flattener',
                'description' => 'Write a function that flattens a multi-dimensional array into a single level.',
                'difficulty' => 'medium',
                'starter_code' => 'function flattenArray($array) {
    // Your code here
}',
                'requirements' => [
                    'Function should accept one array parameter',
                    'Should return a single-level array',
                    'Should preserve values',
                    'Should handle nested arrays of any depth'
                ],
                'examples' => [
                    'flattenArray([1, [2, [3, [4]], 5]) → [1, 2, 3, 4, 5]',
                    'flattenArray([[1, 2], [3, 4]]) → [1, 2, 3, 4]'
                ],
                'tests' => [
                    ['input' => '[1, [2, [3, [4]], 5]', 'expected_output' => '[1, 2, 3, 4, 5]'],
                    ['input' => '[[1, 2], [3, 4]]', 'expected_output' => '[1, 2, 3, 4]'],
                    ['input' => '[]', 'expected_output' => '[]'],
                    ['input' => '[1, 2, 3]', 'expected_output' => '[1, 2, 3]']
                ]
            ]
        ];
        
        foreach ($challenges as $challengeData) {
            // Insert challenge
            $stmt = $this->pdo->prepare("
                INSERT INTO challenges (title, description, difficulty, starter_code)
                VALUES (:title, :description, :difficulty, :starter_code)
            ");
            $stmt->execute([
                ':title' => $challengeData['title'],
                ':description' => $challengeData['description'],
                ':difficulty' => $challengeData['difficulty'],
                ':starter_code' => $challengeData['starter_code']
            ]);
            $challengeId = $this->pdo->lastInsertId();
            
            // Insert requirements
            foreach ($challengeData['requirements'] as $req) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO challenge_requirements (challenge_id, content, is_example)
                    VALUES (:challenge_id, :content, 0)
                ");
                $stmt->execute([
                    ':challenge_id' => $challengeId,
                    ':content' => $req
                ]);
            }
            
            // Insert examples
            foreach ($challengeData['examples'] as $ex) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO challenge_requirements (challenge_id, content, is_example)
                    VALUES (:challenge_id, :content, 1)
                ");
                $stmt->execute([
                    ':challenge_id' => $challengeId,
                    ':content' => $ex
                ]);
            }
            
            // Insert test cases
            foreach ($challengeData['tests'] as $test) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO challenge_tests (challenge_id, input, expected_output)
                    VALUES (:challenge_id, :input, :expected_output)
                ");
                $stmt->execute([
                    ':challenge_id' => $challengeId,
                    ':input' => $test['input'],
                    ':expected_output' => $test['expected_output']
                ]);
            }
        }
    }
}

// Usage
// Usage
try {
    // Require the db config file
    $dbConfigPath = __DIR__ . '/../../config/db.php';
    
    if (!file_exists($dbConfigPath)) {
        throw new RuntimeException("Database configuration file not found");
    }
    
    $pdo = require $dbConfigPath;
    
    // Verify we got a PDO instance
    if (!($pdo instanceof PDO)) {
        throw new RuntimeException("Database configuration did not return a valid PDO instance");
    }
    
    // Create and run the seeder
    $seeder = new ChallengeSeeder($pdo);
    $seeder->run();
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}