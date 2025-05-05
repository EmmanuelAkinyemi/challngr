<?php
require_once __DIR__ . '/config/session.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.html');
    exit;
}

// Quiz questions and answers
$quiz = [
    [
        'question' => 'What is the capital of France?',
        'options' => ['London', 'Paris', 'Berlin', 'Madrid'],
        'answer' => 1 // Paris
    ],
    [
        'question' => 'Which language is primarily used for web development?',
        'options' => ['Java', 'Python', 'JavaScript', 'C++'],
        'answer' => 2 // JavaScript
    ],
    [
        'question' => 'What does HTML stand for?',
        'options' => [
            'Hyper Text Markup Language',
            'High Tech Modern Language',
            'Hyperlinks and Text Markup Language',
            'Home Tool Markup Language'
        ],
        'answer' => 0 // Hyper Text Markup Language
    ],
    [
        'question' => 'Which of these is a frontend framework?',
        'options' => ['Django', 'Laravel', 'React', 'Flask'],
        'answer' => 2 // React
    ],
    [
        'question' => 'What year was JavaScript created?',
        'options' => ['1990', '1995', '2000', '1985'],
        'answer' => 1 // 1995
    ]
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    $results = [];

    foreach ($quiz as $index => $item) {
        $userAnswer = $_POST['question_' . $index] ?? null;
        $isCorrect = ($userAnswer == $item['answer']);

        if ($isCorrect) {
            $score++;
        }

        $results[] = [
            'question' => $item['question'],
            'user_answer' => $item['options'][$userAnswer] ?? 'Not answered',
            'correct_answer' => $item['options'][$item['answer']],
            'is_correct' => $isCorrect
        ];
    }

    // Store results in session
    $_SESSION['quiz_results'] = [
        'score' => $score,
        'total' => count($quiz),
        'details' => $results
    ];

    header('Location: quiz.php?results=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - Challngr</title>
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
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-accent-orange h-4 rounded-full"
                                style="width: <?php echo ($_SESSION['quiz_results']['score'] / $_SESSION['quiz_results']['total']) * 100; ?>%"></div>
                        </div>
                    </div>

                    <?php foreach ($_SESSION['quiz_results']['details'] as $result): ?>
                        <div class="mb-6 p-4 border rounded-lg <?php echo $result['is_correct'] ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'; ?>">
                            <h3 class="font-semibold text-lg mb-2"><?php echo $result['question']; ?></h3>
                            <p class="text-sm mb-1">Your answer: <span class="<?php echo $result['is_correct'] ? 'text-green-600' : 'text-red-600'; ?> font-medium"><?php echo $result['user_answer']; ?></span></p>
                            <p class="text-sm">Correct answer: <span class="text-green-600 font-medium"><?php echo $result['correct_answer']; ?></span></p>
                        </div>
                    <?php endforeach; ?>

                    <a href="quiz.php" class="mt-4 inline-block px-6 py-2 bg-accent-orange text-white rounded-md hover:bg-accent-red transition">
                        Retake Quiz
                    </a>

                <?php else: ?>
                    <!-- Quiz Form -->
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">Test Your Knowledge</h2>
                    <form method="POST" id="quizForm">
                        <?php foreach ($quiz as $index => $item): ?>
                            <div class="mb-8">
                                <h3 class="text-lg font-medium mb-3"><?php echo ($index + 1) . '. ' . $item['question']; ?></h3>
                                <div class="space-y-2">
                                    <?php foreach ($item['options'] as $optionIndex => $option): ?>
                                        <label class="block quiz-option cursor-pointer p-3 border rounded-md hover:shadow-md transition">
                                            <input type="radio" name="question_<?php echo $index; ?>" value="<?php echo $optionIndex; ?>" class="mr-2" required>
                                            <?php echo $option; ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <button type="submit" class="w-full py-3 px-4 bg-accent-orange text-white font-medium rounded-md hover:bg-accent-red transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-red">
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
    </script>
</body>

</html>