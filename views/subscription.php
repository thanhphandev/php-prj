<?php
if (!isset($_SESSION['user'])) {
    header("Location: /auth/sign-in.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$userInfo = getUserProfile($userId);
$userUsage = getUserUsage($userId);
$plans = getSubscriptionPlans();

?>

<main class="container max-w-4xl mx-auto my-10 px-4">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-8 border-b border-gray-200">
            <h1 class="text-2xl font-bold mb-2">Quản lý gói đăng ký</h1>
            <p class="text-gray-600">Quản lý gói đăng ký và theo dõi lượt sử dụng của bạn</p>
        </div>

        <div class="p-8">
            <!-- Current subscription info -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold mb-4">Gói hiện tại của bạn</h2>
                
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                <?= htmlspecialchars($userUsage['plan_name'] ?? 'Free') ?>
                            </span>
                            <?php if ($userUsage['subscription_expiry']): ?>
                                <span class="ml-2 text-sm text-gray-500">
                                    Hết hạn: <?= date('d/m/Y', strtotime($userUsage['subscription_expiry'])) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <button id="upgradeBtn" class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Nâng cấp
                        </button>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium">Số lượt sử dụng</span>
                                <span class="text-sm text-gray-500">
                                    <?= $userUsage['api_requests_count'] ?> / <?= $userUsage['requests_limit'] ?>
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <?php $percentage = ($userUsage['requests_limit'] > 0) ? min(100, ($userUsage['api_requests_count'] / $userUsage['requests_limit']) * 100) : 0; ?>
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?= $percentage ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available plans -->
            <div>
                <h2 class="text-lg font-semibold mb-4">Các gói đăng ký khả dụng</h2>
                
                <div class="grid md:grid-cols-3 gap-6">
                    <?php foreach ($plans as $plan): ?>
                        <?php 
                        $isCurrent = $plan['name'] === ($userUsage['plan_name'] ?? ''); 
                        $features = explode('|', $plan['features']);
                        ?>
                        <div class="border rounded-xl overflow-hidden <?= $isCurrent ? 'ring-2 ring-blue-500' : '' ?>">
                            <div class="p-5 bg-gray-50 border-b">
                                <h3 class="font-bold text-lg"><?= htmlspecialchars($plan['name']) ?></h3>
                                <div class="mt-1 text-2xl font-bold">
                                    <?= number_format($plan['price'], 0, ',', '.') ?>₫
                                    <span class="text-sm font-normal text-gray-500">/tháng</span>
                                </div>
                            </div>
                            
                            <div class="p-5">
                                <ul class="space-y-2 mb-6">
                                    <?php foreach ($features as $feature): ?>
                                        <li class="flex">
                                            <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm"><?= htmlspecialchars($feature) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                
                                <button class="subscribe-btn w-full py-2 rounded-md font-medium transition <?= $isCurrent 
                                    ? 'bg-gray-200 text-gray-800 cursor-default' 
                                    : 'bg-blue-600 text-white hover:bg-blue-700' ?>"
                                    data-plan="<?= htmlspecialchars($plan['name']) ?>"
                                    <?= $isCurrent ? 'disabled' : '' ?>>
                                    <?= $isCurrent ? 'Gói hiện tại' : 'Đăng ký ngay' ?>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Subscription Modal -->
<div id="subscriptionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8 relative animate-fade-in">
        <button id="closeSubscriptionModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-xl">&times;</button>
        <h2 class="text-2xl font-bold mb-2 text-center">Xác nhận đăng ký</h2>
        <p class="text-gray-600 mb-6 text-center">Bạn đang chọn gói <span id="selectedPlanName" class="font-semibold"></span></p>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="flex justify-between mb-2">
                <span>Giá gói</span>
                <span id="planPrice"></span>
            </div>
            <div class="flex justify-between font-semibold">
                <span>Tổng thanh toán</span>
                <span id="totalPrice"></span>
            </div>
        </div>
        
        <button id="confirmSubscribeBtn" class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white py-3 rounded-lg font-semibold text-lg transition">
            Xác nhận thanh toán
        </button>
        
        <!-- Payment Progress Indicator -->
        <div id="paymentProgress" class="hidden mt-6">
            <div class="flex items-center justify-center mb-3">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>
            <p class="text-center text-gray-600">Đang xử lý thanh toán...</p>
        </div>
        
        <div id="subscriptionSuccess" class="hidden mt-6 text-center">
            <div class="text-green-600 text-2xl mb-2"><i class="fas fa-check-circle"></i></div>
            <div class="font-semibold mb-1">Đăng ký thành công!</div>
            <div class="text-gray-500 text-sm">Bạn đã nâng cấp thành công. Hãy tận hưởng các tính năng cao cấp!</div>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: scale(0.95);}
        to { opacity: 1; transform: scale(1);}
    }
    .animate-fade-in { animation: fade-in 0.2s ease; }
</style>

<script>
    $(document).ready(function() {
        let selectedPlan = null;
        const plans = <?= json_encode($plans) ?>;
        
        $('.subscribe-btn').on('click', function() {
            if ($(this).prop('disabled')) return;
            
            const planName = $(this).data('plan');
            selectedPlan = plans.find(p => p.name === planName);
            
            if (selectedPlan) {
                // Populate modal with plan details
                $('#selectedPlanName').text(selectedPlan.name);
                $('#planPrice').text(selectedPlan.price.toLocaleString() + '₫');
                $('#totalPrice').text(selectedPlan.price.toLocaleString() + '₫');
                
                // Show modal
                $('#subscriptionModal').removeClass('hidden');
            }
        });
        
        $('#upgradeBtn').on('click', function() {
            // Simply scroll to plans section
            $('html, body').animate({
                scrollTop: $('.grid.md\\:grid-cols-3').offset().top - 100
            }, 500);
        });
        
        $('#closeSubscriptionModal').on('click', function() {
            $('#subscriptionModal').addClass('hidden');
            $('#paymentProgress').addClass('hidden');
            $('#subscriptionSuccess').addClass('hidden');
        });
        
        $('#confirmSubscribeBtn').on('click', function() {
            if (!selectedPlan) return;
            
            // Disable button and show processing
            $(this).prop('disabled', true).text('Đang xử lý...');
            $('#paymentProgress').removeClass('hidden');
            
            // Call API to update subscription
            $.ajax({
                url: '/api/subscription.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    planName: selectedPlan.name,
                    days: 30
                }),
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        $('#paymentProgress').addClass('hidden');
                        $('#subscriptionSuccess').removeClass('hidden');
                        $('#confirmSubscribeBtn').addClass('hidden');
                        
                        // Reload page after 2 seconds
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        alert('Lỗi: ' + (response.message || 'Không thể đăng ký gói dịch vụ'));
                        $('#paymentProgress').addClass('hidden');
                        $('#confirmSubscribeBtn').prop('disabled', false).text('Xác nhận thanh toán');
                    }
                },
                error: function() {
                    alert('Lỗi kết nối máy chủ. Vui lòng thử lại sau.');
                    $('#paymentProgress').addClass('hidden');
                    $('#confirmSubscribeBtn').prop('disabled', false).text('Xác nhận thanh toán');
                }
            });
        });
    });
</script>
