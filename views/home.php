<main class="bg-white text-gray-800">
  <!-- Hero -->
  <section class="relative overflow-hidden bg-gradient-to-b from-white to-blue-50">
    <div class="max-w-7xl mx-auto px-6 py-20 md:flex md:items-center md:justify-between">
      <div class="md:w-1/2 space-y-6">
        <h1 class="text-4xl md:text-5xl font-bold leading-tight">
          Khám phá sức mạnh AI cùng <span class="text-primary">Zesty AI</span>
        </h1>
        <p class="text-lg text-gray-600">
          Nền tảng trí tuệ nhân tạo tiên tiến, giúp bạn giải quyết mọi vấn đề một cách thông minh và hiệu quả.
        </p>
        <div class="flex flex-wrap gap-4">
          <?php if ($isLoggedIn): ?>
            <a href="/chat.php" class="btn-primary">
              Truy cập ngay <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
          <?php else: ?>
            <a href="/auth/sign-up.php" class="btn-primary">
              Dùng thử miễn phí <i class="fa-solid fa-rocket ml-2"></i>
            </a>
            <a href="/auth/sign-in.php" class="btn-secondary">
              <i class="fa-solid fa-right-to-bracket mr-2"></i> Đăng nhập
            </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="md:w-1/2 mt-12 md:mt-0 relative">
        <img src="/assets/images/ai-illustration.png" alt="Zesty AI Illustration" class="w-full">
        <div class="absolute w-64 h-64 bg-indigo-300 opacity-20 rounded-full -top-10 -left-10 blur-2xl"></div>
      </div>
    </div>
  </section>

  <!-- Features -->
  <section class="py-24 bg-gray-50">
    <div class="max-w-6xl mx-auto px-6">
      <div class="text-center mb-14">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Tính năng nổi bật</h2>
        <p class="text-lg text-gray-600">Zesty AI cách mạng hóa công việc và cuộc sống của bạn</p>
      </div>
      <div class="grid gap-8 md:grid-cols-3">
        <!-- Feature Card -->
        <?php
          $features = [
            [
              'title' => 'Trợ lý thông minh',
              'desc' => 'Hiểu ngôn ngữ tự nhiên, trả lời câu hỏi và thực hiện các tác vụ chính xác.',
              'iconColor' => 'bg-indigo-100 text-indigo-600',
              'svg' => '✨',
            ],
            [
              'title' => 'Xử lý dữ liệu nhanh',
              'desc' => 'Phân tích dữ liệu thời gian thực, hỗ trợ ra quyết định nhanh.',
              'iconColor' => 'bg-purple-100 text-purple-600',
              'svg' => '🚀',
            ],
            [
              'title' => 'Tích hợp linh hoạt',
              'desc' => 'Dễ dàng tích hợp với hệ thống hiện có, tạo hệ sinh thái thông minh.',
              'iconColor' => 'bg-green-100 text-green-600',
              'svg' => '🤖',
            ]
          ];
          foreach ($features as $f):
        ?>
        <div class="bg-white p-8 rounded-xl shadow hover:shadow-lg transition hover:scale-105">
          <div class="w-14 h-14 rounded-lg flex items-center justify-center mb-5 <?php echo $f['iconColor']; ?>">
            <span class="text-3xl"><?php echo $f['svg']; ?></span>
          </div>
          <h3 class="text-lg font-semibold mb-2"><?php echo $f['title']; ?></h3>
          <p class="text-gray-600 text-sm"><?php echo $f['desc']; ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Subscription -->
  <section class="py-20">
    <div class="text-center mb-12">
      <h2 class="text-3xl font-bold">Gói đăng ký</h2>
    </div>
    <div class="max-w-6xl mx-auto px-6 grid gap-8 md:grid-cols-3">
      <?php
        $stmt = $pdo->query("SELECT * FROM subscription_plans ORDER BY price");
        while ($plan = $stmt->fetch()):
          $features = explode('|', $plan['features']);
          $featured = $plan['name'] === 'Standard';
      ?>
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden <?php echo $featured ? 'ring-2 ring-indigo-500 scale-105' : ''; ?>">
        <div class="<?php echo $featured ? 'bg-indigo-600 text-white' : 'bg-gray-100'; ?> p-6 relative">
          <?php if ($featured): ?>
            <span class="absolute top-4 right-4 bg-white text-indigo-600 text-xs px-2 py-1 rounded-full">Phổ biến</span>
          <?php endif; ?>
          <h3 class="text-xl font-bold"><?php echo $plan['name']; ?></h3>
          <p class="mt-2 text-3xl font-bold"><?php echo number_format($plan['price'], 0, ',', '.') . '₫'; ?> <span class="text-sm font-medium">/tháng</span></p>
        </div>
        <div class="p-6 space-y-4">
          <ul class="space-y-2 text-sm">
            <?php foreach ($features as $feature): ?>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mt-1 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span><?php echo $feature; ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
          <a href="index.php?page=subscription" class="block w-full text-center py-2 rounded-md font-medium <?php echo $featured ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'; ?>">
            <?php echo $plan['name'] === 'Free' ? 'Gói bắt đầu' : 'Đăng ký ngay'; ?>
          </a>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </section>
</main>
