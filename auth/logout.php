<?php
session_start();
session_unset();
session_destroy();

// Chuyển hướng về trang đăng nhập (hoặc trang chủ)
header("Location: /auth/sign-in.php");
exit;
