<?php
require_once '../config/constants.php';
$client_id = GOOGLE_CLIENT_ID;
$redirect_uri = urlencode(APP_URL.'/auth/google-callback.php');
$scope = urlencode('https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile');

$auth_url = "https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id={$client_id}&redirect_uri={$redirect_uri}&scope={$scope}&access_type=online";

header('Location: ' . $auth_url);
exit();
