<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/ChallengeController.php';

// Initialize controller with database connection
$pdo = require __DIR__ . '/config/db.php';
$challengeController = new ChallengeController($pdo);

if (!isset($_SESSION['user_id'])) {
    header('Location: ../challngr/auth/login.html');
    exit();
}

// Get challenge ID from URL or default to first challenge
$challengeId = $_GET['id'] ?? $challengeController->getFirstChallengeId();

try {
    // Get all challenges for navigation
    $challenges = $challengeController->getAllChallenges();
    $totalChallenges = count($challenges);
    $currentChallengeIndex = array_search($challengeId, array_column($challenges, 'id'));
    $isLastChallenge = ($currentChallengeIndex === $totalChallenges - 1);

    // Get complete challenge data
    $challengeData = $challengeController->getFullChallengeData($challengeId);

    // Extract data for template
    $challenge = $challengeData['challenge'];
    $requirements = $challengeData['requirements'];
    $examples = $challengeData['examples'];
    $testCases = $challengeData['tests'];
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "Failed to load challenge. Please try again later.";
} catch (Exception $e) {
    $error = $e->getMessage();
}

$pageTitle = $challenge['title'] ?? "Code Challenge - Challngr";
ob_start();
?>

<div class="flex flex-col lg:flex-row min-h-screen bg-gray-900 text-gray-100 rounded-xl overflow-hidden">
    <!-- Challenge Panel -->
    <div class="lg:w-1/3 w-full p-4 overflow-y-auto border-b lg:border-b-0 lg:border-r border-gray-700 rounded-t-xl lg:rounded-tr-none lg:rounded-l-xl">
        <!-- Challenge Navigation -->
        <div class="mb-6">
            <label for="challenge-select" class="block text-sm font-medium mb-1">Select Challenge:</label>
            <select id="challenge-select" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2 text-sm"
                onchange="window.location.href='?id='+this.value">
                <?php foreach ($challenges as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $c['id'] == $challengeId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-900/50 border border-red-700 text-red-100 p-3 rounded-lg mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <h2 class="text-xl font-bold"><?= htmlspecialchars($challenge['title']) ?></h2>
                <div class="space-y-3 text-sm">
                    <p><?= nl2br(htmlspecialchars($challenge['description'])) ?></p>

                    <div class="p-3 bg-gray-800 rounded-xl border border-gray-700">
                        <h3 class="font-medium text-accent-orange mb-1">Requirements</h3>
                        <ul class="list-disc list-inside space-y-1 text-gray-300">
                            <?php foreach ($requirements as $req): ?>
                                <li><?= nl2br(htmlspecialchars($req['content'])) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?php if ($examples): ?>
                        <div class="p-3 bg-gray-800 rounded-xl border border-gray-700">
                            <h3 class="font-medium text-accent-orange mb-1">Examples</h3>
                            <ul class="list-disc list-inside space-y-1 text-gray-300">
                                <?php foreach ($examples as $ex): ?>
                                    <li><?= nl2br(htmlspecialchars($ex['content'])) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Editor Panel -->
    <div class="flex-1 flex flex-col overflow-hidden rounded-b-xl lg:rounded-bl-none lg:rounded-r-xl">
        <!-- Header with challenge difficulty -->
        <div class="flex justify-between items-center p-2 bg-gray-800 border-b border-gray-700 rounded-tr-xl lg:rounded-tl-none">
            <div class="flex space-x-2 items-center">
                <span class="px-2 py-1 text-xs rounded-lg 
                    <?= match($challenge['difficulty'] ?? 'medium') {
                        'easy' => 'bg-green-900 text-green-100',
                        'medium' => 'bg-yellow-900 text-yellow-100',
                        'hard' => 'bg-red-900 text-red-100'
                    } ?>">
                    <?= ucfirst($challenge['difficulty'] ?? 'medium') ?>
                </span>
                <span class="text-xs text-gray-400">
                    Challenge <?= $currentChallengeIndex + 1 ?> of <?= $totalChallenges ?>
                </span>
            </div>
            <div class="flex space-x-2">
                <div id="timer" class="px-3 py-1 bg-gray-700 rounded-full text-sm font-mono">30:00</div>
            </div>
        </div>

        <!-- Editor workspace -->
        <div class="flex-1 overflow-hidden grid grid-rows-2">
            <!-- Editors -->
            <div class="overflow-hidden grid grid-cols-1 md:grid-cols-2 gap-1 p-1 bg-gray-800">
                <!-- HTML Editor -->
                <div class="flex flex-col h-full">
                    <div class="text-xs px-2 py-1 bg-gray-700 rounded-t-lg">HTML</div>
                    <div id="htmlEditor" class="flex-1 border border-gray-700 rounded-b-lg" 
                        data-starter="<?= htmlspecialchars($challenge['html_starter'] ?? '<!-- HTML here -->') ?>"></div>
                </div>
                
                <!-- CSS Editor -->
                <div class="flex flex-col h-full">
                    <div class="text-xs px-2 py-1 bg-gray-700 rounded-t-lg">CSS</div>
                    <div id="cssEditor" class="flex-1 border border-gray-700 rounded-b-lg"
                        data-starter="<?= htmlspecialchars($challenge['css_starter'] ?? '/* CSS here */') ?>"></div>
                </div>
                
                <!-- JavaScript Editor -->
                <div class="flex flex-col h-full">
                    <div class="text-xs px-2 py-1 bg-gray-700 rounded-t-lg">JS</div>
                    <div id="jsEditor" class="flex-1 border border-gray-700 rounded-b-lg"
                        data-starter="<?= htmlspecialchars($challenge['js_starter'] ?? '// JavaScript here') ?>"></div>
                </div>
                
                <!-- PHP Editor -->
                <div class="flex flex-col h-full">
                    <div class="text-xs px-2 py-1 bg-gray-700 rounded-t-lg">PHP</div>
                    <div id="phpEditor" class="flex-1 border border-gray-700 rounded-b-lg"
                        data-starter="<?= htmlspecialchars($challenge['starter_code'] ?? '<?php\n// PHP here') ?>"></div>
                </div>
            </div>

            <!-- Output section -->
            <div class="overflow-hidden grid grid-cols-1 md:grid-cols-2 gap-1 p-1 bg-gray-800">
                <div class="flex flex-col h-full">
                    <div class="flex justify-between items-center text-xs px-2 py-1 bg-gray-700 rounded-t-lg">
                        <span>Preview</span>
                        <button id="runBtn" class="px-2 py-0.5 bg-accent-orange text-xs rounded-lg hover:opacity-90">
                            Run Code
                        </button>
                    </div>
                    <iframe id="output" class="flex-1 bg-white border border-gray-700 rounded-b-lg"></iframe>
                </div>
                <div class="flex flex-col h-full">
                    <div class="text-xs px-2 py-1 bg-gray-700 rounded-t-lg">Test Results</div>
                    <pre id="testResults" class="flex-1 font-mono text-xs p-2 overflow-auto bg-gray-900 border border-gray-700 rounded-b-lg"></pre>
                </div>
            </div>
        </div>

        <!-- Navigation buttons -->
        <div class="flex justify-between p-4 bg-gray-800 border-t border-gray-700">
            <?php if ($currentChallengeIndex > 0): ?>
                <a href="?id=<?= $challenges[$currentChallengeIndex - 1]['id'] ?>" 
                   class="px-4 py-2 bg-gray-700 rounded-lg hover:bg-gray-600">
                    Previous Challenge
                </a>
            <?php else: ?>
                <div></div> <!-- Empty div for spacing -->
            <?php endif; ?>

            <?php if ($isLastChallenge): ?>
                <button id="submitAllBtn" class="px-4 py-2 bg-green-600 rounded-lg hover:bg-green-500">
                    Submit All Challenges
                </button>
            <?php else: ?>
                <a href="?id=<?= $challenges[$currentChallengeIndex + 1]['id'] ?>" 
                   class="px-4 py-2 bg-blue-600 rounded-lg hover:bg-blue-500">
                    Next Challenge
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.36.1/min/vs/loader.js"></script>

<script>
// Monaco Editor Initialization
require.config({ paths: { 'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@0.36.1/min/vs' }});
require(['vs/editor/editor.main'], function() {
    const editors = {};
    const editorIds = ['html', 'css', 'js', 'php'];
    
    // Initialize all editors
    editorIds.forEach(id => {
        const container = document.getElementById(`${id}Editor`);
        const starterCode = container.dataset.starter;
        
        editors[id] = monaco.editor.create(container, {
            value: starterCode,
            language: id,
            theme: 'vs-dark',
            automaticLayout: true,
            minimap: { enabled: false },
            fontSize: 14
        });
    });

    // Run button functionality
    document.getElementById('runBtn').addEventListener('click', async () => {
        const outputFrame = document.getElementById('output');
        const testResults = document.getElementById('testResults');
        
        try {
            // Get editor values
            const html = editors.html.getValue();
            const css = editors.css.getValue();
            const js = editors.js.getValue();
            const php = editors.php.getValue();
            
            // Update iframe
            outputFrame.srcdoc = `
                <!DOCTYPE html>
                <html>
                    <head><style>${css}</style></head>
                    <body>${html}<script>${js}</script></body>
                </html>
            `;
            
            // Test PHP code
            testResults.textContent = "Running tests...";
            const response = await fetch('run-challenge.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    challenge_id: <?= $challengeId ?>,
                    html, css, js, php
                })
            });
            
            const result = await response.json();
            testResults.innerHTML = result.success 
                ? `<span class="text-green-400">✓ ${result.message}</span>`
                : `<span class="text-red-400">✗ ${result.message}</span>`;
                
        } catch (error) {
            testResults.textContent = `Error: ${error.message}`;
        }
    });

    // Submit all challenges
    document.getElementById('submitAllBtn')?.addEventListener('click', async () => {
        const response = await fetch('submit-challenges.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: <?= $_SESSION['user_id'] ?> })
        });
        
        const result = await response.json();
        if (result.success) {
            window.location.href = 'results.php?score=' + result.score;
        } else {
            alert('Submission failed: ' + result.message);
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include 'app-layout.php';
?>