<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Get quiz ID from URL
$quiz_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$quiz_id) {
    header('Location: quizzes.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_quiz':
                $title = $_POST['title'];
                $description = $_POST['description'];
                $difficulty = $_POST['difficulty'];
                $time_limit = $_POST['time_limit'];
                
                $stmt = $pdo->prepare("UPDATE quizzes SET title = ?, description = ?, difficulty = ?, time_limit = ? WHERE id = ?");
                $stmt->execute([$title, $description, $difficulty, $time_limit, $quiz_id]);
                break;
                
            case 'add_question':
                $question_text = $_POST['question_text'];
                $question_type = $_POST['question_type'];
                $points = $_POST['points'];
                
                $stmt = $pdo->prepare("INSERT INTO quiz_questions (quiz_id, question_text, question_type, points) VALUES (?, ?, ?, ?)");
                $stmt->execute([$quiz_id, $question_text, $question_type, $points]);
                break;
                
            case 'delete_question':
                $question_id = $_POST['question_id'];
                $stmt = $pdo->prepare("DELETE FROM quiz_questions WHERE id = ? AND quiz_id = ?");
                $stmt->execute([$question_id, $quiz_id]);
                break;
                
            case 'add_answer':
                $question_id = $_POST['question_id'];
                $answer_text = $_POST['answer_text'];
                $is_correct = isset($_POST['is_correct']) ? 1 : 0;
                
                $stmt = $pdo->prepare("INSERT INTO quiz_answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)");
                $stmt->execute([$question_id, $answer_text, $is_correct]);
                break;
                
            case 'delete_answer':
                $answer_id = $_POST['answer_id'];
                $stmt = $pdo->prepare("DELETE FROM quiz_answers WHERE id = ?");
                $stmt->execute([$answer_id]);
                break;
        }
    }
}

// Fetch quiz details
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    header('Location: quizzes.php');
    exit();
}

// Fetch questions
$stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY id");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch answers for each question
foreach ($questions as &$question) {
    $stmt = $pdo->prepare("SELECT * FROM quiz_answers WHERE question_id = ? ORDER BY id");
    $stmt->execute([$question['id']]);
    $question['answers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quiz - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                            <a class="nav-link active" href="quizzes.php">
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
                    <h1 class="h2">Edit Quiz: <?php echo htmlspecialchars($quiz['title']); ?></h1>
                    <a href="quizzes.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Quizzes
                    </a>
                </div>

                <!-- Quiz Details Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Quiz Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_quiz">
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($quiz['title']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($quiz['description']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="difficulty" class="form-label">Difficulty</label>
                                <select class="form-select" id="difficulty" name="difficulty" required>
                                    <option value="easy" <?php echo $quiz['difficulty'] === 'easy' ? 'selected' : ''; ?>>Easy</option>
                                    <option value="medium" <?php echo $quiz['difficulty'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                    <option value="hard" <?php echo $quiz['difficulty'] === 'hard' ? 'selected' : ''; ?>>Hard</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                                <input type="number" class="form-control" id="time_limit" name="time_limit" value="<?php echo htmlspecialchars($quiz['time_limit']); ?>" min="1" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Update Quiz Details</button>
                        </form>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Questions</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                            <i class="fas fa-plus"></i> Add Question
                        </button>
                    </div>
                    <div class="card-body">
                        <?php foreach ($questions as $question): ?>
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Question #<?php echo $question['id']; ?></h6>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                    <input type="hidden" name="action" value="delete_question">
                                    <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><?php echo htmlspecialchars($question['question_text']); ?></p>
                                <small class="text-muted">
                                    Type: <?php echo ucfirst(str_replace('_', ' ', $question['question_type'])); ?> |
                                    Points: <?php echo $question['points']; ?>
                                </small>
                                
                                <!-- Answers -->
                                <div class="mt-3">
                                    <h6>Answers:</h6>
                                    <ul class="list-group">
                                        <?php foreach ($question['answers'] as $answer): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <?php echo htmlspecialchars($answer['answer_text']); ?>
                                            <?php if ($answer['is_correct']): ?>
                                            <span class="badge bg-success">Correct</span>
                                            <?php endif; ?>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this answer?');">
                                                <input type="hidden" name="action" value="delete_answer">
                                                <input type="hidden" name="answer_id" value="<?php echo $answer['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    
                                    <!-- Add Answer Form -->
                                    <form method="POST" class="mt-2">
                                        <input type="hidden" name="action" value="add_answer">
                                        <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="answer_text" placeholder="New answer" required>
                                            <div class="input-group-text">
                                                <input type="checkbox" name="is_correct" class="form-check-input mt-0">
                                                <label class="ms-2 mb-0">Correct</label>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Add Answer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Question Modal -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_question">
                        
                        <div class="mb-3">
                            <label for="question_text" class="form-label">Question Text</label>
                            <textarea class="form-control" id="question_text" name="question_text" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="question_type" class="form-label">Question Type</label>
                            <select class="form-select" id="question_type" name="question_type" required>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="short_answer">Short Answer</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="points" class="form-label">Points</label>
                            <input type="number" class="form-control" id="points" name="points" value="1" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 