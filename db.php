<?php
$host = 'localhost';
$dbname = 'inventory';
$username = 'root';  // Измените на свой логин, если нужно
$password = '';  // Измените на свой пароль, если нужно

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
?>
