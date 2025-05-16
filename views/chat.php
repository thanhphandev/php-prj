<?php
require_once 'includes/functions.php';
$user = $_SESSION['user'] ?? null;
if ($user === null) {
    header("Location: /auth/sign-in.php");
    exit;
}
$sessionId = $_GET['session_id'] ?? '';

$userSubscription = getUserUsage($user['id']);
$requestsLeft = $userSubscription['requests_limit'] - $userSubscription['api_requests_count'];
?>

<!-- Main container -->
<div class="flex h-screen bg-gray-50 text-gray-900">
    <div class="hidden md:flex w-64 bg-gray-800 text-white flex-col">
        <!-- Logo/Brand area -->
        <div class="p-4 border-b border-gray-700 flex items-center justify-between">
            <div class="font-bold text-xl">Zesty AI</div>
            <button id="newChatBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm">
                <i class="fas fa-plus mr-1"></i> Tạo mới
            </button>
        </div>

        <!-- User info & subscription -->
        <div class="p-3 border-b border-gray-700 flex flex-col">
            <div class="flex items-center mb-2 gap-3">
                <img src="<?= $user['avatar'] ?>" alt="User Avatar" class="w-10 h-10 rounded-full border-2 border-gray-700">
                <div class="text-sm truncate"><?= htmlspecialchars($user['fullname'] ?? 'User') ?>
                </div>
            </div>

            <div class="text-xs text-gray-300 mb-1">
                Gói hiện tại: <span class="font-medium"><?= $userSubscription['plan_name'] ?? 'Free' ?></span>
            </div>

            <div class="text-xs text-gray-300 mb-3">
                <span id="requestLeft" class="font-medium">Bạn còn <?= $requestsLeft ?> lượt</span>
            </div>

            <button id="upgradeBtn" class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white py-1.5 rounded-md text-sm font-medium">
                <a href="subscription-plans.php">
                    <i class="fas fa-arrow-up mr-1"></i> Nâng cấp gói
                </a>
            </button>

        </div>

        <!-- Chat history list -->
        <div class="flex-1 overflow-y-auto" id="chatHistoryList">
            <div class="p-3 pt-4 text-xs text-gray-400 uppercase font-medium">Lịch sử chat</div>
            <!-- Chat history will be loaded here -->
            <div class="chat-sessions-container px-2"></div>
        </div>

        <!-- Settings & logout -->
        <div class="p-3 border-t border-gray-700">
            <div class="flex flex-col space-y-2">
                <button class="text-gray-300 hover:text-white text-sm py-2 px-3 rounded-md hover:bg-gray-700 flex items-center">
                    <i class="fas fa-cog mr-2"></i> Cài đặt
                </button>
                <a href="/auth/logout.php">
                    <button class="text-gray-300 hover:text-white text-sm py-2 px-3 rounded-md hover:bg-gray-700 flex items-center">
                        <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                    </button>
                </a>
            </div>
        </div>
    </div>
    <!-- Main chat area -->
    <div class="flex-1 flex flex-col">
        <!-- Chọn loại AI -->
        <div class="bg-white border-b border-gray-200 p-3 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="font-medium">Mô hình:</div>
                <select id="chatTypeSelect" class="bg-gray-100 border border-gray-300 rounded-md px-3 py-1.5 text-sm">
                    <option value="text">Trò chuyện chung</option>
                    <option value="creative">Check ngữ pháp</option>
                    <option value="code">Trợ lý lập trình</option>
                </select>
            </div>

            <div class="hidden md:inline text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                <span id="modelInfoText">Mô hình tiêu chuẩn cho trợ giúp chung</span>
            </div>
        </div>

        <!-- Khu vực hiển thị tin nhắn -->
        <div class="flex-1 overflow-y-auto p-4 bg-white" id="chatMessagesContainer">
            <!-- Tin nhắn chào mừng -->
            <div class="flex flex-col items-center justify-center h-full text-center px-4" id="welcomeMessage">
                <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-4">
                    <img src="/assets/images/logo.png" alt="Logo" class="w-12 h-12">
                </div>
                <h2 class="text-2xl font-bold mb-2">Trợ lý Zesty AI</h2>
                <p class="text-gray-600 mb-6 max-w-md">Tôi có thể giúp gì cho bạn hôm nay? Hãy đặt câu hỏi hoặc chọn một gợi ý bên dưới.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 w-full max-w-xl">
                    <button class="suggestion-btn bg-gray-100 hover:bg-gray-200 p-3 rounded-lg text-left">
                        <div class="font-medium mb-1">Viết bài luận</div>
                        <div class="text-sm text-gray-600">về bất kỳ chủ đề nào với nghiên cứu chi tiết</div>
                    </button>
                    <button class="suggestion-btn bg-gray-100 hover:bg-gray-200 p-3 rounded-lg text-left">
                        <div class="font-medium mb-1">Giải thích khái niệm</div>
                        <div class="text-sm text-gray-600">bằng ngôn ngữ đơn giản, dễ hiểu</div>
                    </button>
                    <button class="suggestion-btn bg-gray-100 hover:bg-gray-200 p-3 rounded-lg text-left">
                        <div class="font-medium mb-1">Tạo mã nguồn</div>
                        <div class="text-sm text-gray-600">cho một tác vụ lập trình cụ thể</div>
                    </button>
                    <button class="suggestion-btn bg-gray-100 hover:bg-gray-200 p-3 rounded-lg text-left">
                        <div class="font-medium mb-1">Lập kế hoạch</div>
                        <div class="text-sm text-gray-600">để đạt được một mục tiêu cụ thể</div>
                    </button>
                </div>
            </div>

            <!-- Tin nhắn thực tế -->
            <div id="chatMessages" class="hidden space-y-6 pb-6"></div>
        </div>

        <!-- Khu vực nhập tin nhắn -->
        <div class="border-t border-subtle bg-background p-4">
            <div class="relative">
                <div class="rounded-lg border-subtle">
                    <textarea
                        id="messageInput"
                        class="w-full max-h-[200px] overflow-y-auto rounded-lg border-0 p-3 pr-20 resize-none focus:outline-none"
                        placeholder="Bạn muốn hỏi điều gì..."
                        rows="1"></textarea>

                    <div class="absolute bottom-8 right-2 flex items-center space-x-1">
                        <button
                            id="sendButton"
                            class="bg-primary hover:bg-hover text-white py-2 px-4 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                            <i class="far fa-paper-plane"></i>
                        </button>
                    </div>
                </div>

                <div class="mt-2 text-xs text-gray-500 flex justify-between">
                    <div>
                        <span id="characterCount">0</span> ký tự
                    </div>
                    <div>
                        Zesty AI được hỗ trợ bởi <a href="https://gemini.google.com/" class="text-blue-500 hover:underline" target="_blank">Gemini API</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Loading indicator -->
