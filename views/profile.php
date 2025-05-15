<?php
$user_id = $_SESSION['user']['id'] ?? null;
if (!$user_id) {
    session_destroy();
    header('Location: /auth/sign-in.php');
    exit;
}

$user = getUserById($user_id);
if (!$user) {
    session_destroy();
    header('Location: login.php?error=user_not_found');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];
    $password_changed = false;

    if (empty($fullname)) {
        $errors[] = "Họ và tên không được để trống";
    }

    if (empty($email)) {
        $errors[] = "Email không được để trống";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id != :user_id");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Email này đã được sử dụng bởi tài khoản khác";
        }
    }

    // Kiểm tra mật khẩu nếu người dùng muốn thay đổi
    if (!empty($new_password) || !empty($confirm_password) || !empty($current_password)) {
        $password_changed = true;
        $isLoginBySocial = empty($user['password']);
        if ($isLoginBySocial) {
            $errors[] = "Bạn không thể thay đổi mật khẩu vì bạn đã đăng nhập bằng tài khoản mạng xã hội";
        }
        if (empty($current_password)) {
            $errors[] = "Vui lòng nhập mật khẩu hiện tại để thay đổi mật khẩu";
        } elseif (!password_verify($current_password, $user['password']) && !$isLoginBySocial) {
            $errors[] = "Mật khẩu hiện tại không chính xác";
        }

        if (empty($new_password)) {
            $errors[] = "Vui lòng nhập mật khẩu mới";
        } elseif (strlen($new_password) < 8) {
            $errors[] = "Mật khẩu mới phải có ít nhất 8 ký tự";
        }

        if (empty($confirm_password)) {
            $errors[] = "Vui lòng xác nhận mật khẩu mới";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "Mật khẩu xác nhận không khớp với mật khẩu mới";
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            if ($password_changed) {
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET fullname = :fullname, email = :email, password = :password WHERE id = :user_id");
                $stmt->bindParam(':password', $hashedPassword);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET fullname = :fullname, email = :email WHERE id = :user_id");
            }

            $stmt->bindParam(':fullname', $fullname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $pdo->commit();

            // Cập nhật session
            $_SESSION['user']['fullname'] = $fullname;
            $_SESSION['user']['email'] = $email;

            $message = "Thông tin cá nhân đã được cập nhật thành công!";
            $message_type = "success";

            // Cập nhật dữ liệu hiển thị mới nhất
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Đã xảy ra lỗi: " . $e->getMessage();
            $message_type = "error";

            error_log("Lỗi cập nhật thông tin người dùng: " . $e->getMessage());
        }
    } else {
        $message = implode("<br>", $errors);
        $message_type = "error";
    }
}
?>

<div class="max-w-6xl mx-auto px-6 py-8">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Thông tin cá nhân</h2>
        <p class="text-gray-600 mt-2">Cập nhật thông tin cá nhân và mật khẩu của bạn</p>
    </div>

    <?php if (isset($message)): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-100 text-green-700 border-l-4 border-green-500' : 'bg-red-100 text-red-700 border-l-4 border-red-500'; ?> flex items-center">
            <div class="mr-3">
                <?php if ($message_type === 'success'): ?>
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

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Thông tin cơ bản -->
                <div class="md:col-span-2">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Thông tin cơ bản</h3>
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly
                        class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-500 cursor-not-allowed">
                    <p class="text-sm text-gray-500 mt-1">Tên đăng nhập không thể thay đổi</p>
                </div>

                <div>
                    <label for="fullname" class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                    <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Thay đổi mật khẩu -->
                <div class="md:col-span-2 mt-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Thay đổi mật khẩu</h3>
                    <p class="text-sm text-gray-500 mb-4">Để trống các trường này nếu bạn không muốn thay đổi mật khẩu</p>
                </div>

                <div class="md:col-span-2">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu hiện tại</label>
                    <div class="relative">
                        <input type="password" id="current_password" name="current_password"
                            class="w-full px-4 py-2 pr-11 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute inset-y-0 right-3 flex items-center">
                            <button type="button" class="toggle-password text-gray-400 focus:outline-none" data-target="current_password">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
                    <div class="relative">
                        <input type="password" id="new_password" name="new_password" minlength="8"
                            class="w-full px-4 py-2 pr-11 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute inset-y-0 right-3 flex items-center">
                            <button type="button" class="toggle-password text-gray-400 focus:outline-none" data-target="new_password">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Tối thiểu 8 ký tự</p>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu mới</label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="confirm_password" minlength="8"
                            class="w-full px-4 py-2 pr-11 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute inset-y-0 right-3 flex items-center">
                            <button type="button" class="toggle-password text-gray-400 focus:outline-none" data-target="confirm_password">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" name="update_profile"
                    class="w-full bg-primary text-white py-3 rounded-lg font-medium hover:bg-primary transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Cập nhật thông tin
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.toggle-password').on('click', function() {
            const targetId = $(this).data('target');
            const $inputField = $('#' + targetId);

            if ($inputField.attr('type') === 'password') {
                $inputField.attr('type', 'text');
                $(this).html(`
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
            `);
            } else {
                $inputField.attr('type', 'password');
                $(this).html(`
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            `);
            }
        });

        const $newPasswordInput = $('#new_password');
        const $confirmPasswordInput = $('#confirm_password');

        function validatePasswordMatch() {
            const newVal = $newPasswordInput.val();
            const confirmVal = $confirmPasswordInput.val();

            if (newVal && confirmVal && newVal !== confirmVal) {
                $confirmPasswordInput[0].setCustomValidity('Mật khẩu xác nhận không khớp với mật khẩu mới');
            } else {
                $confirmPasswordInput[0].setCustomValidity('');
            }
        }

        $newPasswordInput.on('input', validatePasswordMatch);
        $confirmPasswordInput.on('input', validatePasswordMatch);
    });
</script>