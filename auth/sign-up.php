<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = false;

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $fullname = trim($_POST['fullname']);
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($fullname)) {
        $error = "Tất cả các trường đều bắt buộc";
    } elseif ($password !== $confirm_password) {
        $error = "Mật khẩu không khớp";
    } elseif (strlen($password) < 8) {
        $error = "Mật khẩu phải có ít nhất 8 ký tự";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Định dạng email không hợp lệ";
    } elseif (!isset($_POST['terms'])) {
        $error = "Bạn phải đồng ý với Điều khoản dịch vụ và Chính sách bảo mật";
    } else {
        try {
            if (createUser($username, $email, $fullname, $password)) {
                $success = true;
                // Redirect after 3 seconds
                header("Refresh: 3; URL=/auth/sign-in.php");
            } else {
                $error = 'Đã xảy ra lỗi trong quá trình đăng ký.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'Tên người dùng hoặc email đã tồn tại.';
            } else {
                $error = 'Lỗi cơ sở dữ liệu: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản | Zesty</title>
    <link rel="icon" href="/assets/images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <div class="flex justify-center mb-6">
            <div class="flex items-center">
                <img class="h-10 w-10" src="/assets/images/logo.png" alt="Zesty Logo">
                <h1 class="ml-2 text-2xl font-bold text-blue-600">Zesty AI</h1>
            </div>
        </div>
        
        <h2 class="text-center text-2xl font-extrabold text-gray-900 mb-6">Tạo tài khoản</h2>
        
        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <div>
                    <p class="font-bold">Đăng ký thành công!</p>
                    <p>Tài khoản của bạn đã được tạo. Đang chuyển hướng đến trang đăng nhập...</p>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
        <form id="registerForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-6">
            <div>
                <label for="fullname" class="block text-sm font-medium text-gray-700">Họ và tên</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <input value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" 
                           type="text" 
                           id="fullname" 
                           name="fullname" 
                           class="pl-10 block w-full py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Nhập họ và tên của bạn" 
                           required>
                </div>
            </div>
            
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Tên người dùng</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-at text-gray-400"></i>
                    </div>
                    <input value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                           type="text" 
                           id="username" 
                           name="username" 
                           class="pl-10 block w-full py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Chọn tên người dùng" 
                           required>
                </div>
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Địa chỉ email</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <input value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           type="email" 
                           id="email" 
                           name="email" 
                           class="pl-10 block w-full py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Nhập địa chỉ email của bạn" 
                           required>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" 
                               id="password" 
                               name="password"
                               class="pl-10 pr-10 block w-full py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="Tạo mật khẩu" 
                               required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="toggle-password fas fa-eye text-gray-400 cursor-pointer" data-target="password"></i>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Tối thiểu 8 ký tự</p>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Xác nhận mật khẩu</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               class="pl-10 pr-10 block w-full py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="Xác nhận mật khẩu" 
                               required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="toggle-password fas fa-eye text-gray-400 cursor-pointer" data-target="confirm_password"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms" 
                               name="terms" 
                               type="checkbox" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                               required>
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="font-medium text-gray-700">
                            Tôi đồng ý với <a href="terms.php" class="text-blue-600 hover:text-blue-500">Điều khoản dịch vụ</a> và <a href="privacy.php" class="text-blue-600 hover:text-blue-500">Chính sách bảo mật</a>
                        </label>
                    </div>
                </div>
            </div>
            
            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus"></i>
                    </span>
                    Đăng ký
                </button>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">
                    Đã có tài khoản? <a href="/auth/sign-in.php" class="font-medium text-blue-600 hover:text-blue-500">Đăng nhập</a>
                </p>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <script>
    $(document).ready(function() {
        // Toggle password visibility
        $('.toggle-password').click(function() {
            const targetId = $(this).data('target');
            const input = $('#' + targetId);
            
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
        
        // Password match validation
        $('#confirm_password').on('keyup', function() {
            const password = $('#password').val();
            const confirmPassword = $(this).val();
            
            if (password === confirmPassword) {
                $(this).removeClass('border-red-500').addClass('border-green-500');
            } else {
                $(this).removeClass('border-green-500').addClass('border-red-500');
            }
        });
        
        // Form validation
        $('#registerForm').on('submit', function(e) {
            const password = $('#password').val();
            const confirmPassword = $('#confirm_password').val();
            const isTermsChecked = $('#terms').is(':checked');
            
            // Reset error messages
            $('.error-message').remove();
            
            let hasError = false;
            
            if (password.length < 8) {
                $('#password').after('<p class="text-red-500 text-xs mt-1 error-message">Mật khẩu phải có ít nhất 8 ký tự</p>');
                hasError = true;
            }
            
            if (password !== confirmPassword) {
                $('#confirm_password').after('<p class="text-red-500 text-xs mt-1 error-message">Mật khẩu không khớp</p>');
                hasError = true;
            }
            
            if (!isTermsChecked) {
                $('#terms').parent().parent().after('<p class="text-red-500 text-xs mt-1 error-message">Bạn phải đồng ý với điều khoản</p>');
                hasError = true;
            }
            
            if (hasError) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>