<div id="loadingOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg flex items-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
        <div>Đang xử lý yêu cầu...</div>
    </div>
</div>

<script src="../assets/js/utils.js"></script>

<script>
    $(document).ready(function() {
        let currentSessionId = "<?= htmlspecialchars($sessionId) ?>";
        let currentChatType = $('#chatTypeSelect').val();
        if (currentSessionId === '') {
            createNewSession();
        } else {
            loadChatSession(currentSessionId);
        }

        loadChatSessions();

        $('#messageInput').on('input', function() {
            $('#characterCount').text($(this).val().length);
            $('#sendButton').prop('disabled', $(this).val().trim().length === 0);

            this.style.height = 'auto';

            const maxHeight = parseInt(getComputedStyle(this).maxHeight, 10);
            const scrollHeight = this.scrollHeight;

            this.style.height = Math.min(scrollHeight, maxHeight) + 'px';
        });


        $('#messageInput').on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (!$('#sendButton').prop('disabled')) {
                    sendMessage();
                }
            }
        });

        $('#sendButton').on('click', function() {
            sendMessage();
        })
        // FINE
        $('#chatTypeSelect').on('change', function() {
            currentChatType = $(this).val();
            updateModelInfo();
        });

        // Update model info text based on selection
        function updateModelInfo() {
            let infoText = '';
            switch (currentChatType) {
                case 'grammar':
                    infoText = 'Tối ưu cho kiểm tra ngữ pháp và chính tả tiếng Anh';
                    break;
                case 'code':
                    infoText = 'Tối ưu cho lập trình, sửa lỗi và hỗ trợ kỹ thuật';
                    break;
                default:
                    infoText = 'Mô hình văn bản tiêu chuẩn cho trợ giúp chung';
            }
            $('#modelInfoText').text(infoText);
        }

        $('#newChatBtn').on('click', function() {
            createNewSession();
        });


        $('.suggestion-btn').on('click', function() {
            const text = $(this).find('.font-medium').text();
            $('#messageInput').val(text).trigger('input');
            $('#messageInput').focus();
        });

        function createNewSession() {
            history.pushState(null, '', '/chat.php');
            currentSessionId = "<?= uniqid() ?>"; // random chat session ID

            $('#chatMessages').html('').addClass('hidden');
            $('#welcomeMessage').removeClass('hidden');
            $('#messageInput').val('').trigger('input');
        }

        function loadChatSession(sessionId) {
            // Skip if already loaded
            if (sessionId === currentSessionId && $('#chatMessages').children().length > 0) return;
            currentSessionId = sessionId;
            $('#welcomeMessage').addClass('hidden');
            $('#chatMessages').removeClass('hidden').html('');

            // Show loading indicator
            $('#loading').removeClass('hidden');

            // Fetch chat history
            $.ajax({
                url: `/api/chat.php?session_id=${sessionId}`,
                type: 'GET',
                success: function(data) {
                    // Hide loading indicator
                    $('#loading').addClass('hidden');

                    if (data.success && data.messages && data.messages.length > 0) {
                        renderMessages(data.messages); //check
                        updateUrlWithSessionId(sessionId); //check
                    } else if (data.success && (!data.messages || data.messages.length === 0)) {
                        $('#chatMessages').addClass('hidden');
                        $('#welcomeMessage').removeClass('hidden');
                    } else {
                        addSystemMessage('error', data.error || 'Failed to load chat history.');
                    }

                    highlightActiveSession();
                },
                error: function() {
                    // Hide loading indicator
                    $('#loading').addClass('hidden');
                    addSystemMessage('error', 'Network error when loading chat history.');
                }
            });
        }

        let requestsLeft = <?= $requestsLeft ?>;

        function sendMessage() {
            history.pushState(null, '', `/chat.php?session_id=${currentSessionId}`);
            const message = $('#messageInput').val().trim();
            if (message === '') return;
            // Hide welcome message if visible
            $('#welcomeMessage').addClass('hidden');
            $('#chatMessages').removeClass('hidden');

            // Clear input
            $('#messageInput').val('').trigger('input');
            $('#messageInput').css('height', 'auto');

            // Show loading
            $('#loadingOverlay').removeClass('hidden');
            addMessage('user', message, new Date().toISOString());
            requestsLeft--;
            updateRequestsLeft(requestsLeft);
            $.ajax({
                url: '/api/chat.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    message: message,
                    sessionId: currentSessionId,
                    type: currentChatType
                }),
                success: function(response) {
                    // Hide loading
                    $('#loadingOverlay').addClass('hidden');
                    if (response.success) {

                        addMessage('ai', response.response, response.timestamp);

                        scrollToBottom();
                        loadChatSessions();
                    } else {
                        // Add error message
                        const errorMsg = response.error || 'An error occurred. Please try again.';
                        addSystemMessage('error', errorMsg);
                    }
                },
                error: function() {
                    // Hide loading
                    $('#loadingOverlay').addClass('hidden');

                    // Add error message
                    addSystemMessage('error', 'Network error. Please check your connection and try again.');
                }
            });
        }


        function renderMessages(messages) {
            $('#chatMessages').html('');
            messages.forEach(message => {
                const sender = message.is_user === 1 ? 'user' : 'ai';
                addMessage(sender, message.message, message.created_at);
                addMessage('ai', message.response, message.created_at);
            });
            scrollToBottom();
        }

        function addMessage(sender, content, createdAt) {
            const messageHtml = `
        <div class="message ${sender === 'user' ? 'user-message' : 'ai-message'}">
            <div class="flex items-start">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold mr-3 mt-0.5">
                    ${sender === 'user' ? `<img src="<?= $user['avatar'] ?>" alt="User Avatar" class="w-10 h-10">` : `<image src="/assets/images/logo.png" alt="Logo" class="w-6 h-6 rounded-full">`}
                </div>
                <div class="flex-1">
                    <div class="font-medium text-sm mb-1">
                        ${sender === 'user' ? 'Bạn' : 'Zesty AI'}
                    </div>
                    <div class="prose prose-sm max-w-none">
                        ${formatMessageContent(content)}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        ${formatDate(createdAt)}
                    </div>
                </div>
            </div>
            ${sender === 'ai' ? `
            <div class="ml-11 mt-2 flex items-center space-x-2">
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
            ` : 
            `
            <div class="ml-11 mt-2 flex items-center space-x-2">
                <button class="text-xs text-accent hover:text-indigo-700">
                    <i class="far fa-copy mr-1"></i> Copy
                </button>
            `}
        </div>
    `;

            $('#chatMessages').append(messageHtml);
            scrollToBottom();
        }

        // Hàm định dạng thời gian từ định dạng ISO 8601 sang giờ phút
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
        }

        // Add system message
        function addSystemMessage(type, content) {
            const messageHtml = `
            <div class="py-2 px-4 rounded-md ${type === 'error' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700'}">
                <div class="flex items-center">
                    <i class="${type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-info-circle'} mr-2"></i>
                    <div>${content}</div>
                </div>
            </div>
        `;

            $('#chatMessages').append(messageHtml);
            scrollToBottom();
        }


        // Load chat sessions from server
        function loadChatSessions() {
            $.getJSON('/api/chat.php', function(data) {
                if (data.success && data.sessions) {
                    renderChatSessions(data.sessions);
                }
            });
        }


        // Render chat sessions in sidebar
        function renderChatSessions(sessions) {
            const container = $('.chat-sessions-container');
            container.empty();

            if (sessions.length === 0) {
                container.html('<div class="text-xs text-gray-500 p-2 text-center">Bạn chưa có lịch sử chat</div>');
                return;
            }

            sessions.forEach(session => {
                const isActive = session.session_id === currentSessionId;
                const date = new Date(session.updated_at);
                const formattedDate = date.toLocaleDateString();

                const sessionHtml = `
                <div class="chat-session ${isActive ? 'bg-gray-700' : 'hover:bg-gray-700'} rounded-md mb-1 cursor-pointer transition-colors" 
                     data-session-id="${session.session_id}">
                    <div class="p-2">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-medium truncate">${session.session_name}</div>
                            <div class="text-xs text-gray-400">${formattedDate}</div>
                        </div>
                    </div>
                </div>
            `;

                container.append(sessionHtml);
            });

            // Add click handler to sessions
            $('.chat-session').on('click', function() {
                const sessionId = $(this).data('session-id');
                history.pushState(null, '', `/chat.php?session_id=${sessionId}`);
                loadChatSession(sessionId);
            });
        }

        // Highlight active session
        function highlightActiveSession() {
            $('.chat-session').removeClass('bg-gray-700').addClass('hover:bg-gray-700');
            $(`.chat-session[data-session-id="${currentSessionId}"]`).addClass('bg-gray-700').removeClass('hover:bg-gray-700');
        }

        // Scroll chat to bottom
        function scrollToBottom() {
            const container = document.getElementById('chatMessagesContainer');
            container.scrollTop = container.scrollHeight;
        }

        updateModelInfo();
        highlightActiveSession();
    });
</script>