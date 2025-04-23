<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../includes/functions.php';

if(!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Không đủ quyền truy cập']);
    exit;
}

$userId = $_SESSION['user']['id'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data) || !isset($data['message']) || !isset($data['sessionId']) || !isset($data['type'])) {
        echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
        exit;
    }

    $message = $data['message'];
    $sessionId = $data['sessionId'];
    $type = $data['type'];

    // check user limit

    if(!checkUserLimit($userId)){
        echo json_encode([
            'error' => 'Bạn đã vượt quá giới hạn sử dụng API. Vui lòng nâng cấp gói dịch vụ của bạn.'
        ]);
        exit;
    }

    $apiResponse = sendGroqRequest($message, $type);
    if (!isset($apiResponse['choices'][0]['message']['content'])) {
        echo json_encode(['error' => 'Phản hồi từ API không hợp lệ', 'raw' => $apiResponse]);
        exit;
    }

    
    $content = $apiResponse['choices'][0]['message']['content'];

    $chatId = saveChat($userId, $sessionId,$message, $content);
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