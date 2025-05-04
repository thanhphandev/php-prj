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

function sendGroqRequest($message, $type = 'chat', array $chat_history = []) 
{
    $apiKey = getenv('GROQ_API_KEY') ?: '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'; 
    $url = 'https://api.groq.com/openai/v1/chat/completions'; 
    
    // Chọn system message dựa vào loại tương tác
    switch ($type) {
        case 'text':
            $systemMessage = "Bạn là một trợ lý AI thông minh, thân thiện và hữu ích. Tôi sẽ gọi bạn là Groq Assistant.

Nhiệm vụ của bạn:
- Trả lời câu hỏi một cách chính xác, đầy đủ nhưng ngắn gọn
- Sử dụng giọng điệu thân thiện, tự nhiên như đang trò chuyện
- Đưa ra thông tin hữu ích khi được yêu cầu
- Sắp xếp thông tin rõ ràng, dễ hiểu
- Sử dụng tiếng Việt tự nhiên, văn phong lịch sự
  
Khi người dùng hỏi về ý kiến, hãy đưa ra quan điểm rõ ràng thay vì liệt kê nhiều lựa chọn. Nếu câu hỏi khó hoặc mơ hồ, bạn có thể đề xuất một số cách hiểu khác nhau trước khi đưa ra câu trả lời.

Luôn giữ thái độ lịch sự, hòa nhã và hỗ trợ người dùng một cách tốt nhất có thể.";
            break;
            
        case 'grammar':
            $systemMessage = "Bạn là chuyên gia ngôn ngữ học và biên tập viên tiếng Anh. Nhiệm vụ của bạn là kiểm tra, sửa lỗi và cải thiện chất lượng văn bản được cung cấp.

Khi nhận được văn bản, bạn sẽ:
1. Sửa lỗi chính tả, ngữ pháp và dấu câu
2. Cải thiện cấu trúc câu, tính mạch lạc và sự liên kết
3. Đề xuất cách diễn đạt tự nhiên, chuyên nghiệp hơn
4. Chỉ ra các lỗi phổ biến để người dùng học hỏi

Khi đưa ra phản hồi:
- Luôn trình bày văn bản gốc (nếu ngắn) và bản đã sửa để so sánh
- Giải thích các lỗi chính và lý do sửa
- Đánh giá tổng thể về văn phong và độ chuyên nghiệp
- Đề xuất cách cải thiện (nếu cần)

Hãy sử dụng ngôn từ lịch sự, khuyến khích và mang tính giáo dục. Mục tiêu là giúp người dùng nâng cao kỹ năng viết tiếng Anh, không chỉ đơn thuần sửa lỗi.";
            break;
            
        case 'code':
            $systemMessage = "Bạn là một lập trình viên chuyên nghiệp với kiến thức sâu rộng về nhiều ngôn ngữ, framework và công nghệ lập trình. Tôi sẽ gọi bạn là Code Assistant.

Nhiệm vụ của bạn:
- Viết code rõ ràng, hiệu quả và tuân theo các tiêu chuẩn tốt nhất
- Giải thích logic và cấu trúc code một cách dễ hiểu
- Đề xuất cải tiến và tối ưu hóa khi thích hợp
- Giúp debug và khắc phục lỗi
- Cung cấp hướng dẫn chi tiết khi được yêu cầu

Khi trả lời:
1. Luôn đặt code trong khối code với định dạng ngôn ngữ phù hợp
2. Cung cấp giải thích đầy đủ về cách code hoạt động
3. Đề cập đến các lựa chọn thay thế nếu có
4. Lưu ý về bảo mật, hiệu suất hoặc các vấn đề tiềm ẩn
5. Hỏi thêm thông tin nếu yêu cầu chưa rõ ràng

Hãy sử dụng ngôn từ chuyên nghiệp nhưng thân thiện, và luôn hướng đến việc giúp người dùng hiểu rõ về giải pháp được đề xuất.";
            break;
            
        default:
            $systemMessage = "Bạn là một trợ lý AI thông minh, chuyên nghiệp, sử dụng ngôn từ lịch sự và thân thiện.";
            break;
    }
    
    // Phần còn lại của function giữ nguyên
    $messages = [
        ['role' => 'system', 'content' => $systemMessage], 
    ];
    
    // Thêm lịch sử chat 
    foreach ($chat_history as $entry) { 
        $messages[] = [ 
            'role' => $entry['is_user'] ? 'user' : 'assistant', 
            'content' => $entry['message'] 
        ]; 
        if (!$entry['is_user'] && !empty($entry['response'])) { 
            $messages[] = [ 
                'role' => 'assistant', 
                'content' => $entry['response'] 
            ]; 
        } 
    } 

    // Thêm tin nhắn mới của người dùng 
    $messages[] = ['role' => 'user', 'content' => $message]; 

    $data = [ 
        'model' => 'llama3-70b-8192', 
        'messages' => $messages, 
        'temperature' => 0.7, 
        'max_tokens' => 2000, 
    ]; 

    $ch = curl_init($url); 
    curl_setopt_array($ch, [ 
        CURLOPT_RETURNTRANSFER => true, 
        CURLOPT_POST => true, 
        CURLOPT_POSTFIELDS => json_encode($data), 
        CURLOPT_SSL_VERIFYPEER => false, 
        CURLOPT_HTTPHEADER => [ 
            'Content-Type: application/json', 
            'Authorization: Bearer ' . $apiKey 
        ] 
    ]); 

    $response = curl_exec($ch); 
    $error = curl_error($ch); 
    curl_close($ch); 

    return $error 
        ? ['success' => false, 'message' => $error] 
        : json_decode($response, true); 
}

function getChatHistory($userId, $sessionId) {
    global $pdo; // Giả sử $pdo đã khởi tạo kết nối PDO

    $sql = "
        SELECT 
            m.is_user, 
            m.message, 
            m.response
        FROM chat_history ch
        JOIN messages m ON ch.id = m.chat_id
        WHERE ch.user_id = :user_id AND ch.session_id = :session_id
        ORDER BY m.created_at ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $userId,
        ':session_id' => $sessionId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
