<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!isset($_SESSION['user']) || !isAdmin($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền truy cập vào trang này']);
    exit;
}

$userId = $_SESSION['user']['id'];

try {
    $result = resetUserApiRequests();
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Đã đặt lại số lần yêu cầu API cho tất cả người dùng']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể đặt lại số lần yêu cầu API']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
