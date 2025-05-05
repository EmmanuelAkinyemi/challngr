<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Challngr</title>
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

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
    </style>

    <?php
    // Include session configuration at the very top
    require_once __DIR__ . '/config/session.php';

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../challngr/auth/login.html');
        exit();
    }
    ?>
</head>

<body class="h-full">
    <div class="min-h-full">
        <?php include "_partials/header.php"; ?>

        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                    Welcome, <?php
                                // Get email from session or use default
                                $email = $_SESSION['email'] ?? 'user@example.com';

                                // Split email and get the first part
                                $usernameParts = explode('@', $email);
                                $username = $usernameParts[0];

                                // Remove any numbers/special chars if needed (optional)
                                $cleanUsername = preg_replace('/[0-9]+/', '', $username);

                                // Capitalize and display
                                echo htmlspecialchars(ucfirst($cleanUsername));
                                ?>
                </h1>
            </div>
        </header>

        <main>
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <!-- Example cards with accent colors -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Card 1 with accent border -->
                    <div class="rounded-lg border-l-4 border-accent-red bg-white p-6 shadow">
                        <h3 class="text-lg font-medium text-gray-900">Your Progress</h3>
                        <div class="mt-4 h-2 w-full bg-gray-200 rounded-full">
                            <div class="h-full rounded-full accent-gradient" style="width: 65%"></div>
                        </div>
                    </div>

                    <!-- Card 2 with accent button -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                        <button class="mt-4 px-4 py-2 rounded-md text-white font-medium accent-gradient hover:shadow-md transition-all">
                            Start Challenge
                        </button>
                    </div>

                    <!-- Card 3 with accent text -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                        <p class="mt-2 text-accent-orange font-medium">3 new challenges available</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Example of using accent colors in JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // You can use the accent colors in your JS too
            console.log('Dashboard loaded for user: <?php echo $_SESSION["email"] ?? ""; ?>');

            // Example of applying accent color dynamically
            const primaryButtons = document.querySelectorAll('.btn-primary');
            primaryButtons.forEach(btn => {
                btn.style.background = 'linear-gradient(135deg, #ff3e3e, #ff6d3a)';
                btn.addEventListener('mouseenter', () => {
                    btn.style.opacity = '0.9';
                });
                btn.addEventListener('mouseleave', () => {
                    btn.style.opacity = '1';
                });
            });
        });


        //user avatar
        // This script generates a user avatar with initials and a random background color. It uses the user's name from the data attribute of the avatar element to create the initials. The initials are displayed in uppercase, and a random color is selected from a predefined array of colors.
        document.addEventListener('DOMContentLoaded', function() {
            const avatar = document.getElementById('user-avatar');
            const name = avatar.dataset.name;

            // Generate initials
            let initials = 'U';
            if (name.includes('@')) {
                initials = name.split('@')[0].charAt(0).toUpperCase();
            } else {
                const parts = name.split(' ');
                initials = parts.map(part => part.charAt(0).join('').substring(0, 2).toUpperCase()).join('');
            }

            // Generate random color
            const colors = [
                '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A',
                '#98D8C8', '#F06292', '#7986CB', '#9575CD',
                '#64B5F6', '#4DB6AC', '#81C784', '#FFD54F',
                '#FF8A65', '#A1887F', '#90A4AE'
            ];
            const randomColor = colors[Math.floor(Math.random() * colors.length)];

            // Apply to avatar
            avatar.textContent = initials;
            avatar.style.backgroundColor = randomColor;
        });



        //logout button
        // This script handles the logout functionality when the logout button is clicked. It sends a POST request to the server to log out the user and then redirects them to the login page if successful. It also handles errors and displays appropriate messages.
        document.addEventListener('DOMContentLoaded', function() {
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
                            // Redirect to login page
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
    </script>
    <script src="assets/main.js"></script>
</body>

</html>