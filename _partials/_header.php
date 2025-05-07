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

<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <!-- Logo -->
        <div class="flex items-center">
            <a href="/dashboard" class="text-2xl font-bold text-gray-900">Challngr</a>
        </div>
        
        <!-- User Navigation -->
        <div class="flex items-center space-x-4">
            <button class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none">
                <span class="sr-only">Notifications</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>
            
            <div class="relative ml-3">
                <div class="flex items-center space-x-2">
                    <div id="user-avatar" data-name="<?php echo $_SESSION['email'] ?? 'user@example.com'; ?>" 
                         class="w-8 h-8 rounded-full flex items-center justify-center text-white font-medium">
                    </div>
                    <span class="hidden md:inline text-sm font-medium text-gray-700"><?php echo get_username(); ?></span>
                </div>
                
                <!-- Dropdown menu -->
                <div class="hidden absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                    <a href="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                    <a id="logout-btn" href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</a>
                </div>
            </div>
        </div>
    </div>
</header>