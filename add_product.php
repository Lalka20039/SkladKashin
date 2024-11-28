<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $location = $_POST['location'];

    // Вставляем новый товар в таблицу продуктов
    $stmt = $pdo->prepare('INSERT INTO products (name, quantity, location) VALUES (?, ?, ?)');
    $stmt->execute([$name, $quantity, $location]);
    $productId = $pdo->lastInsertId();  // Получаем ID только что добавленного товара

    // Добавляем запись в таблицу истории изменений
    $stmt = $pdo->prepare('INSERT INTO product_changes (product_id, user_id, action_type, field_changed, old_value, new_value) 
                           VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$productId, $_SESSION['user_id'], 'create', null, null, "Товар создан: $name"]);

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить товар</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Добавить товар</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Название товара</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="form-group">
                <label for="quantity">Количество</label>
                <input type="number" class="form-control" name="quantity" required>
            </div>
            <div class="form-group">
                <label for="location">Местоположение</label>
                <input type="text" class="form-control" name="location" required>
            </div>
            <button type="submit" class="btn btn-primary">Добавить</button>
        </form>
        <a href="index.php" class="btn btn-secondary mt-3">Назад</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
