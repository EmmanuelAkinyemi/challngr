<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code challenge - challngr</title>
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Monaco Editor Loader -->
    <script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.34.1/min/vs/loader.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <style>
        body{
            font-family: "Inter", sans-serif;
        }
    </style>
</head>

<body class="h-screen flex bg-black text-white">
    <!-- Left: Challenge Description -->
    <div class="w-1/2 p-6 overflow-auto border-r border-blue-800">
        <h2 class="text-2xl font-bold mb-4">Code Challenge</h2>
        <p class="mb-2">Write a function <code>factorial($n)</code> in PHP that returns the factorial of <code>$n</code>. Use the editor on the right and click <span class="font-semibold">Run Code</span> to test.</p>
        <ul class="list-disc list-inside">
            <li>Input: Integer <code>n</code></li>
            <li>Output: Factorial of <code>n</code></li>
            <li>Example: <code>factorial(5) === 120</code></li>
        </ul>
    </div>

    <!-- Right: Editors, Timer, and Output -->
    <div class="w-1/2 flex flex-col p-6 space-y-4 overflow-auto">
        <!-- Timer and Heading -->
        <div class="sticky top-0 w-full flex justify-between items-center shadow-lg p-4 bg-blue-800/50 backdrop-blur-lg rounded-lg mb-4 z-10">
            <h2 class="text-2xl font-bold">Your Editor</h2>
            <div id="timer" class="text-lg font-semibold">30:00</div>
        </div>

        <!-- Editors -->
        <div id="editors" class="grid gap-4 flex-1">
            <div class="rounded-lg border border-blue-800 p-2">
                <h3 class="mb-2">HTML</h3>
                <div id="htmlEditor" class="h-40 border border-blue-800 rounded-lg"></div>
            </div>
            <div class="rounded-lg border border-blue-800 p-2">
                <h3 class="mb-2">CSS</h3>
                <div id="cssEditor" class="h-40 border border-blue-800 rounded-lg"></div>
            </div>
            <div class="rounded-lg border border-blue-800 p-2">
                <h3 class="mb-2">JavaScript</h3>
                <div id="jsEditor" class="h-40 border border-blue-800 rounded-lg"></div>
            </div>
            <div class="rounded-lg border border-blue-800 p-2">
                <h3 class="mb-2">PHP</h3>
                <div id="phpEditor" class="h-40 border border-blue-800 rounded-lg"></div>
            </div>
        </div>

        <!-- Run Button -->
        <button id="runBtn" class="self-end px-6 py-2 bg-blue-800 rounded-lg hover:bg-blue-700 transition">Run Code</button>

        <!-- Outputs -->
        <div class="flex-1 grid  gap-4">
            <div class="rounded-lg border border-blue-800 p-2">
                <h3 class="mb-2">Rendered Output</h3>
                <iframe id="output" class="w-full h-60 border border-blue-800 rounded-lg bg-white"></iframe>
            </div>
            <div class="rounded-lg border border-blue-800 p-2">
                <h3 class="mb-2">PHP Output</h3>
                <pre id="phpOutput" class="w-full h-60 border border-blue-800 rounded-lg bg-white text-black p-2 overflow-auto"></pre>
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