<?php

function createUser($username, $email, $fullname, $password)
{
    global $pdo;

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $avatar = 'https://robohash.org/' . urlencode($username);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, fullname, avatar)
                                  VALUES (:username, :email, :password, :fullname, :avatar)
    ");

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':fullname', $fullname);
    $stmt->bindParam(':avatar', $avatar);

    return $stmt->execute();
}

function verifyLogin($login, $password)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :login OR username = :login");
    $stmt->execute(['login' => $login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false;
}

function sendGroqRequest($message, $type = 'chat')
{
    $apiKey = getenv('GROQ_API_KEY');
    if (!$apiKey) {
        $apiKey = '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';
    }
    $url = 'https://api.groq.com/openai/v1/chat/completions';

    $systemMessage = "Bạn là một trợ lý chat thông minh, chuyên nghiệp, sử dụng ngôn từ lịch sự, chuyên nghiệp";
    if ($type === 'code') {
        $systemMessage = "Bạn là một trợ lý lập trình viên, chuyên nghiệp, sử dụng ngôn từ lịch sự, chuyên nghiệp";
    }
    $data = [
        'model' => 'llama3-70b-8192',
        'messages' => [
            [
                'role' => 'system',
                'content' => $systemMessage
            ],
            [
                'role' => 'user',
                'content' => $message
            ]
        ],
        'temperature' => 0.7,
        'max_tokens' => 2000,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ⚠️ Tạm tắt kiểm tra SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ⚠️ Tạm tắt kiểm tra SSL
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return [
            'success' => false,
            'message' => $error
        ];
    }

    return json_decode($response, true);
}

function generateSessionName($message): string
{
    $apiKey = getenv('GROQ_API_KEY') ?: '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';
    $url = 'https://api.groq.com/openai/v1/chat/completions';

    $systemMessage = "Bạn là một agent chuyên đặt tiêu đề ngắn gọn cho các cuộc hội thoại, đủ ý rõ ràng tường minh. Viết tiêu đề cho cuộc hội thoại sau đây:";
    $data = [
        'model' => 'llama3-70b-8192',
        'messages' => [
            ['role' => 'system', 'content' => $systemMessage],
            ['role' => 'user', 'content' => $message]
        ],
        'temperature' => 0.7,
        'max_tokens' => 120,
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_SSL_VERIFYPEER => false, // ⚠️ Chỉ dùng tạm cho dev/testing
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ],
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return 'Lỗi API: ' . $error;
    }

    $json = json_decode($response, true);
    return $json['choices'][0]['message']['content'] ?? 'Không thể tạo tiêu đề';
}


function saveChat($userId, $sessionId, $message, $response)
{
    global $pdo;

    // Kiểm tra xem cuộc trò chuyện đã tồn tại trong bảng chat_history chưa
    $stmt = $pdo->prepare("SELECT id FROM chat_history WHERE user_id = :userId AND session_id = :sessionId");
    $stmt->bindParam(":userId", $userId);
    $stmt->bindParam(":sessionId", $sessionId);
    $stmt->execute();

    $chat = $stmt->fetch();
    if (!$chat) {
        // Nếu chưa tồn tại, tạo mới một cuộc trò chuyện
        $session_name = generateSessionName($message);
        $stmt = $pdo->prepare(
            "
            INSERT INTO chat_history(user_id, session_id, session_name)
            VALUES (:userId, :sessionId, :session_name)"
        );
        $stmt->bindParam(":userId", $userId);
        $stmt->bindParam(":sessionId", $sessionId);
        $stmt->bindParam(":session_name", $session_name);
        $stmt->execute();
        $chatId = $pdo->lastInsertId();
    } else {
        // Nếu đã tồn tại, lấy ID của cuộc trò chuyện hiện tại
        $chatId = $chat['id'];

        // Cập nhật trường updated_at trong bảng chat_history
        $stmt = $pdo->prepare("UPDATE chat_history SET updated_at = CURRENT_TIMESTAMP WHERE id = :chatId");
        $stmt->bindParam(":chatId", $chatId);
        $stmt->execute();
    }

    // Lưu tin nhắn vào bảng messages
    $stmt = $pdo->prepare("INSERT INTO messages (chat_id, is_user, message, response) VALUES (:chatId, 1, :message, :response)");
    $stmt->bindParam(":chatId", $chatId);
    $stmt->bindParam(":message", $message);
    $stmt->bindParam(":response", $response);
    $stmt->execute();
    return $chatId;
}

function checkUserLimit($userId)
{
    global $pdo;

    $stmt = $pdo->prepare(
        "SELECT u.subscription_tier, u.api_requests_count, p.requests_limit
        FROM users u
        JOIN subscription_plans p ON u.subscription_tier = p.name
        WHERE u.id = :userId"
    );
    $stmt->bindParam(":userId", $userId);
    $stmt->execute();
    $user = $stmt->fetch();

    if (!$user) {
        return false;
    }
    return $user['api_requests_count'] < $user['requests_limit'];
}

