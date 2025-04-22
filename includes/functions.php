<?php

function createUser($username, $email, $fullname, $password)
{
    global $pdo;

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $avatar = 'https://robohash.org/' . urlencode($username);

    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password, fullname, avatar)
        VALUES (:username, :email, :password, :fullname, :avatar)
    ");

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':fullname', $fullname);
    $stmt->bindParam(':avatar', $avatar);

    return $stmt->execute();
}


function verifyLogin($login, $password) {
    global $pdo;
    
    // Check if the login is an email or username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    
    return false;
}