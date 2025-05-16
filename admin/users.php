<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user']) || !isAdmin($_SESSION['user']['id'])) {
    header('Location: /auth/sign-in.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['user_id'])) {
        $targetUserId = (int)$_POST['user_id'];
        $action = $_POST['action'];
        
        // Don't allow admins to modify themselves
        if ($targetUserId === $userId) {
            $message = "Bạn không thể thay đổi trạng thái của chính mình.";
            $messageType = "error";
        } else {
            switch ($action) {
                case 'activate':
                    if (updateUserStatus($targetUserId, 'active', $userId)) {
                        $message = "Người dùng đã được kích hoạt thành công.";
                        $messageType = "success";
                    }
                    break;
                    
                case 'deactivate':
                    if (updateUserStatus($targetUserId, 'inactive', $userId)) {
                        $message = "Người dùng đã bị vô hiệu hóa thành công.";
                        $messageType = "success";
                    }
                    break;
                    
                case 'ban':
                    if (updateUserStatus($targetUserId, 'banned', $userId)) {
                        $message = "Người dùng đã bị cấm thành công.";
                        $messageType = "success";
                    }
                    break;
                    
                case 'make_admin':
                    if (updateUserRole($targetUserId, 'admin', $userId)) {
                        $message = "Người dùng đã được thăng cấp thành quản trị viên.";
                        $messageType = "success";
                    }
                    break;
                    
                case 'remove_admin':
                    if (updateUserRole($targetUserId, 'user', $userId)) {
                        $message = "Quyền quản trị viên đã bị thu hồi thành công.";
                        $messageType = "success";
                    }
                    break;
                    
                case 'reset_api_count':
                    $stmt = $pdo->prepare("UPDATE users SET api_requests_count = 0 WHERE id = :userId");
                    $stmt->bindParam(":userId", $targetUserId);
                    if ($stmt->execute()) {
                        $message = "Đã đặt lại số lượng yêu cầu API thành công.";
                        $messageType = "success";
                    }
                    break;
                    
                case 'extend_subscription':
                    $days = isset($_POST['days']) ? (int)$_POST['days'] : 30;
                    $planName = isset($_POST['plan']) ? $_POST['plan'] : null;
                    
                    if ($planName) {
                        if (subscribeUser($targetUserId, $planName, $days)) {
                            $message = "Đã gia hạn gói đăng ký thành công.";
                            $messageType = "success";
                        }
                    }
                    break;
            }
        }
    }
}

