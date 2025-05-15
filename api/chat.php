<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Không đủ quyền truy cập']);
    exit;
}

$userId = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data) || !isset($data['message'], $data['sessionId'], $data['type'])) {
        echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
        exit;
    }

    $message = $data['message'];
    $sessionId = $data['sessionId'];
    $type = $data['type'];

    // Kiểm tra giới hạn người dùng
    if (!checkUserLimit($userId)) {
        echo json_encode([
            'error' => 'Bạn đã vượt quá giới hạn sử dụng API. Vui lòng nâng cấp gói dịch vụ của bạn.'
        ]);
        exit;
    }

    $chat_history = getChatHistory($userId, $sessionId);
    $apiResponse = sendGeminiRequest($message, $type, $chat_history);

    if (!$apiResponse['success']) {
        echo json_encode([
            'error' => 'Phản hồi từ API không hợp lệ',
            'raw' => $apiResponse['raw'] ?? null,
            'details' => $apiResponse['error'] ?? 'Không rõ lỗi'
        ]);
        exit;
    }

    $content = $apiResponse['message'];
    $chatId = saveChat($userId, $sessionId, $message, $content);
    incrementUserRequests($userId);

    echo json_encode([
        'success' => true,
        'message' => $message,
        'response' => $content,
        'chatId' => $chatId,
        'timestamp' => date('Y-m-d H:i:s'),
    ]);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['session_id'])) {
    $sessions = getUserChatSessions($userId);

    echo json_encode([
        'success' => true,
        'sessions' => $sessions
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['session_id'])) {
    $sessionId = $_GET['session_id'];

    // Get chat history for this session
    $stmt = $pdo->prepare("
        SELECT m.* FROM messages m
        JOIN chat_history c ON m.chat_id = c.id
        WHERE c.user_id = ? AND c.session_id = ?
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([$userId, $sessionId]);
    $messages = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'messages' => $messages
    ]);
    exit;
}

echo json_encode(['error' => 'Phương thức không hợp lệ']);
exit;
