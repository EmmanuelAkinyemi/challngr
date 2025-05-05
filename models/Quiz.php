<?php
require_once __DIR__ . '/../config/db.php';

class Quiz
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Create a new quiz
    public function createQuiz($title, $description = null, $timeLimit = null)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO quizzes (title, description, time_limit) 
            VALUES (:title, :description, :time_limit)
        ");
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':time_limit' => $timeLimit
        ]);
        return $this->pdo->lastInsertId();
    }

    // Add a question to a quiz
    public function addQuestion($quizId, $questionText, $questionType = 'multiple_choice', $points = 1)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO questions (quiz_id, question_text, question_type, points)
            VALUES (:quiz_id, :question_text, :question_type, :points)
        ");
        $stmt->execute([
            ':quiz_id' => $quizId,
            ':question_text' => $questionText,
            ':question_type' => $questionType,
            ':points' => $points
        ]);
        return $this->pdo->lastInsertId();
    }

    // Add an option to a question
    public function addOption($questionId, $optionText, $isCorrect = false)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO options (question_id, option_text, is_correct)
            VALUES (:question_id, :option_text, :is_correct)
        ");
        $stmt->execute([
            ':question_id' => $questionId,
            ':option_text' => $optionText,
            ':is_correct' => $isCorrect
        ]);
        return $this->pdo->lastInsertId();
    }

    // Get quiz by ID
    public function getQuiz($quizId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM quizzes WHERE id = :id
        ");
        $stmt->execute([':id' => $quizId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all questions for a quiz
    public function getQuestions($quizId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM questions 
            WHERE quiz_id = :quiz_id
            ORDER BY id ASC
        ");
        $stmt->execute([':quiz_id' => $quizId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get options for a question
    public function getOptions($questionId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM options 
            WHERE question_id = :question_id
            ORDER BY id ASC
        ");
        $stmt->execute([':question_id' => $questionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Start a new quiz attempt
    public function startAttempt($userId, $quizId)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO user_quiz_attempts (user_id, quiz_id)
            VALUES (:user_id, :quiz_id)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':quiz_id' => $quizId
        ]);
        return $this->pdo->lastInsertId();
    }

    // Submit an answer
    public function submitAnswer($attemptId, $questionId, $optionId = null, $answerText = null)
    {
        // First get the correct answer
        $isCorrect = false;
        if ($optionId) {
            $stmt = $this->pdo->prepare("
                SELECT is_correct FROM options WHERE id = :option_id
            ");
            $stmt->execute([':option_id' => $optionId]);
            $isCorrect = (bool)$stmt->fetchColumn();
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO user_answers (attempt_id, question_id, option_id, answer_text, is_correct)
            VALUES (:attempt_id, :question_id, :option_id, :answer_text, :is_correct)
        ");
        $stmt->execute([
            ':attempt_id' => $attemptId,
            ':question_id' => $questionId,
            ':option_id' => $optionId,
            ':answer_text' => $answerText,
            ':is_correct' => $isCorrect
        ]);
        return $this->pdo->lastInsertId();
    }

    // Complete a quiz attempt and calculate score
    public function completeAttempt($attemptId)
    {
        // Calculate total score
        $stmt = $this->pdo->prepare("
            UPDATE user_quiz_attempts uqa
            SET 
                score = (
                    SELECT SUM(q.points) 
                    FROM user_answers ua
                    JOIN questions q ON ua.question_id = q.id
                    WHERE ua.attempt_id = :attempt_id AND ua.is_correct = TRUE
                ),
                completed_at = NOW()
            WHERE id = :attempt_id
        ");
        $stmt->execute([':attempt_id' => $attemptId]);

        // Return attempt details
        return $this->getAttempt($attemptId);
    }

    // Get attempt details
    public function getAttempt($attemptId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM user_quiz_attempts WHERE id = :id
        ");
        $stmt->execute([':id' => $attemptId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get user's answers for an attempt
    public function getUserAnswers($attemptId)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                ua.*, 
                q.question_text,
                q.points,
                o.option_text as selected_option_text,
                co.option_text as correct_option_text
            FROM user_answers ua
            JOIN questions q ON ua.question_id = q.id
            LEFT JOIN options o ON ua.option_id = o.id
            LEFT JOIN options co ON co.question_id = q.id AND co.is_correct = TRUE
            WHERE ua.attempt_id = :attempt_id
        ");
        $stmt->execute([':attempt_id' => $attemptId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
