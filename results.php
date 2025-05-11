<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/UserChallenge.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$score = $_GET['score'] ?? 0;

// Get user's submission history
try {
    $pdo = require __DIR__ . '/config/db.php';
    $userChallenge = new UserChallenge($pdo);
    $submissions = $userChallenge->getUserSubmissions($userId);
} catch (Exception $e) {
    $error = $e->getMessage();
}

$pageTitle = "Your Results";
ob_start();
?>

<div class="min-h-screen bg-gray-900 text-gray-100 p-6">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Challenge Results</h1>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-900/50 border border-red-700 text-red-100 p-3 rounded-lg mb-6">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php else: ?>
            <!-- Results Summary -->
            <div class="bg-gray-800 rounded-xl p-6 mb-8 border border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-semibold">Your Score</h2>
                    <div class="text-4xl font-bold <?= $score >= 80 ? 'text-green-400' : ($score >= 50 ? 'text-yellow-400' : 'text-red-400') ?>">
                        <?= $score ?>%
                    </div>
                </div>
                
                <div class="w-full bg-gray-700 rounded-full h-4 mb-4">
                    <div class="bg-<?= $score >= 80 ? 'green' : ($score >= 50 ? 'yellow' : 'red') ?>-500 h-4 rounded-full" 
                         style="width: <?= $score ?>%"></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <div class="text-sm text-gray-400">Challenges Completed</div>
                        <div class="text-2xl font-bold">4/4</div>
                    </div>
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <div class="text-sm text-gray-400">Correct Solutions</div>
                        <div class="text-2xl font-bold"><?= round($score / 100 * 4) ?>/4</div>
                    </div>
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <div class="text-sm text-gray-400">Average Score</div>
                        <div class="text-2xl font-bold"><?= $score ?>%</div>
                    </div>
                </div>
            </div>
            
            <!-- Submission History -->
            <h2 class="text-2xl font-semibold mb-4">Your Submissions</h2>
            <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700">
                <table class="w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php foreach ($submissions as $submission): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?= date('M j, Y g:i a', strtotime($submission['submitted_at'])) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    <?= $submission['score'] >= 80 ? 'bg-green-900 text-green-100' : 
                                       ($submission['score'] >= 50 ? 'bg-yellow-900 text-yellow-100' : 'bg-red-900 text-red-100') ?>">
                                    <?= $submission['score'] ?>%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="submission-details.php?id=<?= $submission['id'] ?>" class="text-blue-400 hover:text-blue-300">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-8 flex justify-end">
                <a href="dashboard.php" class="px-4 py-2 bg-blue-600 rounded-lg hover:bg-blue-500">
                    Return to Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'app-layout.php';
?>