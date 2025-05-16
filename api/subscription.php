<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Kiểm tra người dùng đã đăng nhập
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user']['id'];

// GET request - lấy danh sách gói subscription
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $plans = getSubscriptionPlans();
    
    echo json_encode([
        'success' => true, 
        'plans' => $plans
    ]);
    exit;
}

// POST request - đăng ký gói subscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['planName'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin gói đăng ký']);
        exit;
    }
    
    $planName = $data['planName'];
    $days = isset($data['days']) ? (int) $data['days'] : 30; // Default 30 days
    $paymentMethod = $data['paymentMethod'] ?? 'momo'; // Default payment method
    
    // Kiểm tra planName tồn tại
    $stmt = $pdo->prepare("SELECT * FROM subscription_plans WHERE name = :name");
    $stmt->bindParam(':name', $planName);
    $stmt->execute();
    $plan = $stmt->fetch();
    
    if (!$plan) {
        echo json_encode(['success' => false, 'message' => 'Gói đăng ký không hợp lệ']);
        exit;
    }
    
    // Giả lập xử lý thanh toán dựa trên phương thức
    switch ($paymentMethod) {
        case 'momo':
        case 'vnpay':
        case 'bank':
            // Giả lập thành công với tất cả phương thức
            $paymentSuccess = true;
            break;
        default:
            $paymentSuccess = false;
            echo json_encode(['success' => false, 'message' => 'Phương thức thanh toán không được hỗ trợ']);
            exit;
    }
    
    // Mô phỏng xử lý thanh toán
    if ($paymentSuccess) {
        // Thực hiện đăng ký subscription
        if (subscribeUser($userId, $planName, $days)) {
            // Cập nhật session user
            $_SESSION['user']['subscription_tier'] = $planName;
            
            echo json_encode([
                'success' => true, 
                'message' => 'Đăng ký gói thành công',
                'plan' => $plan
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật thông tin đăng ký']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Thanh toán không thành công']);
    }
    exit;
}

// Method not allowed
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
exit;