// Get search/filter parameters
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$role = $_GET['role'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;

// Get users
$users = getAllUsers($search, $status, $role, $page, $limit, $userId);

// Get subscription plans for dropdown
$plans = getSubscriptionPlans();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng | Admin Zesty</title>
    <link rel="icon" href="/assets/images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<?php include 'layout/header.php'; ?>

<div class="flex">
    <?php include 'layout/sidebar.php'; ?>
    
    <main class="flex-1 p-8">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Quản lý người dùng</h1>
                <p class="text-gray-600">Quản lý tất cả người dùng trong hệ thống</p>
            </div>
            <div>
                <button type="button" id="resetAllApiRequests" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-sync-alt mr-2"></i> Đặt lại tất cả số lượng API
                </button>
            </div>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border-l-4 border-green-500' : 'bg-red-100 text-red-700 border-l-4 border-red-500'; ?> flex items-center">
                <div class="mr-3">
                    <?php if ($messageType === 'success'): ?>
                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    <?php else: ?>
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    <?php endif; ?>
                </div>
                <div><?php echo $message; ?></div>
            </div>
        <?php endif; ?>
        
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Tên đăng nhập, email, họ tên...">
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                        <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Không hoạt động</option>
                        <option value="banned" <?php echo $status === 'banned' ? 'selected' : ''; ?>>Bị cấm</option>
                    </select>
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                    <select id="role" name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả vai trò</option>
                        <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>Người dùng</option>
                        <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Quản trị viên</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-search mr-2"></i> Lọc
                    </button>
                    <a href="users.php" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <i class="fas fa-times mr-2"></i> Xóa bộ lọc
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người dùng</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò/Trạng thái</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gói đăng ký</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">API đã dùng</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày đăng ký</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users['data'] as $user): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="<?php echo htmlspecialchars($user['avatar'] ?? 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y'); ?>" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['fullname'] ?: 'N/A'); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user['username']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php
                                    $roleClass = $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800';
                                    echo $roleClass;
                                    ?>">
                                    <?php echo $user['role'] === 'admin' ? 'Quản trị viên' : 'Người dùng'; ?>
                                </span>
                                <br>
                                <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php
                                    $statusClass = 'bg-gray-100 text-gray-800';
                                    if ($user['status'] === 'active') $statusClass = 'bg-green-100 text-green-800';
                                    elseif ($user['status'] === 'inactive') $statusClass = 'bg-yellow-100 text-yellow-800';
                                    elseif ($user['status'] === 'banned') $statusClass = 'bg-red-100 text-red-800';
                                    echo $statusClass;
                                    ?>">
                                    <?php 
                                    $statusText = 'Không xác định';
                                    if ($user['status'] === 'active') $statusText = 'Hoạt động';
                                    elseif ($user['status'] === 'inactive') $statusText = 'Không hoạt động';
                                    elseif ($user['status'] === 'banned') $statusText = 'Bị cấm';
                                    echo $statusText;
                                    ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['subscription_tier']); ?></div>
                                <?php if ($user['subscription_expiry']): ?>
                                <div class="text-xs text-gray-500">
                                    <?php echo date('d/m/Y', strtotime($user['subscription_expiry'])); ?>
                                    <?php 
                                    $daysLeft = ceil((strtotime($user['subscription_expiry']) - time()) / (60 * 60 * 24));
                                    $expireClass = $daysLeft <= 3 ? 'text-red-500' : ($daysLeft <= 7 ? 'text-yellow-500' : 'text-green-500');
                                    ?>
                                    <span class="<?php echo $expireClass; ?>">
                                        (<?php echo $daysLeft > 0 ? "còn $daysLeft ngày" : "đã hết hạn"; ?>)
                                    </span>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo number_format($user['api_requests_count']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="text-blue-600 hover:text-blue-900 focus:outline-none">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10" style="display: none;">
                                        <div class="py-1" role="menu" aria-orientation="vertical">
                                            <?php if ($user['status'] !== 'active'): ?>
                                            <form method="POST" class="block">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="activate">
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                                                    <i class="fas fa-check-circle mr-2 text-green-500"></i> Kích hoạt tài khoản
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($user['status'] !== 'inactive'): ?>
                                            <form method="POST" class="block">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="deactivate">
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                                                    <i class="fas fa-pause-circle mr-2 text-yellow-500"></i> Vô hiệu hóa tài khoản
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($user['status'] !== 'banned'): ?>
                                            <form method="POST" class="block">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="ban">
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                                                    <i class="fas fa-ban mr-2 text-red-500"></i> Cấm tài khoản
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                            
                                            <div class="border-t border-gray-100 my-1"></div>
                                            
                                            <?php if ($user['role'] !== 'admin'): ?>
                                            <form method="POST" class="block">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="make_admin">
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                                                    <i class="fas fa-user-shield mr-2 text-purple-500"></i> Thăng cấp quản trị viên
                                                </button>
                                            </form>
                                            <?php else: ?>
                                            <form method="POST" class="block">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="remove_admin">
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                                                    <i class="fas fa-user mr-2 text-blue-500"></i> Hạ xuống người dùng
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                            
                                            <div class="border-t border-gray-100 my-1"></div>
                                            
                                            <form method="POST" class="block">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="reset_api_count">
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                                                    <i class="fas fa-sync-alt mr-2 text-blue-500"></i> Đặt lại số lượng API
                                                </button>
                                            </form>
                                            
                                            <button type="button" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 extend-subscription" data-user-id="<?php echo $user['id']; ?>">
                                                <i class="fas fa-crown mr-2 text-yellow-500"></i> Gia hạn gói đăng ký
                                            </button>
                                            
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($users['data'])): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Không tìm thấy người dùng nào phù hợp với tiêu chí tìm kiếm.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($users['pages'] > 1): ?>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Hiển thị <span class="font-medium"><?php echo ($page - 1) * $limit + 1; ?></span> đến 
                        <span class="font-medium"><?php echo min($page * $limit, $users['total']); ?></span> trong số 
                        <span class="font-medium"><?php echo $users['total']; ?></span> người dùng
                    </div>
                    <div class="flex items-center space-x-2">
                        <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&role=<?php echo urlencode($role); ?>" 
                            class="px-3 py-1 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-100">
                            Trước
                        </a>
                        <?php else: ?>
                        <span class="px-3 py-1 rounded-md text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 cursor-not-allowed">
                            Trước
                        </span>
                        <?php endif; ?>
                        
                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($users['pages'], $page + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&role=<?php echo urlencode($role); ?>" 
                            class="px-3 py-1 rounded-md text-sm font-medium <?php echo $i === $page ? 'text-white bg-blue-600 border border-blue-600' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-100'; ?>">
                            <?php echo $i; ?>
                        </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $users['pages']): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&role=<?php echo urlencode($role); ?>" 
                            class="px-3 py-1 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-100">
                            Tiếp
                        </a>
                        <?php else: ?>
                        <span class="px-3 py-1 rounded-md text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 cursor-not-allowed">
                            Tiếp
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Extend Subscription Modal -->
<div id="extendSubscriptionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Gia hạn gói đăng ký</h3>
            <button id="closeExtendModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="extendSubscriptionForm" method="POST">
            <input type="hidden" name="user_id" id="extend_user_id">
            <input type="hidden" name="action" value="extend_subscription">
            
            <div class="mb-4">
                <label for="plan" class="block text-sm font-medium text-gray-700 mb-1">Gói đăng ký</label>
                <select id="plan" name="plan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <?php foreach ($plans as $plan): ?>
                    <option value="<?php echo htmlspecialchars($plan['name']); ?>">
                        <?php echo htmlspecialchars($plan['name']); ?> (<?php echo number_format($plan['price']); ?> VND)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-6">
                <label for="days" class="block text-sm font-medium text-gray-700 mb-1">Thời hạn (ngày)</label>
                <input type="number" id="days" name="days" min="1" value="30" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancelExtendModal" class="mr-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Hủy
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Gia hạn
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reset API Confirmation Modal -->
<div id="resetApiModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Xác nhận đặt lại</h3>
            <button id="closeResetModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <p class="mb-6 text-gray-700">Bạn có chắc chắn muốn đặt lại số lượng yêu cầu API cho <strong>tất cả người dùng</strong>?</p>
        
        <div class="flex justify-end">
            <button type="button" id="cancelResetModal" class="mr-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Hủy
            </button>
            <button type="button" id="confirmResetApi" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Đặt lại
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
<script>
$(document).ready(function() {
    // Extend subscription
    $('.extend-subscription').click(function() {
        const userId = $(this).data('user-id');
        $('#extend_user_id').val(userId);
        $('#extendSubscriptionModal').removeClass('hidden');
    });
    
    $('#closeExtendModal, #cancelExtendModal').click(function() {
        $('#extendSubscriptionModal').addClass('hidden');
    });
    
    // Reset all API requests
    $('#resetAllApiRequests').click(function() {
        $('#resetApiModal').removeClass('hidden');
    });
    
    $('#closeResetModal, #cancelResetModal').click(function() {
        $('#resetApiModal').addClass('hidden');
    });
    
    $('#confirmResetApi').click(function() {
        $.ajax({
            url: 'ajax/reset-all-api-requests.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Đã xảy ra lỗi: ' + response.message);
                }
            },
            error: function() {
                alert('Đã xảy ra lỗi khi kết nối với máy chủ.');
            }
        });
    });
});
</script>

</body>
</html>
