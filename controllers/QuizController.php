<?php
require_once __DIR__ . '/../models/Quiz.php';

class QuizController
{
    private $quizModel;

    public function __construct()
    {
        $this->quizModel = new Quiz();
    }

    public function showQuiz($quizId)
    {
        // Verify user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login.html');
            exit;
        }

        $quiz = $this->quizModel->getQuiz($quizId);
        $questions = $this->quizModel->getQuestions($quizId);

        if (!$quiz || !$questions) {
            die('Quiz not found');
        }

        // Prepare questions with options
        foreach ($questions as &$question) {
            $question['options'] = $this->quizModel->getOptions($question['id']);
        }

        // Start a new attempt if not already started
        if (!isset($_SESSION['quiz_attempt_id'])) {
            $_SESSION['quiz_attempt_id'] = $this->quizModel->startAttempt(
                $_SESSION['user_id'],
                $quizId
            );
        }

        include __DIR__ . '/../views/quiz.php';
    }

    public function submitQuiz($quizId)
    {
        // Verify user is logged in and has an active attempt
        if (!isset($_SESSION['user_id'], $_SESSION['quiz_attempt_id'])) {
            header('Location: /auth/login.html');
            exit;
        }

        $attemptId = $_SESSION['quiz_attempt_id'];
        $questions = $this->quizModel->getQuestions($quizId);

        // Process each question
        foreach ($questions as $question) {
            $answerKey = 'question_' . $question['id'];

            if (isset($_POST[$answerKey])) {
                $this->quizModel->submitAnswer(
                    $attemptId,
                    $question['id'],
                    $_POST[$answerKey] // For multiple choice
                    // Add handling for other question types here
                );
            }
        }

        // Complete the attempt
        $result = $this->quizModel->completeAttempt($attemptId);

        // Store results in session and clean up
        $_SESSION['quiz_results'] = [
            'score' => $result['score'],
            'total' => array_sum(array_column($questions, 'points')),
            'details' => $this->quizModel->getUserAnswers($attemptId)
        ];

        unset($_SESSION['quiz_attempt_id']);

        header('Location: quiz.php?results=1');
        exit;
    }
}
