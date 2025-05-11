<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../challngr/auth/login.html');
    exit();
}

// Set the page title
$pageTitle = "Quiz Reports - Challngr";

// Fetch user's quiz attempts
$quizAttempts = [];
try {
    $stmt = $pdo->prepare("
        SELECT q.title, uqa.score, uqa.completed_at, 
               (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) AS total_questions,
               ROUND((uqa.score / (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id)) * 100) AS percentage
        FROM user_quiz_attempts uqa
        JOIN quizzes q ON uqa.quiz_id = q.id
        WHERE uqa.user_id = ?
        ORDER BY uqa.completed_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $quizAttempts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "Failed to load quiz reports. Please try again later.";
}

ob_start(); // Start output buffering
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Your Quiz Reports</h1>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php elseif (empty($quizAttempts)): ?>
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
            You haven't completed any quizzes yet. Take a quiz to see your results here!
        </div>
    <?php else: ?>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quiz Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Completed</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($quizAttempts as $attempt): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($attempt['title']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M j, Y g:i a', strtotime($attempt['completed_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($attempt['score'] . '/' . htmlspecialchars($attempt['total_questions'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                        <div class="bg-accent-orange h-2.5 rounded-full"
                                            style="width: <?php echo htmlspecialchars($attempt['percentage']); ?>%"></div>
                                    </div>
                                    <span><?php echo htmlspecialchars($attempt['percentage']); ?>%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php
                                $statusClass = $attempt['percentage'] >= 70 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                $statusText = $attempt['percentage'] >= 70 ? 'Passed' : 'Failed';
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo $statusClass; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 bg-white shadow-md rounded-lg p-4">
            <h2 class="text-xl font-semibold text-gray-800 mb-3">Performance Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-blue-600">Quizzes Taken</p>
                    <p class="text-2xl font-bold text-blue-800"><?php echo count($quizAttempts); ?></p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-green-600">Average Score</p>
                    <p class="text-2xl font-bold text-green-800">
                        <?php
                        $totalPercentage = array_reduce($quizAttempts, function ($carry, $item) {
                            return $carry + $item['percentage'];
                        }, 0);
                        echo round($totalPercentage / count($quizAttempts)) . '%';
                        ?>
                    </p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <p class="text-sm text-purple-600">Pass Rate</p>
                    <p class="text-2xl font-bold text-purple-800">
                        <?php
                        $passed = array_filter($quizAttempts, function ($item) {
                            return $item['percentage'] >= 70;
                        });
                        echo round(count($passed) / count($quizAttempts) * 100) . '%';
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean(); // Get the buffered content
include 'app-layout.php'; // Include the layout
?>