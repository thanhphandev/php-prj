<?php

$host = 'localhost';
$dbname = 'aichat_db';
$username = 'root';
$password = '';


try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); //Khi gọi fetch(), kết quả sẽ trả về mảng liên kết (dạng [cột => giá trị]), dễ xử lý hơn dạng mảng số.

}catch(PDOException $e){
    die("Kết nối thất bại: " . $e->getMessage());
}