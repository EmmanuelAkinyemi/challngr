<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Get challenge ID from URL
$challenge_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$challenge_id) {
    header('Location: challenges.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_challenge':
                $title = $_POST['title'];
                $description = $_POST['description'];
                $difficulty = $_POST['difficulty'];
                $time_limit = $_POST['time_limit'];
                $initial_code = $_POST['initial_code'];
                $test_cases = json_encode($_POST['test_cases']);
                
                $stmt = $pdo->prepare("UPDATE code_challenges SET title = ?, description = ?, difficulty = ?, time_limit = ?, initial_code = ?, test_cases = ? WHERE id = ?");
                $stmt->execute([$title, $description, $difficulty, $time_limit, $initial_code, $test_cases, $challenge_id]);
                break;
        }
    }
}

// Fetch challenge details
$stmt = $pdo->prepare("SELECT * FROM code_challenges WHERE id = ?");
$stmt->execute([$challenge_id]);
$challenge = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$challenge) {
    header('Location: challenges.php');
    exit();
}

// Decode test cases
$test_cases = json_decode($challenge['test_cases'], true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Code Challenge - Admin Dashboard</title>
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
                    <h1 class="h2">Edit Challenge: <?php echo htmlspecialchars($challenge['title']); ?></h1>
                    <a href="challenges.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Challenges
                    </a>
                </div>

                <!-- Challenge Details Form -->
                <form method="POST">
                    <input type="hidden" name="action" value="update_challenge">
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Challenge Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($challenge['title']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($challenge['description']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="difficulty" class="form-label">Difficulty</label>
                                <select class="form-select" id="difficulty" name="difficulty" required>
                                    <option value="easy" <?php echo $challenge['difficulty'] === 'easy' ? 'selected' : ''; ?>>Easy</option>
                                    <option value="medium" <?php echo $challenge['difficulty'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                    <option value="hard" <?php echo $challenge['difficulty'] === 'hard' ? 'selected' : ''; ?>>Hard</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                                <input type="number" class="form-control" id="time_limit" name="time_limit" value="<?php echo htmlspecialchars($challenge['time_limit']); ?>" min="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Initial Code</h5>
                        </div>
                        <div class="card-body">
                            <textarea id="initial_code" name="initial_code"><?php echo htmlspecialchars($challenge['initial_code']); ?></textarea>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Test Cases</h5>
                            <button type="button" class="btn btn-primary" id="addTestCase">
                                <i class="fas fa-plus"></i> Add Test Case
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="testCases">
                                <?php if ($test_cases): ?>
                                <?php foreach ($test_cases['input'] as $index => $input): ?>
                                <div class="test-case mb-2">
                                    <div class="row">
                                        <div class="col">
                                            <input type="text" class="form-control" name="test_cases[input][]" value="<?php echo htmlspecialchars($input); ?>" placeholder="Input">
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control" name="test_cases[output][]" value="<?php echo htmlspecialchars($test_cases['output'][$index]); ?>" placeholder="Expected Output">
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-danger remove-test-case">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php else: ?>
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
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </main>
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