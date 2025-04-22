<?php
session_start();
require_once 'config/database.php';
$title ??= 'Trang chủ Zesty AI';
$isLoggedIn = isset($_SESSION['user_id']);
$userImage = isset($_SESSION['avatar']) ? $_SESSION['avatar'] : '/assets/images/default-avatar.png';
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
                <div class="flex items-center space-x-3">
                    <img class="w-10 h-10 object-contain" src="/assets/images/logo.png" alt="Zesty AI Logo">
                    <div class="hidden sm:flex flex-col">
                        <h1 class="text-xl font-bold text-indigo-600">Zesty AI</h1>
                        <span class="text-xs text-accent">Trí tuệ nhân tạo tương tác</span>
                    </div>
                </div>

                <!-- Main Navigation - Hidden on mobile -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="/" class="nav-link active">Trang chủ</a>
                    <a href="/features.php" class="nav-link">Tính năng</a>
                    <a href="/pricing.php" class="nav-link">Bảng giá</a>
                    <a href="/contact.php" class="nav-link">Liên hệ</a>
                </nav>

                <!-- User Menu or Auth Buttons -->
                <div class="flex items-center space-x-4">
                    <?php if ($isLoggedIn): ?>
                        <!-- Notification Icon -->
                        <div class="relative">
                            <button class="text-gray-600 hover:text-indigo-600 transition duration-300">
                                <i class="fa-regular fa-bell text-xl"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">3</span>
                            </button>
                        </div>

                        <!-- User Profile Dropdown -->
                        <div class="relative">
                            <div id="avatar" class="flex items-center space-x-2 cursor-pointer">
                                <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-indigo-100 hover:border-indigo-300 transition duration-300">
                                    <img class="w-full h-full object-cover" src="<?= $userImage ?>" alt="Ảnh hồ sơ">
                                </div>
                            </div>

                            <div id="dropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-100 hidden dropdown-animate z-50">
                                <div class="p-4 border-b border-gray-100">
                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($_SESSION['fullname']) ?></p>
                                    <p class="text-sm text-gray-500">
                                        <?php if (isset($_SESSION['email'])): ?>
                                            <?= htmlspecialchars($_SESSION['email']) ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars($_SESSION['username']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <div class="py-2">
                                    <a href="/profile.php" class="flex items-center px-4 py-3 hover:bg-gray-50 transition duration-300">
                                        <i class="fa-solid fa-user text-gray-500 w-5"></i>
                                        <span class="ml-3 text-gray-700">Hồ sơ cá nhân</span>
                                    </a>
                                    <a href="/settings.php" class="flex items-center px-4 py-3 hover:bg-gray-50 transition duration-300">
                                        <i class="fa-solid fa-gear text-gray-500 w-5"></i>
                                        <span class="ml-3 text-gray-700">Cài đặt</span>
                                    </a>
                                    <a href="/usage.php" class="flex items-center px-4 py-3 hover:bg-gray-50 transition duration-300">
                                        <i class="fa-solid fa-chart-simple text-gray-500 w-5"></i>
                                        <span class="ml-3 text-gray-700">Thống kê sử dụng</span>
                                    </a>
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

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button" class="md:hidden text-gray-500 focus:outline-none">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu - Hidden by default -->
        <div id="mobile-menu" class="md:hidden hidden bg-white border-t border-gray-100">
            <div class="container mx-auto px-4 py-3">
                <nav class="flex flex-col space-y-3">
                    <a href="/" class="py-2 px-4 rounded-lg bg-indigo-50 text-indigo-600 font-medium">Trang chủ</a>
                    <a href="/features.php" class="py-2 px-4 rounded-lg text-gray-700 hover:bg-gray-50">Tính năng</a>
                    <a href="/pricing.php" class="py-2 px-4 rounded-lg text-gray-700 hover:bg-gray-50">Bảng giá</a>
                    <a href="/contact.php" class="py-2 px-4 rounded-lg text-gray-700 hover:bg-gray-50">Liên hệ</a>

                    <?php if (!$isLoggedIn): ?>
                        <div class="pt-2 border-t border-gray-100 flex space-x-2">
                            <a href="/auth/sign-in.php" class="flex btn-secondary flex-1 items-center justify-center gap-2">
                                <span>Đăng nhập</span>
                                <i class="fa-solid fa-right-to-bracket"></i>
                            </a>
                            <a href="/auth/sign-up.php" class="flex btn-primary flex-1 items-center justify-center gap-2">
                                <span>Đăng ký</span>
                                <i class="fa-solid fa-user-plus"></i>
                            </a>

                        </div>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main content would go here -->
    <main class="container mx-auto px-4 py-8">
        <script src="/assets/js/header.js"></script>