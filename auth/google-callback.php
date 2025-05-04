<?php
session_start();
require_once '../config/database.php';
require_once '../config/constants.php';

$client_id = GOOGLE_CLIENT_ID;
$client_secret = GOOGLE_SECRET_ID;
$redirect_uri = APP_URL . '/auth/google-callback.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // 1. Lấy access token
    $response = file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded",
            'content' => http_build_query([
                'code' => $code,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
                'grant_type' => 'authorization_code'
            ])
        ]
    ]));

    $data = json_decode($response, true);
    $access_token = $data['access_token'] ?? null;

    if ($access_token) {
        // 2. Lấy thông tin người dùng
        $user_info = file_get_contents("https://www.googleapis.com/oauth2/v2/userinfo?access_token=$access_token");
        $google_user = json_decode($user_info, true);

        $email = $google_user['email'];
        $fullname = $google_user['name'];
        $avatar = $google_user['picture'];

        // 3. Kiểm tra người dùng trong DB
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Nếu user đã có và có mật khẩu => không cho login qua Google
            if (!empty($user['password'])) {
                header("Location: /auth/sign-in.php?error=google_account_exists_with_password");
                exit();
            }
        } else {
            // Tạo mới user
            $username = explode('@', $email)[0];
            $stmt = $pdo->prepare("INSERT INTO users (username, email, fullname, avatar, password) VALUES (?, ?, ?, ?, '')");
            $stmt->execute([$username, $email, $fullname, $avatar]);
            $user_id = $pdo->lastInsertId();
            $user = [
                'id' => $user_id,
                'username' => $username,
                'fullname' => $fullname,
                'avatar' => $avatar,
                'email' => $email,
                'role' => 'user'
            ];
        }

        // 4. Đăng nhập
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
    }
}

// Nếu thất bại
header("Location: /auth/sign-in.php?error=google_login_failed");
exit();
