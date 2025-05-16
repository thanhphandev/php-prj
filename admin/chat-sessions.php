<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user']) || !isAdmin($_SESSION['user']['id'])) {
    header('Location: /auth/sign-in.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];
if ($role !== 'admin') {
    header('Location: chat.php');
    exit;
}
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_session') {
    
    $sessionIdToDelete = (int)$_POST['session_id'] ?? 0;
    if ($sessionIdToDelete > 0) {
        // Note: deleteChatSession function needs modification to allow admin deletion
        // For now, we'll implement the logic directly here
        try {
            $pdo->beginTransaction();
            
            // Delete messages
            $stmt = $pdo->prepare("DELETE FROM messages WHERE chat_id = :chatId");
            $stmt->bindParam(":chatId", $sessionIdToDelete);
            $stmt->execute();
            
            // Delete chat history
            $stmt = $pdo->prepare("DELETE FROM chat_history WHERE id = :chatId");
            $stmt->bindParam(":chatId", $sessionIdToDelete);
            
            if ($stmt->execute()) {
                $pdo->commit();
                $message = "Phiên chat đã được xóa thành công.";
                $messageType = "success";
            } else {
                $pdo->rollBack();
                $message = "Đã xảy ra lỗi khi xóa phiên chat.";
                $messageType = "error";
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Lỗi cơ sở dữ liệu: " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        $message = "ID phiên chat không hợp lệ.";
        $messageType = "error";
    }
}

// Get search/filter parameters
$search = $_GET['search'] ?? '';
$userIdFilter = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 15;

$sessions = getAdminPaginatedChatSessions($search, $userIdFilter, $page, $limit);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử chat | Admin Zesty</title>
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
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Lịch sử chat</h1>
            <p class="text-gray-600">Xem và quản lý các phiên chat của người dùng</p>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border-l-4 border-green-500' : 'bg-red-100 text-red-700 border-l-4 border-red-500'; ?> flex items-center">
                <div class="mr-3">
                    <?php if ($messageType === 'success'): ?>
                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <?php else: ?>
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <?php endif; ?>
                </div>
                <div><?php echo $message; ?></div>
            </div>
        <?php endif; ?>
        
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Tên phiên, username, email...">
                </div>
                
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Lọc theo User ID</label>
                    <input type="number" id="user_id" name="user_id" value="<?php echo htmlspecialchars($userIdFilter ?? ''); ?>" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập User ID">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-search mr-2"></i> Lọc
                    </button>
                    <a href="chat-sessions.php" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <i class="fas fa-times mr-2"></i> Xóa bộ lọc
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Sessions Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Phiên</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên phiên</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người dùng</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tin nhắn</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cập nhật lần cuối</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($sessions['data'])): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Không tìm thấy phiên chat nào.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sessions['data'] as $session): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        #<?php echo $session['id']; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($session['session_name']); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo htmlspecialchars($session['session_id']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($session['username']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($session['email']); ?></div>
                                        <div class="text-xs text-gray-500">(ID: <?php echo $session['user_id']; ?>)</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo number_format($session['message_count']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('d/m/Y H:i', strtotime($session['updated_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="view-chat.php?id=<?php echo $session['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="text-red-600 hover:text-red-900 delete-session" data-id="<?php echo $session['id']; ?>" data-name="<?php echo htmlspecialchars($session['session_name']); ?>" title="Xóa phiên chat">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($sessions['pages'] > 1): ?>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Hiển thị <span class="font-medium"><?php echo ($page - 1) * $limit + 1; ?></span> đến 
                            <span class="font-medium"><?php echo min($page * $limit, $sessions['total']); ?></span> trong số 
                            <span class="font-medium"><?php echo $sessions['total']; ?></span> phiên chat
                        </div>
                        <div class="flex items-center space-x-2">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&user_id=<?php echo urlencode($userIdFilter ?? ''); ?>" 
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
                            $endPage = min($sessions['pages'], $page + 2);
                            
                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&user_id=<?php echo urlencode($userIdFilter ?? ''); ?>" 
                                    class="px-3 py-1 rounded-md text-sm font-medium <?php echo $i === $page ? 'text-white bg-blue-600 border border-blue-600' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-100'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $sessions['pages']): ?>
                                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&user_id=<?php echo urlencode($userIdFilter ?? ''); ?>" 
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Xác nhận xóa</h3>
            <button id="closeDeleteModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <p class="mb-6 text-gray-700">Bạn có chắc chắn muốn xóa phiên chat <strong id="deleteSessionName"></strong>? Hành động này không thể hoàn tác.</p>
        
        <form id="deleteForm" method="POST">
            <input type="hidden" name="action" value="delete_session">
            <input type="hidden" name="session_id" id="deleteSessionId">
            
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
    // Delete session
    $('.delete-session').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        $('#deleteSessionId').val(id);
        $('#deleteSessionName').text(name);
        $('#deleteModal').removeClass('hidden');
    });
    
    // Close delete modal
    $('#closeDeleteModal, #cancelDeleteModal').click(function() {
        $('#deleteModal').addClass('hidden');
    });
});
</script>

</body>
</html>