function incrementUserRequests($userId)
{
    global $pdo;

    $stmt = $pdo->prepare("UPDATE users SET api_requests_count = api_requests_count + 1 WHERE id = :userId");
    $stmt->bindParam(":userId", $userId);
    $stmt->execute();
}

// Add new function for tracking subscription usage
function getUserUsage($userId)
{
    global $pdo;

    $stmt = $pdo->prepare(
        "SELECT u.subscription_tier, u.api_requests_count, p.requests_limit, p.name as plan_name, p.price, u.subscription_expiry
         FROM users u
         JOIN subscription_plans p ON u.subscription_tier = p.name
         WHERE u.id = :userId"
    );
    $stmt->bindParam(":userId", $userId);
    $stmt->execute();
    return $stmt->fetch();
}

function getUserChatSessions(int $userId): array
{
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT * FROM chat_history 
        WHERE user_id = :userId 
        ORDER BY created_at DESC
    ");

    $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        return [];
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function getChatMessages(int $chatId): array
{
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT * FROM messages 
        WHERE chat_id = :chatId 
        ORDER BY created_at ASC
    ");

    $stmt->bindValue(':chatId', $chatId, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        return [];
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

// Update chat session name
function updateChatSessionName($chatId, $newName)
{
    global $pdo;

    $stmt = $pdo->prepare("UPDATE chat_history SET session_name = :newName WHERE id = :chatId");
    $stmt->bindParam(":newName", $newName);
    $stmt->bindParam(":chatId", $chatId);
    return $stmt->execute();
}

// Subscribe user to a plan
function subscribeUser($userId, $planName, $expiryDays = 30)
{
    global $pdo;

    $expiryDate = date('Y-m-d H:i:s', strtotime("+$expiryDays days"));

    $stmt = $pdo->prepare(
        "UPDATE users 
         SET subscription_tier = :planName, 
             subscription_expiry = :expiryDate,
             api_requests_count = 0
         WHERE id = :userId"
    );
    $stmt->bindParam(":planName", $planName);
    $stmt->bindParam(":expiryDate", $expiryDate);
    $stmt->bindParam(":userId", $userId);
    return $stmt->execute();
}


// Get available subscription plans
function getSubscriptionPlans()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM subscription_plans ORDER BY price ASC");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Delete a chat session
function deleteChatSession($chatId, $userId)
{
    global $pdo;

    // First verify the chat belongs to this user
    $stmt = $pdo->prepare("SELECT id FROM chat_history WHERE id = :chatId AND user_id = :userId");
    $stmt->bindParam(":chatId", $chatId);
    $stmt->bindParam(":userId", $userId);
    $stmt->execute();

    if (!$stmt->fetch()) {
        return false; // Chat doesn't exist or doesn't belong to user
    }

    // Delete all messages in this chat
    $stmt = $pdo->prepare("DELETE FROM messages WHERE chat_id = :chatId");
    $stmt->bindParam(":chatId", $chatId);
    $stmt->execute();

    // Delete the chat history entry
    $stmt = $pdo->prepare("DELETE FROM chat_history WHERE id = :chatId");
    $stmt->bindParam(":chatId", $chatId);
    return $stmt->execute();
}

// Get user profile data
function getUserProfile($userId)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :userId");
    $stmt->bindParam(":userId", $userId);
    $stmt->execute();
    return $stmt->fetch();
}

// Update user profile
function updateUserProfile($userId, $fullname, $email, $avatar = null)
{
    global $pdo;

    $query = "UPDATE users SET fullname = :fullname, email = :email";
    $params = [
        ":userId" => $userId,
        ":fullname" => $fullname,
        ":email" => $email
    ];

    if ($avatar) {
        $query .= ", avatar = :avatar";
        $params[":avatar"] = $avatar;
    }

    $query .= " WHERE id = :userId";

    $stmt = $pdo->prepare($query);
    return $stmt->execute($params);
}

// Get AI chat history with pagination
function getPaginatedChatHistory($userId, $page = 1, $limit = 10)
{
    global $pdo;

    $offset = ($page - 1) * $limit;

    // Get total count
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM chat_history WHERE user_id = :userId");
    $countStmt->bindParam(":userId", $userId);
    $countStmt->execute();
    $totalCount = $countStmt->fetchColumn();

    // Get paginated results
    $stmt = $pdo->prepare(
        "SELECT ch.*, 
        (SELECT message FROM messages WHERE chat_id = ch.id ORDER BY created_at ASC LIMIT 1) AS first_message
        FROM chat_history ch
        WHERE ch.user_id = :userId
        ORDER BY ch.created_at DESC
        LIMIT :limit OFFSET :offset"
    );
    $stmt->bindParam(":userId", $userId);
    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    return [
        'total' => $totalCount,
        'pages' => ceil($totalCount / $limit),
        'current_page' => $page,
        'data' => $stmt->fetchAll()
    ];
}
