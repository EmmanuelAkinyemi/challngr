<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../challngr/auth/login.html');
    exit();
}
// Set the page title
$pageTitle = "Code Challenge - Challngr";
ob_start(); // Start output buffering for the content
?>

<!-- Full viewport container -->
<div class="flex flex-col h-screen bg-gray-900 text-white">
    <!-- Main content area that fills remaining space -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Left: Challenge Description -->
        <div class="w-1/2 p-6 overflow-y-auto border-r border-accent">
            <h2 class="text-2xl font-bold mb-4">Code Challenge</h2>
            <div class="prose prose-invert max-w-none">
                <p class="mb-4">Write a function <code class="bg-gray-800 px-2 py-1 rounded">factorial($n)</code> in PHP that returns the factorial of <code class="bg-gray-800 px-2 py-1 rounded">$n</code>. Use the editor on the right and click <span class="font-semibold text-accent-orange">Run Code</span> to test.</p>

                <div class="mb-6 p-4 bg-gray-800 rounded-lg border border-accent">
                    <h3 class="text-lg font-semibold mb-2 text-accent-orange">Requirements</h3>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Input: Integer <code class="bg-gray-700 px-1 rounded">n</code></li>
                        <li>Output: Factorial of <code class="bg-gray-700 px-1 rounded">n</code></li>
                        <li>Example: <code class="bg-gray-700 px-1 rounded">factorial(5) === 120</code></li>
                    </ul>
                </div>

                <div class="mb-6 p-4 bg-gray-800 rounded-lg border border-accent">
                    <h3 class="text-lg font-semibold mb-2 text-accent-orange">Instructions</h3>
                    <ol class="list-decimal list-inside space-y-2">
                        <li>Write your solution in the PHP editor</li>
                        <li>Click "Run Code" to test your solution</li>
                        <li>View the output in the PHP Output panel</li>
                        <li>You can also experiment with HTML/CSS/JS in the other editors</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Right: Editors, Timer, and Output -->
        <div class="w-1/2 flex flex-col overflow-hidden">
            <!-- Timer and Heading -->
            <div class="sticky top-0 w-full flex justify-between items-center shadow-lg p-4 bg-gray-800 backdrop-blur-lg z-10 border-b border-accent">
                <h2 class="text-2xl font-bold text-accent-orange">Your Editor</h2>
                <div id="timer" class="text-lg font-semibold accent-gradient text-white px-3 py-1 rounded-full">30:00</div>
            </div>

            <!-- Scrollable content area -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                <!-- Editors -->
                <div id="editors" class="grid gap-4">
                    <div class="rounded-lg border border-accent p-2 bg-gray-800">
                        <h3 class="mb-2 font-medium">HTML</h3>
                        <div id="htmlEditor" class="h-40 border border-accent rounded-lg"></div>
                    </div>
                    <div class="rounded-lg border border-accent p-2 bg-gray-800">
                        <h3 class="mb-2 font-medium">CSS</h3>
                        <div id="cssEditor" class="h-40 border border-accent rounded-lg"></div>
                    </div>
                    <div class="rounded-lg border border-accent p-2 bg-gray-800">
                        <h3 class="mb-2 font-medium">JavaScript</h3>
                        <div id="jsEditor" class="h-40 border border-accent rounded-lg"></div>
                    </div>
                    <div class="rounded-lg border border-accent p-2 bg-gray-800">
                        <h3 class="mb-2 font-medium">PHP</h3>
                        <div id="phpEditor" class="h-40 border border-accent rounded-lg"></div>
                    </div>
                </div>

                <!-- Run Button -->
                <button id="runBtn" class="self-end px-6 py-2 accent-gradient text-white font-medium rounded-lg hover:opacity-90 transition-all shadow-md">
                    Run Code
                </button>

                <!-- Outputs -->
                <div class="grid gap-4">
                    <div class="rounded-lg border border-accent p-2 bg-gray-800">
                        <h3 class="mb-2 font-medium">Rendered Output</h3>
                        <iframe id="output" class="w-full h-60 border border-accent rounded-lg bg-white"></iframe>
                    </div>
                    <div class="rounded-lg border border-accent p-2 bg-gray-800">
                        <h3 class="mb-2 font-medium">PHP Output</h3>
                        <pre id="phpOutput" class="w-full h-60 border border-accent rounded-lg bg-gray-900 text-white p-2 overflow-auto font-mono text-sm"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the layout
include 'app-layout.php';
?>