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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'create') {
            $name = trim($_POST['name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $limit = intval($_POST['limit'] ?? 0);
            $features = trim($_POST['features'] ?? '');
            
            if (empty($name) || $limit <= 0) {
                $message = "Vui lòng nhập đầy đủ thông tin gói đăng ký";
                $messageType = "error";
            } else {
                if (createSubscriptionPlan($name, $price, $limit, $features, $userId)) {
                    $message = "Đã tạo gói đăng ký mới thành công";
                    $messageType = "success";
                } else {
                    $message = "Đã xảy ra lỗi khi tạo gói đăng ký";
                    $messageType = "error";
                }
            }
        } elseif ($action === 'update') {
            $id = intval($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $limit = intval($_POST['limit'] ?? 0);
            $features = trim($_POST['features'] ?? '');
            
            if (empty($name) || $limit <= 0 || $id <= 0) {
                $message = "Vui lòng nhập đầy đủ thông tin gói đăng ký";
                $messageType = "error";
            } else {
                if (updateSubscriptionPlan($id, $name, $price, $limit, $features, $userId)) {
                    $message = "Đã cập nhật gói đăng ký thành công";
                    $messageType = "success";
                } else {
                    $message = "Đã xảy ra lỗi khi cập nhật gói đăng ký";
                    $messageType = "error";
                }
            }
        } elseif ($action === 'delete') {
            $id = intval($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                $message = "ID gói đăng ký không hợp lệ";
                $messageType = "error";
            } else {
                if (deleteSubscriptionPlan($id, $userId)) {
                    $message = "Đã xóa gói đăng ký thành công";
                    $messageType = "success";
                } else {
                    $message = "Đã xảy ra lỗi khi xóa gói đăng ký";
                    $messageType = "error";
                }
            }
        }
    }
}

$plans = getSubscriptionPlans();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý gói đăng ký | Admin Zesty</title>
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
                <h1 class="text-3xl font-bold text-gray-800">Quản lý gói đăng ký</h1>
                <p class="text-gray-600">Tạo và quản lý các gói đăng ký có sẵn</p>
            </div>
            <div>
                <button type="button" id="createPlanBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-plus mr-2"></i> Thêm gói mới
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
        
        <!-- Subscription Plans Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($plans as $plan): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($plan['name']); ?></h3>
                        <div class="flex space-x-2">
                            <button type="button" class="text-blue-600 hover:text-blue-800 edit-plan" data-id="<?php echo $plan['id']; ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="text-red-600 hover:text-red-800 delete-plan" data-id="<?php echo $plan['id']; ?>" data-name="<?php echo htmlspecialchars($plan['name']); ?>">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="text-3xl font-bold text-gray-900">
                            <?php if ($plan['price'] > 0): ?>
                                <?php echo number_format($plan['price']); ?> <span class="text-sm font-medium">VND</span>
                            <?php else: ?>
                                Miễn phí
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-500 text-sm">mỗi tháng</p>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-bolt mr-2 text-blue-500"></i>
                            <span><strong><?php echo number_format($plan['requests_limit']); ?></strong> yêu cầu API mỗi ngày</span>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Tính năng:</h4>
                        <ul class="space-y-2">
                            <?php foreach (explode('|', $plan['features']) as $feature): ?>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                                <span class="text-gray-600 text-sm"><?php echo htmlspecialchars(trim($feature)); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div class="mt-4 text-sm text-gray-500">
                        Tạo lúc: <?php echo date('d/m/Y H:i', strtotime($plan['created_at'])); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>

<!-- Create/Edit Plan Modal -->
<div id="planModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Thêm gói đăng ký mới</h3>
            <button id="closePlanModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="planForm" method="POST">
            <input type="hidden" name="action" id="planAction" value="create">
            <input type="hidden" name="id" id="planId" value="">
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên gói</label>
                <input type="text" id="name" name="name" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Giá (VND)</label>
                <input type="number" id="price" name="price" min="0" step="1000" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-4">
                <label for="limit" class="block text-sm font-medium text-gray-700 mb-1">Giới hạn yêu cầu API (mỗi ngày)</label>
                <input type="number" id="limit" name="limit" min="1" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-6">
                <label for="features" class="block text-sm font-medium text-gray-700 mb-1">Tính năng (phân tách bằng ký tự |)</label>
                <textarea id="features" name="features" rows="4" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                <p class="text-xs text-gray-500 mt-1">Ví dụ: Tính năng 1 | Tính năng 2 | Tính năng 3</p>
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancelPlanModal" class="mr-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Hủy
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Xác nhận xóa</h3>
            <button id="closeDeleteModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <p class="mb-6 text-gray-700">Bạn có chắc chắn muốn xóa gói đăng ký <strong id="deletePlanName"></strong>?</p>
        
        <form id="deleteForm" method="POST">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="deletePlanId">
            
            <div class="flex justify-end">
                <button type="button" id="cancelDeleteModal" class="mr-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Hủy
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Xóa
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Create new plan
    $('#createPlanBtn').click(function() {
        $('#modalTitle').text('Thêm gói đăng ký mới');
        $('#planAction').val('create');
        $('#planId').val('');
        $('#name').val('');
        $('#price').val('0');
        $('#limit').val('10');
        $('#features').val('');
        $('#planModal').removeClass('hidden');
    });
    
    $('.edit-plan').click(function() {
        const id = $(this).data('id');
        
        // Get plan data via AJAX
        $.ajax({
            url: 'ajax/get-plan.php',
            type: 'GET',
            data: {id: id},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const plan = response.plan;
                    
                    $('#modalTitle').text('Chỉnh sửa gói đăng ký');
                    $('#planAction').val('update');
                    $('#planId').val(plan.id);
                    $('#name').val(plan.name);
                    $('#price').val(plan.price);
                    $('#limit').val(plan.requests_limit);
                    $('#features').val(plan.features);
                    $('#planModal').removeClass('hidden');
                } else {
                    alert('Đã xảy ra lỗi: ' + response.message);
                }
            },
            error: function() {
                alert('Đã xảy ra lỗi khi kết nối với máy chủ.');
            }
        });
    });
    
    // Delete plan
    $('.delete-plan').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        $('#deletePlanId').val(id);
        $('#deletePlanName').text(name);
        $('#deleteModal').removeClass('hidden');
    });
    
    // Close modals
    $('#closePlanModal, #cancelPlanModal').click(function() {
        $('#planModal').addClass('hidden');
    });
    
    $('#closeDeleteModal, #cancelDeleteModal').click(function() {
        $('#deleteModal').addClass('hidden');
    });
});
</script>

</body>
</html>
