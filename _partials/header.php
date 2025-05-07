<?php
// Helper function to extract username from email
function get_username() {
    if (!isset($_SESSION['email'])) return 'User';
    
    $email = $_SESSION['email'];
    $usernameParts = explode('@', $email);
    $username = $usernameParts[0];
    $cleanUsername = preg_replace('/[0-9]+/', '', $username);
    return htmlspecialchars(ucfirst($cleanUsername));
}
?>

<header class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <!-- Logo and Navigation -->
            <div class="flex items-center">
                <div class="shrink-0">
                    <img class="size-8" src="assets/img/favicon.png" alt="Challngr" />
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <?php
                        $currentPage = basename($_SERVER['PHP_SELF']);
                        
                        $navItems = [
                            'dashboard.php' => 'Dashboard',
                            'quiz.php' => 'Quiz',
                            'code-challenge.php' => 'Code Challenges',
                            'quiz-reports.php' => 'Quiz Reports',
                            'challenge-report.php' => 'Challenge Report'
                        ];
                        
                        foreach ($navItems as $page => $name) {
                            $isActive = ($currentPage === $page);
                            $baseClasses = 'rounded-md px-3 py-2 text-sm font-medium';
                            $activeClasses = 'bg-gray-900 text-white';
                            $inactiveClasses = 'text-gray-300 hover:bg-gray-700 hover:text-white';
                            
                            $classes = $isActive 
                                ? "$baseClasses $activeClasses" 
                                : "$baseClasses $inactiveClasses";
                        ?>
                            <a 
                                href="<?= $page ?>" 
                                class="<?= $classes ?>"
                                <?= $isActive ? 'aria-current="page"' : '' ?>>
                                <?= $name ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <!-- User Navigation -->
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">
                    <!-- Notification button -->
                    <button type="button" class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">View notifications</span>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </button>

                    <!-- Profile dropdown -->
                    <div class="relative ml-3">
                        <div>
                            <button id="user-menu-button" type="button" class="relative flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" aria-expanded="false" aria-haspopup="true">
                                <span class="absolute -inset-1.5"></span>
                                <span class="sr-only">Open user menu</span>
                                <div id="user-avatar" class="h-8 w-8 rounded-full flex items-center justify-center text-white font-medium text-sm" data-name="<?php echo htmlspecialchars($_SESSION['email'] ?? 'user@example.com'); ?>"></div>
                                <span class="ml-2 hidden md:inline text-sm font-medium text-white"><?php echo get_username(); ?></span>
                            </button>
                        </div>

                        <!-- Dropdown menu -->
                        <div id="user-menu" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Profile</a>
                            <a href="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Settings</a>
                            <a href="#" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sign out</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile menu button -->
            <div class="-mr-2 flex md:hidden">
                <button id="mobile-menu-button" type="button" class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="absolute -inset-0.5"></span>
                    <span class="sr-only">Open main menu</span>
                    <!-- Menu open: "hidden", Menu closed: "block" -->
                    <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <!-- Menu open: "block", Menu closed: "hidden" -->
                    <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="md:hidden hidden" id="mobile-menu">
        <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
            <?php foreach ($navItems as $page => $name): ?>
                <?php $isActive = ($currentPage === $page); ?>
                <a 
                    href="<?= $page ?>" 
                    class="<?= $isActive ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' ?> block rounded-md px-3 py-2 text-base font-medium"
                    <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <?= $name ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="border-t border-gray-700 pb-3 pt-4">
            <div class="flex items-center px-5">
                <div class="shrink-0">
                    <div id="mobile-user-avatar" class="h-10 w-10 rounded-full flex items-center justify-center text-white font-medium text-lg" data-name="<?php echo htmlspecialchars($_SESSION['email'] ?? 'user@example.com'); ?>"></div>
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium text-white"><?php echo get_username(); ?></div>
                    <div class="text-sm font-medium text-gray-400"><?php echo htmlspecialchars($_SESSION['email'] ?? 'user@example.com'); ?></div>
                </div>
                <button type="button" class="relative ml-auto shrink-0 rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                    <span class="absolute -inset-1.5"></span>
                    <span class="sr-only">View notifications</span>
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                </button>
            </div>
            <div class="mt-3 space-y-1 px-2">
                <a href="/profile" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Your Profile</a>
                <a href="/settings" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Settings</a>
                <a href="#" id="mobile-logout-btn" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Sign out</a>
            </div>
        </div>
    </div>
</header>

<script>
    // Toggle mobile menu
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        const isHidden = menu.classList.contains('hidden');
        
        // Toggle menu visibility
        menu.classList.toggle('hidden');
        
        // Toggle button icons
        const svgs = this.querySelectorAll('svg');
        svgs.forEach(svg => svg.classList.toggle('hidden'));
        svgs.forEach(svg => svg.classList.toggle('block'));
    });

    // Toggle user dropdown
    document.getElementById('user-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('user-menu');
        menu.classList.toggle('hidden');
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#user-menu-button') && !event.target.closest('#user-menu')) {
            document.getElementById('user-menu').classList.add('hidden');
        }
        if (!event.target.closest('#mobile-menu-button') && !event.target.closest('#mobile-menu')) {
            document.getElementById('mobile-menu').classList.add('hidden');
            // Reset mobile menu button icons
            const button = document.getElementById('mobile-menu-button');
            const svgs = button.querySelectorAll('svg');
            svgs[0].classList.remove('hidden');
            svgs[0].classList.add('block');
            svgs[1].classList.remove('block');
            svgs[1].classList.add('hidden');
        }
    });

    // Avatar generation for both desktop and mobile
    document.addEventListener('DOMContentLoaded', function() {
        const generateAvatar = (element) => {
            const name = element.dataset.name;
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
            
            element.textContent = initials;
            element.style.backgroundColor = randomColor;
        };

        // Generate avatars
        const desktopAvatar = document.getElementById('user-avatar');
        const mobileAvatar = document.getElementById('mobile-user-avatar');
        
        if (desktopAvatar) generateAvatar(desktopAvatar);
        if (mobileAvatar) generateAvatar(mobileAvatar);

        // Logout functionality
        const logout = async () => {
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
        };

        document.getElementById('logout-btn')?.addEventListener('click', (e) => {
            e.preventDefault();
            logout();
        });

        document.getElementById('mobile-logout-btn')?.addEventListener('click', (e) => {
            e.preventDefault();
            logout();
        });
    });
</script>