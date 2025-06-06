<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_challenge':
                $title = $_POST['title'];
                $description = $_POST['description'];
                $difficulty = $_POST['difficulty'];
                $time_limit = $_POST['time_limit'];
                $initial_code = $_POST['initial_code'];
                $test_cases = json_encode($_POST['test_cases']);
                
                $stmt = $pdo->prepare("INSERT INTO code_challenges (title, description, difficulty, time_limit, initial_code, test_cases) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $difficulty, $time_limit, $initial_code, $test_cases]);
                break;
                
            case 'delete_challenge':
                $challenge_id = $_POST['challenge_id'];
                $stmt = $pdo->prepare("DELETE FROM code_challenges WHERE id = ?");
                $stmt->execute([$challenge_id]);
                break;
        }
    }
}

// Fetch all challenges
$stmt = $pdo->query("SELECT * FROM code_challenges ORDER BY created_at DESC");
$challenges = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Code Challenges - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="quizzes.php">
                                <i class="fas fa-question-circle"></i> Quizzes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="challenges.php">
                                <i class="fas fa-code"></i> Code Challenges
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Code Challenges</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createChallengeModal">
                        <i class="fas fa-plus"></i> Create New Challenge
                    </button>
                </div>

                <!-- Challenges List -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Difficulty</th>
                                <th>Time Limit</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($challenges as $challenge): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($challenge['title']); ?></td>
                                <td><?php echo htmlspecialchars($challenge['difficulty']); ?></td>
                                <td><?php echo htmlspecialchars($challenge['time_limit']); ?> minutes</td>
                                <td><?php echo date('M d, Y', strtotime($challenge['created_at'])); ?></td>
                                <td>
                                    <a href="edit_challenge.php?id=<?php echo $challenge['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this challenge?');">
                                        <input type="hidden" name="action" value="delete_challenge">
                                        <input type="hidden" name="challenge_id" value="<?php echo $challenge['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Create Challenge Modal -->
    <div class="modal fade" id="createChallengeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Code Challenge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create_challenge">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="difficulty" class="form-label">Difficulty</label>
                            <select class="form-select" id="difficulty" name="difficulty" required>
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                            <input type="number" class="form-control" id="time_limit" name="time_limit" min="1" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="initial_code" class="form-label">Initial Code</label>
                            <textarea class="form-control" id="initial_code" name="initial_code" rows="10"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Test Cases</label>
                            <div id="testCases">
                                <div class="test-case mb-2">
                                    <div class="row">
                                        <div class="col">
                                            <input type="text" class="form-control" name="test_cases[input][]" placeholder="Input">
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control" name="test_cases[output][]" placeholder="Expected Output">
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-danger remove-test-case">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary mt-2" id="addTestCase">
                                <i class="fas fa-plus"></i> Add Test Case
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Challenge</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
    <script>
        // Initialize CodeMirror
        var editor = CodeMirror.fromTextArea(document.getElementById("initial_code"), {
            mode: "javascript",
            theme: "monokai",
            lineNumbers: true,
            autoCloseBrackets: true,
            matchBrackets: true,
            indentUnit: 4
        });

        // Test cases management
        document.getElementById('addTestCase').addEventListener('click', function() {
            const testCase = document.querySelector('.test-case').cloneNode(true);
            testCase.querySelectorAll('input').forEach(input => input.value = '');
            document.getElementById('testCases').appendChild(testCase);
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-test-case')) {
                const testCases = document.querySelectorAll('.test-case');
                if (testCases.length > 1) {
                    e.target.closest('.test-case').remove();
                }
            }
        });
    </script>
</body>
</html> 