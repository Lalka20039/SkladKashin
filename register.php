<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Присваиваем роль 'viewer' по умолчанию
    $role = 'viewer';

    // Сохраняем пользователя в базе данных
    $stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
    $stmt->execute([$username, $password, $role]);

    // Уведомление о успешной регистрации
    $_SESSION['message'] = 'Вы успешно зарегистрированы! Ваша роль - viewer. Для изменения роли на администратор обратитесь к руководству.';

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Регистрация</h2>
        <form method="POST" class="mt-4">
            <div class="form-group">
                <label for="username">Логин</label>
                <input type="text" class="form-control" id="username" name="username" required placeholder="Введите ваш логин">
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Введите ваш пароль">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Зарегистрироваться</button>
        </form>
        <div class="text-center mt-3">
            <a href="login.php" class="btn btn-link">Уже есть аккаунт? Войти</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
