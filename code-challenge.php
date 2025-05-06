<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code challenge - challngr</title>
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Add your custom colors to Tailwind -->
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
    <!-- Monaco Editor Loader -->
    <script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.34.1/min/vs/loader.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Inter", sans-serif;
        }
        /* Gradient accent for interactive elements */
        .accent-gradient {
            background: linear-gradient(135deg, #ff3e3e, #ff6d3a);
        }
        .accent-gradient:hover {
            opacity: 0.9;
        }
        .border-accent {
            border-color: #ff6d3a;
        }
    </style>
</head>

<body class="min-h-full bg-gray-100">
    <!-- Header Partial -->
    <?php include "_partials/header.php"; ?>

    <div class="flex h-[calc(100vh-64px)] bg-gray-900 text-white">
        <!-- Left: Challenge Description -->
        <div class="w-1/2 p-6 overflow-auto border-r border-accent">
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
        <div class="w-1/2 flex flex-col p-6 space-y-4 overflow-auto">
            <!-- Timer and Heading -->
            <div class="sticky top-0 w-full flex justify-between items-center shadow-lg p-4 bg-gray-800 backdrop-blur-lg rounded-lg mb-4 z-10 border border-accent">
                <h2 class="text-2xl font-bold text-accent-orange">Your Editor</h2>
                <div id="timer" class="text-lg font-semibold accent-gradient text-white px-3 py-1 rounded-full">30:00</div>
            </div>

            <!-- Editors -->
            <div id="editors" class="grid gap-4 flex-1">
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
            <div class="flex-1 grid gap-4">
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

    <!-- Scripts -->
    <!-- Monaco Init Script -->
    <script src="assets/editor.js"></script>

    <!-- Timer Script -->
    <script>
        (function() {
            let totalTime = 30 * 60; // 30 minutes in seconds
            const timerEl = document.getElementById('timer');
            const runBtn = document.getElementById('runBtn');
            
            const interval = setInterval(() => {
                if (totalTime <= 0) {
                    clearInterval(interval);
                    timerEl.textContent = '00:00';
                    runBtn.disabled = true;
                    runBtn.textContent = 'Time Up';
                    runBtn.classList.remove('accent-gradient');
                    runBtn.classList.add('bg-gray-600', 'cursor-not-allowed');
                    // Auto-submit or finalize assessment logic here
                    alert("Time's up! Submitting assessment...");
                    return;
                }
                totalTime--;
                const minutes = String(Math.floor(totalTime / 60)).padStart(2, '0');
                const seconds = String(totalTime % 60).padStart(2, '0');
                timerEl.textContent = `${minutes}:${seconds}`;
            }, 1000);
        })();
    </script>
</body>
</html>