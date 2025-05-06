<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Challngr</title>
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: {
                            red: '#ff3e3e',
                            orange: '#ff6d3a',
                            DEFAULT: '#ff3e3e'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-taken {
            background-color: #E5E7EB;
            color: #374151;
        }
        .badge-available {
            background-color: #D1FAE5;
            color: #065F46;
        }
    </style>

    <?php
    require_once __DIR__ . '/config/session.php';
    require_once __DIR__ . '/config/db.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../challngr/auth/login.html');
        exit();
    }

    // Fetch available quizzes
    $availableQuizzes = [];
    $quizPerformance = [];
    
    try {
        // Get available quizzes (not taken by user)
        $quizStmt = $pdo->prepare("
            SELECT q.id, q.title, q.description 
            FROM quizzes q
            LEFT JOIN user_quiz_attempts uqa ON q.id = uqa.quiz_id AND uqa.user_id = ?
            WHERE uqa.id IS NULL
        ");
        $quizStmt->execute([$_SESSION['user_id']]);
        $availableQuizzes = $quizStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get quiz performance (taken quizzes)
        $performanceStmt = $pdo->prepare("
            SELECT q.id, q.title, uqa.score, uqa.completed_at,
                   (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) AS total_questions,
                   (uqa.score / (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id)) * 100 AS percentage
            FROM user_quiz_attempts uqa
            JOIN quizzes q ON uqa.quiz_id = q.id
            WHERE uqa.user_id = ?
            ORDER BY uqa.completed_at DESC
        ");
        $performanceStmt->execute([$_SESSION['user_id']]);
        $quizPerformance = $performanceStmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
    }
    ?>
</head>

<body class="h-full">
    <div class="min-h-full">
        <?php include "_partials/header.php"; ?>

        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                    Welcome, <?php
                                $email = $_SESSION['email'] ?? 'user@example.com';
                                $usernameParts = explode('@', $email);
                                $username = $usernameParts[0];
                                $cleanUsername = preg_replace('/[0-9]+/', '', $username);
                                echo htmlspecialchars(ucfirst($cleanUsername));
                                ?>
                </h1>
            </div>
        </header>

        <main>
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 space-y-8">
                <!-- Quick Stats Cards -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-lg border-l-4 border-accent-red bg-white p-6 shadow">
                        <h3 class="text-lg font-medium text-gray-900">Your Progress</h3>
                        <div class="mt-4 h-2 w-full bg-gray-200 rounded-full">
                            <div class="h-full rounded-full accent-gradient" style="width: 65%"></div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow">
                        <h3 class="text-lg font-medium text-gray-900 py-2">Quick Actions</h3>
                        <a href="code-challenge.php" class="mt-4 px-4 py-2 rounded-md text-white font-medium accent-gradient hover:shadow-md transition-all">
                            Start Challenge
                        </a>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow">
                        <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                        <p class="mt-2 text-accent-orange font-medium"><?php echo count($availableQuizzes); ?> new challenges available</p>
                    </div>
                </div>

                <!-- Available Quizzes Table -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Available Quizzes</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (count($availableQuizzes) > 0): ?>
                                    <?php foreach ($availableQuizzes as $quiz): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($quiz['title']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($quiz['description']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="badge badge-available">Available</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="quiz.php?id=<?php echo $quiz['id']; ?>" class="text-accent-orange hover:text-accent-red">Take Quiz</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No available quizzes at the moment</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quiz Performance Table -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Your Quiz Performance</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quiz</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Completed</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (count($quizPerformance) > 0): ?>
                                    <?php foreach ($quizPerformance as $performance): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($performance['title']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M j, Y', strtotime($performance['completed_at'])); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $performance['score']; ?>/<?php echo $performance['total_questions']; ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex items-center">
                                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                                        <div class="bg-accent-orange h-2.5 rounded-full" 
                                                             style="width: <?php echo $performance['percentage']; ?>%"></div>
                                                    </div>
                                                    <span><?php echo round($performance['percentage']); ?>%</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="quiz-results.php?id=<?php echo $performance['id']; ?>" class="text-accent-orange hover:text-accent-red">View Details</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No quiz attempts yet</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Avatar generation
        document.addEventListener('DOMContentLoaded', function() {
            const avatar = document.getElementById('user-avatar');
            const name = avatar.dataset.name;
            let initials = 'U';
            
            if (name.includes('@')) {
                initials = name.split('@')[0].charAt(0).toUpperCase();
            } else {
                const parts = name.split(' ');
                initials = parts.map(part => part.charAt(0).join('').substring(0, 2).toUpperCase();
            }
            
            const colors = [
                '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A',
                '#98D8C8', '#F06292', '#7986CB', '#9575CD',
                '#64B5F6', '#4DB6AC', '#81C784', '#FFD54F',
                '#FF8A65', '#A1887F', '#90A4AE'
            ];
            const randomColor = colors[Math.floor(Math.random() * colors.length)];
            
            avatar.textContent = initials;
            avatar.style.backgroundColor = randomColor;
        });

        // Logout functionality
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.getElementById('logout-btn');
            
            if (logoutBtn) {
                logoutBtn.addEventListener('click', async function(e) {
                    e.preventDefault();
                    
                    try {
                        const response = await fetch('./auth/controller/logout.php', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            window.location.href = result.redirect;
                        } else {
                            console.error('Logout failed:', result.message);
                            alert('Logout failed. Please try again.');
                        }
                    } catch (error) {
                        console.error('Logout error:', error);
                        alert('An error occurred during logout.');
                    }
                });
            }
        });
    </script>
</body>
</html>