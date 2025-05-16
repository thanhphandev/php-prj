<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user']) || !isAdmin($_SESSION['user']['id'])) {
    header('Location: /auth/sign-in.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$stats = getSystemStats();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Zesty</title>
    <link rel="icon" href="/assets/images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<?php include 'layout/header.php'; ?>

<div class="flex">
    <?php include 'layout/sidebar.php'; ?>
    
    <main class="flex-1 p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Bảng điều khiển</h1>
            <p class="text-gray-600">Thông số hiệu suất</p>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 flex items-start space-x-4">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Tổng số người dùng</p>
                    <h3 class="text-2xl font-bold"><?php echo number_format($stats['total_users']); ?></h3>
                    <p class="text-sm text-gray-500">
                        <span class="text-green-500"><i class="fas fa-arrow-up"></i> <?php echo number_format($stats['new_users_week']); ?></span> người dùng mới trong tuần
                    </p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 flex items-start space-x-4">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <i class="fas fa-crown text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Số người đăng ký gói</p>
                    <h3 class="text-2xl font-bold"><?php echo number_format($stats['premium_users']); ?></h3>
                    <p class="text-sm text-gray-500">
                        <span class="text-blue-500"><?php echo round(($stats['premium_users'] / max(1, $stats['total_users'])) * 100); ?>%</span> của tổng số người dùng
                    </p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 flex items-start space-x-4">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                    <i class="fas fa-comments text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Tổng đoạn chat</p>
                    <h3 class="text-2xl font-bold"><?php echo number_format($stats['total_chats']); ?></h3>
                    <p class="text-sm text-gray-500">
                        <span class="text-purple-500"><?php echo number_format($stats['total_messages']); ?></span> tin nhắn
                    </p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 flex items-start space-x-4">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                    <i class="fas fa-bolt text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Số lượng tin nhắn hôm nay</p>
                    <h3 class="text-2xl font-bold"><?php echo number_format($stats['messages_24h']); ?></h3>
                    <p class="text-sm text-gray-500">
                        Tin nhắn hôm nay
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Subscription Distribution</h3>
                <div class="h-64">
                    <canvas id="planDistribution"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">User Status</h3>
                <div class="h-64">
                    <canvas id="userStatus"></canvas>
                </div>
            </div>
        </div>
        
    </main>
</div>

<script>
$(document).ready(function() {
    // Plan distribution chart
    const planCtx = document.getElementById('planDistribution').getContext('2d');
    const planLabels = [];
    const planData = [];
    const planColors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'];
    
    <?php foreach ($stats['plans'] as $index => $plan): ?>
    planLabels.push('<?php echo $plan['subscription_tier']; ?>');
    planData.push(<?php echo $plan['count']; ?>);
    <?php endforeach; ?>
    
    new Chart(planCtx, {
        type: 'doughnut',
        data: {
            labels: planLabels,
            datasets: [{
                data: planData,
                backgroundColor: planColors.slice(0, planLabels.length),
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            },
            cutout: '65%'
        }
    });
    
    // User status chart
    const statusCtx = document.getElementById('userStatus').getContext('2d');
    
    const activeUsers = <?php echo $stats['active_users']; ?>;
    const totalUsers = <?php echo $stats['total_users']; ?>;
    const inactiveUsers = totalUsers - activeUsers;
    
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['Active', 'Inactive/Banned'],
            datasets: [{
                data: [activeUsers, inactiveUsers],
                backgroundColor: ['#10B981', '#EF4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
});
</script>

</body>
</html>
