<?php
session_start();
require_once 'db.php';

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    try {
        // Удаляем все записи из product_changes, связанные с этим товаром
        $stmt = $pdo->prepare('DELETE FROM product_changes WHERE product_id = ?');
        $stmt->execute([$productId]);

        // Удаляем товар из таблицы products
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$productId]);

        // Уведомление об успешном удалении
        $_SESSION['message'] = 'Товар успешно удален!';
    } catch (PDOException $e) {
        // Обработка ошибок
        $_SESSION['error'] = 'Ошибка при удалении товара: ' . $e->getMessage();
    }

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удаление товара</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Удаление товара</h2>
        <div class="alert alert-warning">
            Вы уверены, что хотите удалить товар <strong><?= htmlspecialchars($product['name']) ?></strong>?
        </div>

        <form method="POST">
            <button type="submit" class="btn btn-danger">Удалить</button>
            <a href="index.php" class="btn btn-secondary">Отмена</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
