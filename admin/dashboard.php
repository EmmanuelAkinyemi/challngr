<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: ../auth/login.html');
    exit();
}

// Fetch statistics
$stats = [
    'quizzes' => $pdo->query("SELECT COUNT(*) FROM quizzes")->fetchColumn(),
    'challenges' => $pdo->query("SELECT COUNT(*) FROM code_challenges")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'total_attempts' => $pdo->query("SELECT COUNT(*) FROM quiz_attempts")->fetchColumn() + 
                       $pdo->query("SELECT COUNT(*) FROM challenge_attempts")->fetchColumn()
];

// Fetch recent quizzes
$recent_quizzes = $pdo->query("SELECT * FROM quizzes ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent challenges
$recent_challenges = $pdo->query("SELECT * FROM code_challenges ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Fetch difficulty distribution
$difficulty_stats = [
    'quizzes' => [
        'easy' => $pdo->query("SELECT COUNT(*) FROM quizzes WHERE difficulty = 'easy'")->fetchColumn(),
        'medium' => $pdo->query("SELECT COUNT(*) FROM quizzes WHERE difficulty = 'medium'")->fetchColumn(),
        'hard' => $pdo->query("SELECT COUNT(*) FROM quizzes WHERE difficulty = 'hard'")->fetchColumn()
    ],
    'challenges' => [
        'easy' => $pdo->query("SELECT COUNT(*) FROM code_challenges WHERE difficulty = 'easy'")->fetchColumn(),
        'medium' => $pdo->query("SELECT COUNT(*) FROM code_challenges WHERE difficulty = 'medium'")->fetchColumn(),
        'hard' => $pdo->query("SELECT COUNT(*) FROM code_challenges WHERE difficulty = 'hard'")->fetchColumn()
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="quizzes.php">
                                <i class="fas fa-question-circle"></i> Quizzes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="challenges.php">
                                <i class="fas fa-code"></i> Code Challenges
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Quizzes</h5>
                                <h2 class="card-text"><?php echo $stats['quizzes']; ?></h2>
                                <i class="fas fa-question-circle fa-2x position-absolute top-50 end-0 me-3 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Challenges</h5>
                                <h2 class="card-text"><?php echo $stats['challenges']; ?></h2>
                                <i class="fas fa-code fa-2x position-absolute top-50 end-0 me-3 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <h2 class="card-text"><?php echo $stats['users']; ?></h2>
                                <i class="fas fa-users fa-2x position-absolute top-50 end-0 me-3 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Attempts</h5>
                                <h2 class="card-text"><?php echo $stats['total_attempts']; ?></h2>
                                <i class="fas fa-tasks fa-2x position-absolute top-50 end-0 me-3 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Quiz Difficulty Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="quizDifficultyChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Challenge Difficulty Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="challengeDifficultyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Quizzes</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <?php foreach ($recent_quizzes as $quiz): ?>
                                    <a href="edit_quiz.php?id=<?php echo $quiz['id']; ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($quiz['title']); ?></h6>
                                            <small class="text-muted"><?php echo date('M d, Y', strtotime($quiz['created_at'])); ?></small>
                                        </div>
                                        <small class="text-muted">Difficulty: <?php echo ucfirst($quiz['difficulty']); ?></small>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Challenges</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <?php foreach ($recent_challenges as $challenge): ?>
                                    <a href="edit_challenge.php?id=<?php echo $challenge['id']; ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($challenge['title']); ?></h6>
                                            <small class="text-muted"><?php echo date('M d, Y', strtotime($challenge['created_at'])); ?></small>
                                        </div>
                                        <small class="text-muted">Difficulty: <?php echo ucfirst($challenge['difficulty']); ?></small>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Quiz Difficulty Chart
        new Chart(document.getElementById('quizDifficultyChart'), {
            type: 'pie',
            data: {
                labels: ['Easy', 'Medium', 'Hard'],
                datasets: [{
                    data: [
                        <?php echo $difficulty_stats['quizzes']['easy']; ?>,
                        <?php echo $difficulty_stats['quizzes']['medium']; ?>,
                        <?php echo $difficulty_stats['quizzes']['hard']; ?>
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                }]
            }
        });

        // Challenge Difficulty Chart
        new Chart(document.getElementById('challengeDifficultyChart'), {
            type: 'pie',
            data: {
                labels: ['Easy', 'Medium', 'Hard'],
                datasets: [{
                    data: [
                        <?php echo $difficulty_stats['challenges']['easy']; ?>,
                        <?php echo $difficulty_stats['challenges']['medium']; ?>,
                        <?php echo $difficulty_stats['challenges']['hard']; ?>
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                }]
            }
        });
    </script>
</body>
</html>
