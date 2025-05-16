<header class="bg-white shadow">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <a href="/admin/index.php" class="flex items-center space-x-3">
                    <img class="w-10 h-10 object-contain" src="/assets/images/logo.png" alt="Zesty AI Logo">
                    <div class="hidden sm:flex flex-col">
                        <h1 class="text-xl font-bold text-indigo-600">Zesty AI</h1>
                        <span class="text-xs text-accent">Trang quản trị</span>
                    </div>
                </a>
            </div>

            <div class="flex items-center">
                <!-- Notification dropdown -->
                <div class="ml-3 relative">
                    <button type="button" class="bg-white p-1 rounded-full text-gray-500 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="notification-menu-button" aria-expanded="false" aria-haspopup="true">
                        <span class="sr-only">View notifications</span>
                        <i class="fas fa-bell text-lg"></i>
                    </button>
                </div>

                <!-- Profile dropdown -->
                <div class="ml-3 relative">
                    <div>
                        <button type="button" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                            <span class="sr-only">Open user menu</span>
                            <img class="h-8 w-8 rounded-full" src="<?php echo htmlspecialchars($_SESSION['user']['avatar'] ?? 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y'); ?>" alt="Profile Image">
                            <span class="ml-2 mr-1 text-gray-700"><?php echo htmlspecialchars($_SESSION['user']['username']); ?></span>
                            <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                        </button>
                    </div>

                    <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" id="user-menu">
                        <a href="/">
                            <div class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                <i class="fas fa-home mr-2"></i> Trang chủ
                            </div>
                        </a>
                        <a href="/profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                            <i class="fas fa-user mr-2"></i> Hồ sơ cá nhân
                        </a>
                        <a href="/chat.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                            <i class="fas fa-comments mr-2"></i> Trò chuyện ngay
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="/auth/logout.php" class="block px-4 py-2 text-sm text-red-700 hover:bg-red-50" role="menuitem">
                            <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    $(document).ready(function() {
        // Toggle user menu
        $('#user-menu-button').click(function() {
            $('#user-menu').toggleClass('hidden');
        });

        // Close user menu when clicking elsewhere
        $(document).click(function(e) {
            if (!$(e.target).closest('#user-menu-button, #user-menu').length) {
                $('#user-menu').addClass('hidden');
            }
        });
    });
</script>