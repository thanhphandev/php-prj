<?php
session_start();
require_once 'config/database.php';
$title ??= 'Trang chủ Zesty AI';
$user = $_SESSION['user'] ?? null;
$isLoggedIn = isset($user) && !empty($user['username']);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="icon" href="/assets/images/logo.png" type="image/x-icon">
    <title><?= $title ?></title>

</head>

<body class="font-sans">
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <!-- Logo and Brand -->
                <a href="/" class="flex items-center space-x-3">
                    <img class="w-10 h-10 object-contain" src="/assets/images/logo.png" alt="Zesty AI Logo">
                    <div class="hidden sm:flex flex-col">
                        <h1 class="text-xl font-bold text-indigo-600">Zesty AI</h1>
                        <span class="text-xs text-accent">Trợ lý ảo AI 4.0</span>
                    </div>
                </a>

                <!-- User Menu or Auth Buttons -->
                <div class="flex items-center space-x-4">
                    <?php if ($isLoggedIn): ?>
                        <!-- User Profile Dropdown -->
                        <div class="relative" id="avatar">
                            <button type="button" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                <img class="h-8 w-8 rounded-full" src="<?php echo htmlspecialchars($_SESSION['user']['avatar'] ?? 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y'); ?>" alt="Profile Image">
                                <span class="ml-2 mr-1 text-gray-700"><?php echo htmlspecialchars($_SESSION['user']['username']); ?></span>
                                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                            </button>

                            <div id="dropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-100 hidden dropdown-animate z-50">
                                <div class="p-4 border-b border-gray-100">
                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($user['fullname']) ?></p>
                                    <p class="text-sm text-gray-500">
                                        <?= htmlspecialchars($user['username']) ?>
                                        <span class="text-xs text-gray-400">|</span>
                                        <span class="text-xs text-gray-400"><?= htmlspecialchars($user['role'] === 'user' ? 'Người dùng' : 'Admin') ?></span>
                                    </p>
                                </div>

                                <div class="py-2">
                                    <a href="/profile.php" class="flex items-center px-4 py-3 hover:bg-gray-50 transition duration-300">
                                        <i class="fa-solid fa-user text-gray-500 w-5"></i>
                                        <span class="ml-3 text-gray-700">Thông tin hồ sơ</span>
                                    </a>
                                    <a href="/chat.php" class="flex items-center px-4 py-3 hover:bg-gray-50 transition duration-300">
                                        <i class="fa-solid fa-comments text-gray-500 w-5"></i>
                                        <span class="ml-3 text-gray-700">Chat ngay</span>
                                    </a>
                                    <a href="/subscription-plans.php" class="flex items-center px-4 py-3 hover:bg-gray-50 transition duration-300">
                                        <i class="fa-solid fa-crown text-gray-500 w-5"></i>
                                        <span class="ml-3 text-gray-700">Gói đăng ký</span>
                                    </a>
                                    <?php if (isset($user['role']) && strtolower($user['role']) === 'admin'): ?>
                                        <a href="/admin/index.php">
                                            <div class="px-4 py-2 text-sm text-gray-700" role="menuitem">
                                                <i class="fas fa-tachometer-alt mr-2"></i> Bảng điều khiển
                                            </div>
                                        </a>
                                    <?php endif; ?>

                                </div>

                                <div class="py-2 border-t border-gray-100">
                                    <a href="/auth/logout.php" class="flex items-center px-4 py-3 text-red-600 hover:bg-red-50 transition duration-300">
                                        <i class="fa-solid fa-right-from-bracket w-5"></i>
                                        <span class="ml-3">Đăng xuất</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/auth/sign-in.php" class="btn-secondary items-center md:flex hidden">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span class="ml-2">Đăng nhập</span>
                        </a>
                        <a href="/auth/sign-up.php" class="btn-primary items-center">
                            <span class="mr-2">Dùng thử miễn phí</span>
                            <i class="fa-solid fa-rocket"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <script src="/assets/js/header.js"></script>
    </header>