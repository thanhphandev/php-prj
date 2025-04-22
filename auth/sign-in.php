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
            // Set session variables
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'fullname' => $user['fullname'],
                'avatar' => $user['avatar']
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
    <title>Đăng nhập | Zesty</title>
    <link rel="icon" href="/assets/images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-background min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <div class="flex justify-center mb-6">
            <div class="flex items-center">
                <img class="h-10 w-10" src="/assets/images/logo.png" alt="Zesty Logo">
                <h1 class="ml-2 text-2xl font-bold text-primary">Zesty AI</h1>
            </div>
        </div>

        <h2 class="text-center text-2xl font-extrabold text-primary mb-6">Đăng nhập</h2>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form id="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-6">
            <div class="flex flex-col gap-5">

                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <img src="/assets/images/google.png" class="w-5 h-5 mr-2" alt="Google Logo"/>
                            Tiếp tục với Google
                        </a>
                    </div>
                    <!-- <div>
                        <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <img src="/assets/images/github.png" class="w-5 h-5 mr-2" alt="Github Logo"/>
                            Github
                        </a>
                    </div> -->

                </div>
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">
                            Hoặc đăng nhập bằng
                        </span>
                    </div>
                </div>
            </div>
            <div class="">
                <label for="login" class="block text-sm font-medium text-gray-700">Tên đăng nhập hoặc Email</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <input value="<?php echo htmlspecialchars($login); ?>"
                        type="text"
                        id="login"
                        name="login"
                        class="pl-10 block w-full py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Nhập tên đăng nhập hoặc email"
                        required
                        autocomplete="username">
                </div>
            </div>

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
                        placeholder="Nhập mật khẩu"
                        required
                        autocomplete="current-password">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i class="toggle-password fas fa-eye text-gray-400 cursor-pointer"></i>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end">

                <div class="text-sm">
                    <a href="forgot-password.php" class="font-medium text-blue-600 hover:text-blue-500">
                        Quên mật khẩu?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt"></i>
                    </span>
                    Đăng nhập
                </button>
            </div>

            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">
                    Chưa có tài khoản? <a href="/auth/sign-up.php" class="font-medium text-blue-600 hover:text-blue-500">Đăng ký ngay</a>
                </p>
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

            // Form validation
            $('#loginForm').on('submit', function(e) {
                const login = $('#login').val().trim();
                const password = $('#password').val();

                // Reset error messages
                $('.error-message').remove();

                let hasError = false;

                if (login === '') {
                    $('#login').after('<p class="text-red-500 text-xs mt-1 error-message">Vui lòng nhập tên đăng nhập hoặc email</p>');
                    hasError = true;
                }

                if (password === '') {
                    $('#password').after('<p class="text-red-500 text-xs mt-1 error-message">Vui lòng nhập mật khẩu</p>');
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