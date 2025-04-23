<main class="mx-auto maax-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="container mx-auto px-4 py-20">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-10 md:mb-0">
                <h1 class="text-4xl text-primary md:text-5xl font-bold leading-tight mb-6">Khám phá sức mạnh AI cùng Zesty AI</h1>
                <p class="text-xl mb-8">Nền tảng trí tuệ nhân tạo tiên tiến, giúp bạn giải quyết mọi vấn đề một cách thông minh và hiệu quả.</p>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                    <?php if ($isLoggedIn): ?>
                        <a href="/chat.php">
                            <button class="btn-primary items-center">
                                <span class="mr-2">Truy cập ngay</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </a>
                    <?php else: ?>
                        <a href="/auth/sign-up.php">
                            <button class="btn-primary items-center">
                                <span class="mr-2">Dùng thử miễn phí</span>
                                <i class="fa-solid fa-rocket"></i>
                            </button>
                        </a>
                        <a href="/auth/sign-in.php">
                            <button class="btn-secondary items-center">
                                <i class="fa-solid fa-right-to-bracket"></i>
                                <span class="ml-2">Đăng nhập</span>
                            </button>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="md:w-1/2">
                <div class="relative">
                    <div class="bg-blue-500 opacity-20 w-64 h-64 rounded-full absolute -top-2 -right-10"></div>
                    <div class="bg-purple-500 opacity-20 w-64 h-64 rounded-full absolute -bottom-14 -left-10"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800">Tính năng nổi bật</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Khám phá cách Zesty AI có thể cách mạng hóa công việc và cuộc sống của bạn với công nghệ AI tiên tiến</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                    <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-indigo-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Trợ lý thông minh</h3>
                    <p class="text-gray-600">Trợ lý ảo thông minh có thể hiểu ngôn ngữ tự nhiên, trả lời câu hỏi và thực hiện các tác vụ một cách chính xác.</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                    <div class="w-16 h-16 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Xử lý dữ liệu nhanh chóng</h3>
                    <p class="text-gray-600">Phân tích và xử lý khối lượng dữ liệu lớn trong thời gian thực, giúp bạn đưa ra quyết định nhanh chóng và chính xác.</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                    <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Tích hợp linh hoạt</h3>
                    <p class="text-gray-600">Dễ dàng tích hợp với các ứng dụng và hệ thống hiện có của bạn, tạo ra một hệ sinh thái thông minh và liền mạch.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Section -->
    <div class="mt-2 py-10 px-4 rounded-lg">
        <h2 class="text-3xl text-center font-bold text-primary mb-6">Gói đăng ký</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <?php
            $stmt = $pdo->query("SELECT * FROM subscription_plans ORDER BY price");
            while ($plan = $stmt->fetch()) {
                $features = explode('|', $plan['features']);
                $featured = $plan['name'] === 'Basic';
            ?>
                <div class="bg-white rounded-lg overflow-hidden shadow-lg <?php echo $featured ? 'ring-2 ring-indigo-600 transform scale-105' : ''; ?>">
                    <div class="p-6 <?php echo $featured ? 'bg-indigo-600 text-white' : 'bg-gray-100'; ?>">
                        <h3 class="text-2xl font-bold"><?php echo $plan['name']; ?></h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-4xl font-bold">
                                <?php echo number_format($plan['price'], 0, ',', '.') . '₫'; ?>
                            </span>
                            <span class="ml-1 text-xl text-gray-<?php echo $featured ? '200' : '500'; ?>">/tháng</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-4">
                            <?php foreach ($features as $feature): ?>
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-3 text-gray-700"><?php echo $feature; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="mt-6">
                            <a href="index.php?page=subscription" class="block w-full py-3 px-4 rounded-lg text-center font-medium <?php echo $featured ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'; ?> transition-colors">
                                <?php echo $plan['name'] === 'Free' ? 'Gói bắt đầu' : 'Đăng ký ngay'; ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</main>