<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$login = '';

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($login) || empty($password)) {
        $error = "Vui lòng nhập tên đăng nhập/email và mật khẩu";
    } else {
        $user = verifyLogin($login, $password);

        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'fullname' => $user['fullname'],
                'avatar' => $user['avatar'],
                'email' => $user['email'],
                'role' => $user['role'],
            ];

            header("Location: /index.php");
            exit();
        } else {
            $error = "Tên đăng nhập/email hoặc mật khẩu không chính xác";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập tài khoản | Zesty</title>
    <link rel="icon" href="/assets/images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-background min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-2">
            <img src="/assets/images/logo.png" class="mx-auto h-12 w-12" alt="Logo">
            <h2 class="mt-4 text-2xl font-bold text-primary">Đăng nhập vào Zesty</h2>
            <p class="text-sm text-gray-500">Chào mừng bạn quay lại!</p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form id="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-6">

            <!-- Google login -->
            <div class="flex flex-col space-y-4">
                <button type="button"
                    class="flex items-center justify-center gap-3 w-full px-4 py-3 border border-gray-200 rounded-lg bg-white text-sm text-gray-700 hover:bg-gray-50 transition">
                    <img src="/assets/images/google.png" alt="Google Logo" class="w-5 h-5" />
                    <span class="font-semibold">Tiếp tục với Google</span>
                </button>
                <div class="relative flex items-center justify-center">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <div class="mx-4 text-sm text-gray-500">hoặc đăng nhập bằng tài khoản</div>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>
            </div>

            <!-- Tên đăng nhập/email -->
            <div class="space-y-1">
                <label for="login" class="block text-sm font-medium text-gray-700">Tên đăng nhập hoặc email</label>
                <input type="text" id="login" name="login" required autocomplete="username"
                    value="<?php echo htmlspecialchars($login); ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm"
                    placeholder="Nhập tên đăng nhập hoặc email">
            </div>

            <!-- Mật khẩu -->
            <div class="space-y-1">
                <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        class="w-full px-4 py-2 pr-11 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm"
                        placeholder="Nhập mật khẩu">
                    <div class="absolute inset-y-0 right-3 flex items-center">
                        <i class="toggle-password fas fa-eye text-gray-400 cursor-pointer"></i>
                    </div>
                </div>
            </div>


            <!-- Quên mật khẩu -->
            <div class="flex justify-end text-sm">
                <a href="forgot-password.php" class="text-blue-600 hover:underline">Quên mật khẩu?</a>
            </div>

            <!-- Submit -->
            <div>
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 py-2 px-4 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    <i class="fas fa-sign-in-alt"></i>
                    Đăng nhập
                </button>
            </div>

            <!-- Chưa có tài khoản -->
            <div class="text-center text-sm text-gray-600">
                Bạn chưa có tài khoản?
                <a href="/auth/sign-up.php" class="text-blue-600 hover:underline font-medium">Đăng ký ngay</a>
            </div>

        </form>

    </div>

    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('.toggle-password').click(function() {
                const input = $('#password');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    $(this).removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    $(this).removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            $('#loginForm').on('submit', function(e) {
                const login = $('#login').val().trim();
                const password = $('#password').val();

                // Remove previous error messages
                $('.error-message').remove();

                let hasError = false;

                if (login === '') {
                    $('#login').addClass('border-red-500').after('<p class="text-red-500 text-xs mt-1 error-message">Vui lòng nhập tên đăng nhập hoặc email</p>');
                    hasError = true;
                } else {
                    $('#login').removeClass('border-red-500');
                }

                if (password === '') {
                    $('#password').addClass('border-red-500').after('<p class="text-red-500 text-xs mt-1 error-message">Vui lòng nhập mật khẩu</p>');
                    hasError = true;
                } else {
                    $('#password').removeClass('border-red-500');
                }

                if (hasError) {
                    e.preventDefault();
                } else {
                    $('button[type="submit"]').html('<i class="fas fa-spinner fa-spin mr-2"></i> Đang xử lý...');
                }
            });
        });
    </script>
</body>

</html>