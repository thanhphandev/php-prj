<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || !isAdmin($_SESSION['user']['id'])) {
    header('Location: /auth/sign-in.php');
    exit;
}

$adminUserId = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];
if ($role !== 'admin') {
    header('Location: /chat.php');
    exit;
}

$chatId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($chatId <= 0) {
    header('Location: chat-sessions.php');
    exit;
}

// Get chat session details
$stmt = $pdo->prepare("
    SELECT ch.*, u.username, u.email 
    FROM chat_history ch 
    JOIN users u ON ch.user_id = u.id 
    WHERE ch.id = :chatId
");
$stmt->bindParam(':chatId', $chatId);
$stmt->execute();
$session = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$session) {
    // Session not found or doesn't belong to any user (shouldn't happen with JOIN)
    header('Location: chat-sessions.php');
    exit;
}

// Get messages for this chat
$messages = getChatMessages($chatId);

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết phiên chat | Admin Zesty</title>
    <link rel="icon" href="/assets/images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dompurify@3.0.5/dist/purify.min.js"></script>

</head>

<body class="bg-gray-50 min-h-screen">

    <?php include 'layout/header.php'; ?>

    <div class="flex">
        <?php include 'layout/sidebar.php'; ?>

        <main class="flex-1 p-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Chi tiết phiên chat</h1>
                        <p class="text-gray-600">Xem nội dung của phiên chat #<?php echo $chatId; ?></p>
                    </div>
                    <a href="chat-sessions.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
                    </a>
                </div>

                <div class="mt-4 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">Tên phiên: <span class="font-medium text-gray-900"><?php echo htmlspecialchars($session['session_name']); ?></span></p>
                    ID: <span class="font-medium text-gray-900"><?php echo htmlspecialchars($session['session_id']); ?></span></p>
                    <p class="text-sm text-gray-500">Người dùng: <span class="font-medium text-gray-900"><?php echo htmlspecialchars($session['username']); ?> (<?php echo htmlspecialchars($session['email']); ?>)</span></p>
                    <p class="text-sm text-gray-500">Session
                    <p class="text-sm text-gray-500">Tạo lúc: <span class="font-medium text-gray-900"><?php echo date('d/m/Y H:i', strtotime($session['created_at'])); ?></span></p>
                    <p class="text-sm text-gray-500">Cập nhật lần cuối: <span class="font-medium text-gray-900"><?php echo date('d/m/Y H:i', strtotime($session['updated_at'])); ?></span></p>
                </div>
            </div>
            <?php foreach ($messages as $msg): ?>
                <?php
                $createdAt = date('H:i, d/m/Y', strtotime($msg['created_at']));
                $userAvatar = htmlspecialchars($session['avatar'] ?? 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y');
                $username = htmlspecialchars($session['username'] ?? 'Bạn');
                ?>

                <?php if (!empty($msg['message'])): ?>
                    <!-- User Message -->
                    <div class="message user-message mb-4">
                        <div class="flex items-start">
                            <img src="<?= $userAvatar ?>" alt="User Avatar" class="w-8 h-8 rounded-full mr-3 mt-0.5">
                            <div class="flex-1">
                                <div class="font-medium text-sm mb-1"><?= $username ?></div>
                                <div class="message-content prose prose-sm max-w-none bg-gray-50 p-3 rounded-lg shadow-sm"
                                    data-raw="<?= htmlspecialchars($msg['message'], ENT_QUOTES) ?>">
                                    <!-- Rendered by JS -->
                                </div>
                                <div class="text-xs text-gray-500 mt-1"><?= $createdAt ?></div>
                            </div>
                        </div>
                        <div class="ml-11 mt-2 flex items-center space-x-2">
                            <button class="text-xs text-accent hover:text-indigo-700">
                                <i class="far fa-copy mr-1"></i> Copy
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($msg['response'])): ?>
                    <!-- AI Message -->
                    <div class="message ai-message mb-4">
                        <div class="flex items-start">
                            <img src="/assets/images/logo.png" alt="Logo" class="w-8 h-8 rounded-full mr-3 mt-0.5">
                            <div class="flex-1">
                                <div class="font-medium text-sm mb-1">Zesty AI</div>
                                <div class="message-content prose prose-sm max-w-none bg-white p-3 rounded-lg shadow"
                                    data-raw="<?= htmlspecialchars($msg['response'], ENT_QUOTES) ?>">
                                    <!-- Rendered by JS -->
                                </div>
                                <div class="text-xs text-gray-500 mt-1"><?= $createdAt ?></div>
                            </div>
                        </div>
                        <div class="ml-11 mt-2 flex items-center space-x-3">
                            <button class="text-xs text-gray-500 hover:text-gray-700">
                                <i class="far fa-thumbs-up mr-1"></i> Helpful
                            </button>
                            <button class="text-xs text-gray-500 hover:text-gray-700">
                                <i class="far fa-thumbs-down mr-1"></i> Not helpful
                            </button>
                            <button class="text-xs text-accent hover:text-indigo-700">
                                <i class="far fa-copy mr-1"></i> Copy
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>


        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('.markdown-content').forEach((block) => {
                const rawMarkdown = block.textContent;
                block.innerHTML = marked.parse(rawMarkdown);
            });
            hljs.highlightAll();
        });
    </script>

</body>

<script src="../assets/js/utils.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.message-content[data-raw]').forEach(el => {
        const raw = el.getAttribute('data-raw') || '';
        const html = formatMessageContent(raw);
        el.innerHTML = DOMPurify.sanitize(html);
    });
});
</script>

</html>