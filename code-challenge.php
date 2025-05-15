<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/ChallengeModel.php';
require_once __DIR__ . '/models/CodeEvaluator.php';
require_once __DIR__ . '/controllers/ChallengeController.php';

// Initialize components
$pdo = require __DIR__ . '/config/db.php';
$model = new ChallengeModel($pdo);
$evaluator = new CodeEvaluator();
$challengeController = new ChallengeController($model, $evaluator);

if (!isset($_SESSION['user_id'])) {
    header('Location: ../challngr/auth/login.html');
    exit();
}

// Get challenge ID from URL or default to first challenge
$challengeId = $_GET['id'] ?? 1;

try {
    // Get challenge data
    $challengeData = $challengeController->showChallenge($challengeId);
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_SESSION['user_id'];
        $code = [
            'html' => $_POST['html_code'] ?? '',
            'css' => $_POST['css_code'] ?? '',
            'js' => $_POST['js_code'] ?? '',
            'php' => $_POST['php_code'] ?? ''
        ];
        $result = $challengeController->submitSolution($challengeId, $userId, json_encode($code));
    }

    // Extract data for template
    $challenge = $challengeData['challenge'];
    $testCases = $challengeData['tests'];
} catch (Exception $e) {
    $error = $e->getMessage();
}

$pageTitle = $challenge['title'] ?? "Code Challenge - Challngr";
$pageHeader = null; // Hide default header

