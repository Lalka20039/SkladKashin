<?php
session_start();
require_once 'db.php';

// Получаем все товары из базы данных
$stmt = $pdo->query('SELECT * FROM products');
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Склад</title>
    <!-- Подключение Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table th, .table td {
            text-align: center;
        }
        .product-actions {
            display: flex;
            justify-content: center;
        }
        .alert-box {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Сообщение о успешной регистрации с ролью "viewer" -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info alert-box" role="alert">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <h2 class="text-center mb-4">Список товаров на складе</h2>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Название</th>
                    <th>Количество</th>
                    <th>Местоположение</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($product['location']); ?></td>
                        <td class="product-actions">
                            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'viewer'): ?>
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm mr-2">Редактировать</a>
                                <!-- Кнопка для просмотра истории изменений -->
                                <a href="view_history.php?product_id=<?php echo $product['id']; ?>" class="btn btn-info btn-sm mr-2">История изменений</a>
                            <?php endif; ?>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm">Удалить</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Кнопка для добавления товара доступна только для админа -->
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <div class="text-center mt-3">
                <a href="add_product.php" class="btn btn-primary">Добавить товар</a>
            </div>
        <?php endif; ?>

        <!-- Кнопка выхода -->
        <div class="text-center mt-3">
            <a href="logout.php" class="btn btn-secondary">Выйти</a>
        </div>
    </div>

    <!-- Подключение Bootstrap JS и jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
