<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

require_once 'db.php';

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Получаем данные товара из базы
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        echo "Товар не найден.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Обработка формы редактирования
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $location = $_POST['location'];

    // Записываем изменения в историю
    if ($product['quantity'] != $quantity) {
        $stmt = $pdo->prepare('INSERT INTO product_changes (product_id, user_id, action_type, field_changed, old_value, new_value) 
                               VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$productId, $_SESSION['user_id'], 'update', 'quantity', $product['quantity'], $quantity]);
    }

    if ($product['location'] != $location) {
        $stmt = $pdo->prepare('INSERT INTO product_changes (product_id, user_id, action_type, field_changed, old_value, new_value) 
                               VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$productId, $_SESSION['user_id'], 'update', 'location', $product['location'], $location]);
    }

    // Обновляем товар в базе
    $stmt = $pdo->prepare('UPDATE products SET name = ?, quantity = ?, location = ? WHERE id = ?');
    $stmt->execute([$name, $quantity, $location, $productId]);

    // Записываем, что товар был обновлен
    $stmt = $pdo->prepare('INSERT INTO product_changes (product_id, user_id, action_type, field_changed, old_value, new_value) 
                           VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$productId, $_SESSION['user_id'], 'update', 'product', json_encode($product), json_encode(['name' => $name, 'quantity' => $quantity, 'location' => $location])]);

    header('Location: index.php');
    exit;
}
?>

<!-- HTML форма редактирования товара, как выше -->


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование товара</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Редактирование товара</h2>

        <form method="POST">
            <div class="form-group">
                <label for="name">Название товара</label>
                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="quantity">Количество</label>
                <input type="number" class="form-control" name="quantity" value="<?= htmlspecialchars($product['quantity']) ?>" required>
            </div>
            <div class="form-group">
                <label for="location">Местоположение</label>
                <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($product['location']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Обновить</button>
        </form>

        <a href="index.php" class="btn btn-secondary mt-3">Назад</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