// Custom styles for this page
$customStyles = '
<style>
    .monaco-editor {
        height: 100%;
        width: 100%;
    }

    .editor-container {
        height: 300px;
        border: 1px solid #374151;
        border-radius: 0.375rem;
        overflow: hidden;
    }

    .test-result pre {
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .accent-gradient {
        background: linear-gradient(135deg, #ff3e3e, #ff6d3a);
    }

    .accent-gradient:hover {
        opacity: 0.9;
    }

    .preview-frame {
        width: 100%;
        height: 300px;
        border: 1px solid #374151;
        border-radius: 0.375rem;
        background: white;
    }
</style>';

ob_start();
?>

<div class="min-h-screen bg-gray-900 text-gray-100">
    <div class="flex flex-col lg:flex-row">
        <!-- Challenge Panel -->
        <div class="lg:w-1/4 p-6 border-r border-gray-700">
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-white"><?= htmlspecialchars($challenge['title'] ?? "Challenge") ?></h1>
                    <span class="px-3 py-1 text-sm rounded-full bg-gradient-to-r from-orange-600 to-red-600">
                        <?= htmlspecialchars($challenge['difficulty'] ?? 'Medium') ?>
                    </span>
                </div>

                <div class="prose prose-invert prose-sm">
                    <?= nl2br(htmlspecialchars($challenge['description'] ?? "")) ?>
                </div>

                <?php if (!empty($testCases)): ?>
                    <div class="space-y-3">
                        <h2 class="text-lg font-semibold text-gray-300">Test Cases</h2>
                        <div class="space-y-2">
                            <?php foreach ($testCases as $test): ?>
                                <?php if (!$test['is_hidden']): ?>
                                    <div class="p-3 bg-gray-800 rounded-lg border border-gray-700">
                                        <div class="text-sm text-gray-400">Input:</div>
                                        <code class="block p-2 mt-1 text-sm bg-gray-900 rounded"><?= htmlspecialchars($test['input']) ?></code>
                                        <div class="text-sm text-gray-400 mt-2">Expected Output:</div>
                                        <code class="block p-2 mt-1 text-sm bg-gray-900 rounded"><?= htmlspecialchars($test['expected_output']) ?></code>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="p-4 bg-red-900/50 border border-red-700 rounded-lg text-red-100">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Editor Panel -->
        <div class="lg:w-3/4 p-6">
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- HTML Editor -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-300">HTML</label>
                            <span class="text-xs text-gray-500">index.html</span>
                        </div>
                        <div class="editor-container">
                            <div id="htmlEditor" data-language="html" data-starter="<?= htmlspecialchars($challenge['html_starter'] ?? '<!-- Write your HTML here -->') ?>"></div>
                        </div>
                        <textarea name="html_code" class="hidden"></textarea>
                    </div>

                    <!-- CSS Editor -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-300">CSS</label>
                            <span class="text-xs text-gray-500">styles.css</span>
                        </div>
                        <div class="editor-container">
                            <div id="cssEditor" data-language="css" data-starter="<?= htmlspecialchars($challenge['css_starter'] ?? '/* Write your CSS here */') ?>"></div>
                        </div>
                        <textarea name="css_code" class="hidden"></textarea>
                    </div>

                    <!-- JavaScript Editor -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-300">JavaScript</label>
                            <span class="text-xs text-gray-500">script.js</span>
                        </div>
                        <div class="editor-container">
                            <div id="jsEditor" data-language="javascript" data-starter="<?= htmlspecialchars($challenge['js_starter'] ?? '// Write your JavaScript here') ?>"></div>
                        </div>
                        <textarea name="js_code" class="hidden"></textarea>
                    </div>

                    <!-- PHP Editor -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-300">PHP</label>
                            <span class="text-xs text-gray-500">index.php</span>
                        </div>
                        <div class="editor-container">
                            <div id="phpEditor" data-language="php" data-starter="<?= htmlspecialchars($challenge['php_starter'] ?? '<?php\n// Write your PHP code here') ?>"></div>
                        </div>
                        <textarea name="php_code" class="hidden"></textarea>
                    </div>
                </div>

                <!-- Preview Panel -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-300">Preview</label>
                        <div class="flex gap-2">
                            <button type="button" id="previewBtn" class="px-4 py-2 text-sm font-medium text-white bg-gray-700 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-orange-500">
                                Preview
                            </button>
                            <button type="submit" id="submitBtn" class="px-4 py-2 text-sm font-medium text-white rounded-lg accent-gradient focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-orange-500">
                                Submit Solution
                            </button>
                        </div>
                    </div>
                    <iframe id="previewFrame" class="preview-frame"></iframe>
                </div>

                <?php if (isset($result)): ?>
                    <div class="space-y-4">
                        <h2 class="text-xl font-bold flex items-center justify-between">
                            Test Results
                            <span class="text-<?= $result['passed'] ? 'green' : 'red' ?>-500">
                                Score: <?= $result['score'] ?>%
                            </span>
                        </h2>

                        <div class="space-y-3">
                            <?php foreach ($result['test_results'] as $test): ?>
                                <?php if (!$test['hidden']): ?>
                                    <div class="p-4 rounded-lg border <?= $test['passed'] ? 'bg-green-900/20 border-green-700' : 'bg-red-900/20 border-red-700' ?>">
                                        <div class="font-medium <?= $test['passed'] ? 'text-green-400' : 'text-red-400' ?>">
                                            <?= $test['passed'] ? '✓ Passed' : '✗ Failed' ?>
                                        </div>
                                        <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <div class="font-medium text-gray-400">Input:</div>
                                                <pre class="mt-1 p-2 bg-gray-800 rounded"><?= htmlspecialchars($test['input']) ?></pre>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-400">Expected:</div>
                                                <pre class="mt-1 p-2 bg-gray-800 rounded"><?= htmlspecialchars($test['expected']) ?></pre>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-400">Actual:</div>
                                                <pre class="mt-1 p-2 bg-gray-800 rounded"><?= htmlspecialchars($test['actual']) ?></pre>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize editors
    const editors = {};
    const editorElements = document.querySelectorAll('[id$="Editor"]');
    
    editorElements.forEach(element => {
        const language = element.dataset.language;
        const starterCode = element.dataset.starter || '';
        
        editors[language] = monaco.editor.create(element, {
            value: starterCode,
            language: language,
            theme: 'vs-dark',
            automaticLayout: true,
            minimap: { enabled: false },
            fontSize: 14,
            scrollBeyondLastLine: false,
            roundedSelection: true,
            padding: { top: 10 }
        });
    });

    // Handle preview button click
    const previewBtn = document.getElementById('previewBtn');
    const previewFrame = document.getElementById('previewFrame');
    
    if (previewBtn && previewFrame) {
        previewBtn.addEventListener('click', function() {
            const html = editors.html.getValue();
            const css = editors.css.getValue();
            const js = editors.javascript.getValue();
            
            const content = `
                <!DOCTYPE html>
                <html>
                <head>
                    <style>${css}</style>
                </head>
                <body>
                    ${html}
                    <script>${js}<\/script>
                </body>
                </html>
            `;
            
            previewFrame.srcdoc = content;
        });
    }

    // Handle form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            // Update hidden textareas with editor content
            document.querySelector('[name="html_code"]').value = editors.html.getValue();
            document.querySelector('[name="css_code"]').value = editors.css.getValue();
            document.querySelector('[name="js_code"]').value = editors.javascript.getValue();
            document.querySelector('[name="php_code"]').value = editors.php.getValue();
        });
    }
});
</script>

<?php
$content = ob_get_clean();
include 'app-layout.php';
?>