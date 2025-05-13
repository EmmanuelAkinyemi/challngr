<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/db.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.html');
    exit;
}

// Get student name from session
$studentName = $_SESSION['email'] ?? 'Student';

// Get attempt ID
$attemptId = $_GET['id'] ?? null;
if (!$attemptId) {
    header('Location: dashboard.php');
    exit;
}

try {
    // Fetch quiz attempt
    $attemptStmt = $pdo->prepare("
        SELECT uqa.*, q.title AS quiz_title
        FROM user_quiz_attempts uqa
        JOIN quizzes q ON uqa.quiz_id = q.id
        WHERE uqa.id = ? AND uqa.user_id = ?
    ");
    $attemptStmt->execute([$attemptId, $_SESSION['user_id']]);
    $attempt = $attemptStmt->fetch(PDO::FETCH_ASSOC);

    if (!$attempt) {
        throw new Exception("Quiz attempt not found");
    }

    // Fetch answers
    $answersStmt = $pdo->prepare("
        SELECT 
            q.id AS question_id,
            q.question_text,
            q.points,
            o.id AS selected_option_id,
            o.option_text AS selected_option_text,
            co.id AS correct_option_id,
            co.option_text AS correct_option_text,
            ua.is_correct
        FROM user_answers ua
        JOIN questions q ON ua.question_id = q.id
        LEFT JOIN options o ON ua.option_id = o.id
        LEFT JOIN options co ON co.question_id = q.id AND co.is_correct = 1
        WHERE ua.attempt_id = ?
        ORDER BY q.id
    ");
    $answersStmt->execute([$attemptId]);
    $answers = $answersStmt->fetchAll(PDO::FETCH_ASSOC);

    $totalQuestions = count($answers);
    $correctAnswers = array_sum(array_column($answers, 'is_correct'));
    $percentage = $totalQuestions ? round(($correctAnswers / $totalQuestions) * 100) : 0;

    // Grade logic
    if ($percentage >= 70) $grade = 'A';
    elseif ($percentage >= 60) $grade = 'B';
    elseif ($percentage >= 50) $grade = 'C';
    elseif ($percentage >= 45) $grade = 'D';
    elseif ($percentage >= 40) $grade = 'E';
    else $grade = 'F';
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results - Challngr</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .accent-gradient {
            background: linear-gradient(135deg, #ff3e3e, #ff6d3a);
        }

        .accent-gradient:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body class="h-full">
    <div class="min-h-full">
        <?php include "_partials/header.php"; ?>

        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div id="resultCard" class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b bg-blue-50 border-blue-200">
                    <h1 class="text-2xl font-bold text-gray-800">Student: <?php echo htmlspecialchars($studentName); ?></h1>
                </div>

                <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Quiz Results</h2>
                            <p class="mt-1 text-gray-600"><?php echo htmlspecialchars($attempt['quiz_title']); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Completed on <?php echo date('M j, Y g:i a', strtotime($attempt['completed_at'])); ?></p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="w-full md:w-1/2">
                            <h3 class="text-lg font-medium text-gray-900">Your Score</h3>
                            <div class="mt-4">
                                <div class="text-4xl font-bold"><?php echo $correctAnswers; ?>/<?php echo $totalQuestions; ?></div>
                                <div class="mt-2 w-full bg-gray-200 rounded-full h-4">
                                    <div class="h-full rounded-full accent-gradient" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <p class="mt-2 text-gray-600"><?php echo $percentage; ?>% Correct</p>
                                <p class="text-gray-800 mt-1">Grade: <span class="font-semibold"><?php echo $grade; ?></span></p>
                            </div>
                        </div>
                        <div class="w-full md:w-1/2">
                            <h3 class="text-lg font-medium text-gray-900">Performance Breakdown</h3>
                            <div class="mt-4 grid grid-cols-2 gap-4">
                                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                    <p class="text-sm font-medium text-green-800">Correct Answers</p>
                                    <p class="text-2xl font-bold text-green-600"><?php echo $correctAnswers; ?></p>
                                </div>
                                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                    <p class="text-sm font-medium text-red-800">Incorrect Answers</p>
                                    <p class="text-2xl font-bold text-red-600"><?php echo $totalQuestions - $correctAnswers; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-5">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Question Breakdown</h3>
                    <div class="space-y-6">
                        <?php foreach ($answers as $index => $answer): ?>
                            <div class="p-4 rounded-lg border <?php echo $answer['is_correct'] ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'; ?>">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">
                                            Question #<?php echo $index + 1; ?>: <?php echo htmlspecialchars($answer['question_text']); ?>
                                        </h4>
                                        <p class="mt-2 text-sm <?php echo $answer['is_correct'] ? 'text-green-600' : 'text-red-600'; ?>">
                                            Your answer: <?php echo htmlspecialchars($answer['selected_option_text'] ?? 'Not answered'); ?>
                                        </p>
                                        <?php if (!$answer['is_correct']): ?>
                                            <p class="mt-1 text-sm text-green-600">
                                                Correct answer: <?php echo htmlspecialchars($answer['correct_option_text']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $answer['is_correct'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $answer['is_correct'] ? 'Correct' : 'Incorrect'; ?>
                                    </span>
                                </div>
                                <?php if ($answer['points'] > 1): ?>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Points: <?php echo $answer['is_correct'] ? '+' . $answer['points'] : '0'; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <a href="dashboard.php" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Back to Dashboard
                </a>
                <button onclick="downloadResult()" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white accent-gradient hover:opacity-90">
                    Download Result
                </button>
            </div>
        </main>
    </div>

    <script>
        function downloadResult() {
            const resultCard = document.getElementById('resultCard');
            html2canvas(resultCard).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Quiz_Result_<?php echo preg_replace("/[^a-zA-Z0-9]/", "_", $studentName); ?>.jpeg';
                link.href = canvas.toDataURL("image/jpeg");
                link.click();
            });
        }
    </script>
</body>

</html>