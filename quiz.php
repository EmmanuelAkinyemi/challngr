<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.html');
    exit;
}

// Fetch quiz from database
try {
    // Get quiz ID (you could also pass this as a parameter)
    $quizId = 1; // Assuming we're using quiz ID 1 for this example

    // Fetch quiz details
    $quizStmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
    $quizStmt->execute([$quizId]);
    $quizDetails = $quizStmt->fetch(PDO::FETCH_ASSOC);

    if (!$quizDetails) {
        throw new Exception("Quiz not found");
    }

    // Fetch questions for this quiz
    $questionsStmt = $pdo->prepare("
        SELECT q.id, q.question_text, q.points 
        FROM questions q 
        WHERE q.quiz_id = ? 
        ORDER BY q.id
    ");
    $questionsStmt->execute([$quizId]);
    $questions = $questionsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch options for each question
    foreach ($questions as &$question) {
        $optionsStmt = $pdo->prepare("
            SELECT id, option_text, is_correct 
            FROM options 
            WHERE question_id = ? 
            ORDER BY id
        ");
        $optionsStmt->execute([$question['id']]);
        $question['options'] = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    unset($question); // Break the reference

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    $results = [];
    $totalPoints = 0;

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Create quiz attempt
        $attemptStmt = $pdo->prepare("
            INSERT INTO user_quiz_attempts (user_id, quiz_id) 
            VALUES (?, ?)
        ");
        $attemptStmt->execute([$_SESSION['user_id'], $quizId]);
        $attemptId = $pdo->lastInsertId();

        foreach ($questions as $question) {
            $totalPoints += $question['points'];
            $userAnswerKey = 'question_' . $question['id'];
            $selectedOptionId = $_POST[$userAnswerKey] ?? null;

            // Find correct option ID
            $correctOptionId = null;
            foreach ($question['options'] as $option) {
                if ($option['is_correct']) {
                    $correctOptionId = $option['id'];
                    break;
                }
            }

            $isCorrect = ($selectedOptionId == $correctOptionId);

            if ($isCorrect) {
                $score += $question['points'];
            }

            // Record user answer
            $answerStmt = $pdo->prepare("
                INSERT INTO user_answers (attempt_id, question_id, option_id, is_correct)
                VALUES (?, ?, ?, ?)
            ");
            $answerStmt->execute([
                $attemptId,
                $question['id'],
                $selectedOptionId,
                $isCorrect
            ]);

            // Store for results display
            $userAnswerText = 'Not answered';
            $correctAnswerText = '';

            foreach ($question['options'] as $option) {
                if ($option['id'] == $selectedOptionId) {
                    $userAnswerText = $option['option_text'];
                }
                if ($option['is_correct']) {
                    $correctAnswerText = $option['option_text'];
                }
            }

            $results[] = [
                'question' => $question['question_text'],
                'user_answer' => $userAnswerText,
                'correct_answer' => $correctAnswerText,
                'is_correct' => $isCorrect,
                'points' => $question['points']
            ];
        }

        // Update attempt with final score
        $updateStmt = $pdo->prepare("
            UPDATE user_quiz_attempts 
            SET score = ?, completed_at = NOW() 
            WHERE id = ?
        ");
        $updateStmt->execute([$score, $attemptId]);

        $pdo->commit();

        // Store results in session
        $_SESSION['quiz_results'] = [
            'score' => $score,
            'total' => $totalPoints,
            'details' => $results
        ];

        header('Location: quiz.php?results=1');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error processing quiz: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quizDetails['title']); ?> - Challngr</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .quiz-option:hover {
            transform: translateY(-2px);
        }

        .quiz-option.correct {
            background-color: #4CAF50;
            color: white;
        }

        .quiz-option.incorrect {
            background-color: #F44336;
            color: white;
        }
    </style>
</head>

<body class="h-full">
    <div class="min-h-full">
        <?php include "_partials/header.php"; ?>

        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <?php if (isset($_GET['results'])): ?>
                    <!-- Results View -->
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">Your Quiz Results</h2>
                    <div class="mb-8">
                        <div class="text-4xl font-bold text-center mb-2">
                            <?php echo $_SESSION['quiz_results']['score']; ?>/<?php echo $_SESSION['quiz_results']['total']; ?>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden relative">
                            <div
                                class="h-full rounded-full transition-all duration-1000 ease-out flex items-center justify-end pr-2 text-xs font-bold text-white"
                                style="
                                    width: <?php echo ($_SESSION['quiz_results']['score'] / $_SESSION['quiz_results']['total']) * 100; ?>%;
                                    background: linear-gradient(90deg, #ff3e3e, #ff6d3a);
                                    min-width: 2rem; 
                                    "
                                id="score-progress">
                                <?php echo round(($_SESSION['quiz_results']['score'] / $_SESSION['quiz_results']['total']) * 100); ?>%
                            </div>
                        </div>
                    </div>

                    <?php foreach ($_SESSION['quiz_results']['details'] as $result): ?>
                        <div class="mb-6 p-4 border rounded-lg <?php echo $result['is_correct'] ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'; ?>">
                            <h3 class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($result['question']); ?></h3>
                            <p class="text-sm mb-1">Your answer: <span class="<?php echo $result['is_correct'] ? 'text-green-600' : 'text-red-600'; ?> font-medium"><?php echo htmlspecialchars($result['user_answer']); ?></span></p>
                            <p class="text-sm">Correct answer: <span class="text-green-600 font-medium"><?php echo htmlspecialchars($result['correct_answer']); ?></span></p>
                            <p class="text-sm mt-1">Points: <?php echo $result['is_correct'] ? '+' . $result['points'] : '0'; ?></p>
                        </div>
                    <?php endforeach; ?>

                    <a href="quiz.php" class="mt-4 inline-block px-6 py-2 bg-orange-600 text-white rounded-md hover:bg-accent-red hover:bg-orange-500 transition">
                        Retake Quiz
                    </a>

                <?php else: ?>
                    <!-- Quiz Form -->
                    <h2 class="text-2xl font-bold mb-6 text-gray-800"><?php echo htmlspecialchars($quizDetails['title']); ?></h2>
                    <?php if ($quizDetails['description']): ?>
                        <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($quizDetails['description']); ?></p>
                    <?php endif; ?>

                    <form method="POST" id="quizForm">
                        <?php foreach ($questions as $question): ?>
                            <div class="mb-8">
                                <h3 class="text-lg font-medium mb-3"><?php echo htmlspecialchars($question['question_text']); ?></h3>
                                <div class="space-y-2">
                                    <?php foreach ($question['options'] as $option): ?>
                                        <label class="block quiz-option cursor-pointer p-3 border rounded-md hover:shadow-md transition">
                                            <input type="radio"
                                                name="question_<?php echo $question['id']; ?>"
                                                value="<?php echo $option['id']; ?>"
                                                class="mr-2"
                                                required>
                                            <?php echo htmlspecialchars($option['option_text']); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <button type="submit" class="w-full py-3 px-4 bg-orange-600 text-white font-medium rounded-md hover:bg-accent-red transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-red">
                            Submit Quiz
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Add interactive effects
        document.querySelectorAll('.quiz-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from siblings
                this.parentNode.querySelectorAll('.quiz-option').forEach(el => {
                    el.classList.remove('bg-gray-100', 'border-accent-orange');
                });

                // Add selected class to clicked option
                this.classList.add('bg-gray-100', 'border-accent-orange');
                this.querySelector('input').checked = true;
            });
        });

        // Timer functionality (if quiz has time limit)
        <?php if ($quizDetails['time_limit'] > 0): ?>
            const timeLimit = <?php echo $quizDetails['time_limit'] * 60; ?>;
            let timeLeft = timeLimit;

            const timer = setInterval(() => {
                timeLeft--;
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                document.getElementById('timer').textContent =
                    `${minutes}:${seconds.toString().padStart(2, '0')}`;

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    document.getElementById('quizForm').submit();
                }
            }, 1000);
        <?php endif; ?>
    </script>
</body>

</html>