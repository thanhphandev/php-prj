<aside class="w-64 bg-white shadow-md h-screen sticky top-0 overflow-y-auto hidden md:block">
    <div class="py-4 px-3">
        <nav class="mt-5 space-y-1">
            <a href="/admin/index.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
                <i class="fas fa-tachometer-alt w-6 mr-3 text-<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'blue' : 'gray'; ?>-500"></i>
                Dashboard
            </a>

            <a href="/admin/users.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
                <i class="fas fa-users w-6 mr-3 text-<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'blue' : 'gray'; ?>-500"></i>
                Quản lý người dùng
            </a>

            <a href="/admin/subscription-plans.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'subscription-plans.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
                <i class="fas fa-crown w-6 mr-3 text-<?php echo basename($_SERVER['PHP_SELF']) == 'subscription-plans.php' ? 'blue' : 'gray'; ?>-500"></i>
                Gói đăng ký
            </a>

            <a href="/admin/chat-sessions.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'chat-sessions.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
                <i class="fas fa-comments w-6 mr-3 text-<?php echo basename($_SERVER['PHP_SELF']) == 'chat-sessions.php' ? 'blue' : 'gray'; ?>-500"></i>
                Lịch sử chat
            </a>

        </nav>

        <div class="mt-10 pt-6 border-t border-gray-200">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tài nguyên
            </div>
            <nav class="mt-3 space-y-1">
                <a href="/profile.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-user-circle w-6 mr-3 text-gray-500"></i>
                    Hồ sơ cá nhân
                </a>
                <a href="https://ai.google.dev/gemini-api/docs?hl=en" target="_blank" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-book w-6 mr-3 text-gray-500"></i>
                    Tài liệu API
                </a>
                <a href="/auth/logout.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-red-700 hover:bg-red-50">
                    <i class="fas fa-sign-out-alt w-6 mr-3 text-red-500"></i>
                    Đăng xuất
                </a>
            </nav>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center px-3">
                <div class="flex-shrink-0">
                    <img class="h-10 w-10 rounded-full" src="<?php echo htmlspecialchars($_SESSION['user']['avatar'] ?? 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y'); ?>" alt="User profile">
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['user']['fullname'] ?? $_SESSION['user']['username']); ?></div>
                    <div class="text-xs text-gray-500">Quản trị viên</div>
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile sidebar toggle -->
<div class="md:hidden fixed bottom-4 right-4 z-20">
    <button id="mobileMenuToggle" class="p-3 bg-blue-600 text-white rounded-full shadow-lg">
        <i class="fas fa-bars" id="menuIcon"></i>
    </button>
</div>

<!-- Mobile sidebar -->
<div id="mobileSidebar" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden">
    <div class="w-64 h-full bg-white overflow-y-auto">
        <div class="p-4 flex justify-between items-center border-b border-gray-200">
            <div class="flex items-center">
                <img class="h-8 w-8" src="/assets/images/logo.png" alt="Logo">
                <span class="ml-2 text-lg font-bold text-gray-900">Zesty Admin</span>
            </div>
            <button id="closeMobileMenu" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="py-4 px-3">
            <nav class="space-y-1">
                <a href="/admin/index.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <i class="fas fa-tachometer-alt w-6 mr-3 text-<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'blue' : 'gray'; ?>-500"></i>
                    Dashboard
                </a>

                <!-- Repeat the other links from the desktop sidebar -->
                <a href="/admin/users.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <i class="fas fa-users w-6 mr-3 text-<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'blue' : 'gray'; ?>-500"></i>
                    Quản lý người dùng
                </a>

                <a href="/admin/subscription-plans.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'subscription-plans.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <i class="fas fa-crown w-6 mr-3 text-<?php echo basename($_SERVER['PHP_SELF']) == 'subscription-plans.php' ? 'blue' : 'gray'; ?>-500"></i>
                    Gói đăng ký
                </a>

                <a href="/admin/chat-sessions.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'chat-sessions.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <i class="fas fa-comments w-6 mr-3 text-<?php echo basename($_SERVER['PHP_SELF']) == 'chat-sessions.php' ? 'blue' : 'gray'; ?>-500"></i>
                    Lịch sử chat
                </a>
            </nav>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Toggle mobile sidebar
        $('#mobileMenuToggle').click(function() {
            $('#mobileSidebar').removeClass('hidden');
            $('#menuIcon').removeClass('fa-bars').addClass('fa-times');
        });

        $('#closeMobileMenu').click(function() {
            $('#mobileSidebar').addClass('hidden');
            $('#menuIcon').removeClass('fa-times').addClass('fa-bars');
        });

        // Close mobile sidebar when clicking outside
        $('#mobileSidebar').click(function(e) {
            if (e.target === this) {
                $(this).addClass('hidden');
                $('#menuIcon').removeClass('fa-times').addClass('fa-bars');
            }
        });
    });
</script>