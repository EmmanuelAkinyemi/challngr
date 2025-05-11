<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard - Challngr'; ?></title>
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

    <!-- Tailwind CSS -->
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

    <!-- Load Monaco Editor -->
    <script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.34.1/min/vs/loader.js"></script>

    <!-- Load your editor configuration -->
    <script src="assets/editor.js"></script>
    <!-- Fonts -->
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
</head>

<body class="h-full">
    <div class="min-h-full">
        <?php include "_partials/header.php"; ?>

        <?php if (!isset($hidePageHeader)): ?>
            <header class="bg-white shadow-sm">
                <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                        <?php echo $pageHeader ?? 'Welcome, ' . get_username(); ?>
                    </h1>
                </div>
            </header>
        <?php endif; ?>

        <main>
            <div class="mx-auto max-w-7xl px-2 py-6 sm:px-2 lg:px-4 space-y-8">
                <?php echo $content; ?>
            </div>
        </main>
    </div>

    <?php include "_partials/footer.php"; ?>

    <!-- <script src="./assets/editor.js"></script> -->
    <script>
        // Avatar generation
        document.addEventListener('DOMContentLoaded', function() {
            const avatar = document.getElementById('user-avatar');
            if (avatar) {
                const name = avatar.dataset.name;
                let initials = 'U';

                if (name.includes('@')) {
                    initials = name.split('@')[0].charAt(0).toUpperCase();
                } else {
                    const parts = name.split(' ');
                    initials = parts.map(part => part.charAt(0)).join('').substring(0, 2).toUpperCase();
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
            }

            // Logout functionality
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



        // Example of a function to check if a username is available
    </script>


</body>

</html>