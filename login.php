<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'db.php';
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверные данные для входа';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Вход в систему</h2>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <?php if (isset($_GET['registered'])) { echo "<div class='alert alert-success'>Вы успешно зарегистрированы. Пожалуйста, войдите.</div>"; } ?>
        <form method="POST" class="mt-4">
            <div class="form-group">
                <label for="username">Логин</label>
                <input type="text" class="form-control" name="username" required placeholder="Введите ваш логин">
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" class="form-control" name="password" required placeholder="Введите ваш пароль">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Войти</button>
        </form>

        <div class="mt-3 text-center">
            <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
