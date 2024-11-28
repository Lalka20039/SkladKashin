<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'viewer' && $_SESSION['role'] != 'admin')) {
    header('Location: index.php');
    exit;
}

require_once 'db.php';

if (isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];

    // Получаем историю изменений для указанного товара
    $stmt = $pdo->prepare('SELECT product_changes.*, users.username FROM product_changes 
                           JOIN users ON product_changes.user_id = users.id WHERE product_id = ? ORDER BY created_at DESC');
    $stmt->execute([$productId]);
    $history = $stmt->fetchAll();
} else {
    echo "Не указан товар.";
    exit;
}

// Массив с переводом действий на русский язык
$actions = [
    'create' => 'Создание',
    'update' => 'Обновление',
    'delete' => 'Удаление'
];

// Массив для перевода полей в JSON на русский язык
$fields = [
    'product' => 'Продукт',  // Переводим "product" на русский
    'name' => 'Название',
    'quantity' => 'Количество',
    'location' => 'Местоположение'
];

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>История изменений товара</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>История изменений товара</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Пользователь</th>
                    <th>Действие</th>
                    <th>Измененное поле</th>
                    <th>Старое значение</th>
                    <th>Новое значение</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $change): ?>
                    <tr>
                        <!-- Форматируем дату для лучшего отображения -->
                        <td><?php echo date('d.m.Y H:i', strtotime($change['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($change['username']); ?></td>

                        <!-- Используем массив для перевода действия на русский -->
                        <td><?php echo isset($actions[$change['action_type']]) ? $actions[$change['action_type']] : $change['action_type']; ?></td>
                        
                        <!-- Переводим поле на русский -->
                        <td><?php echo isset($fields[$change['field_changed']]) ? $fields[$change['field_changed']] : $change['field_changed']; ?></td>

                        <!-- Обрабатываем старое и новое значение -->
                        <td><?php echo htmlspecialchars(format_value($change['field_changed'], $change['old_value'])); ?></td>
                        <td><?php echo htmlspecialchars(format_value($change['field_changed'], $change['new_value'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-secondary mt-3">Назад</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Функция для обработки значений
function format_value($field, $value) {
    // Если поле name, то выводим только старое или новое имя
    if ($field == 'name') {
        return htmlspecialchars($value); // Возвращаем только название
    }

    // Если значение - это JSON, обрабатываем его
    if (is_json($value)) {
        $decoded = json_decode($value, true);

        // Преобразуем числовые индексы в русские ключи
        if (is_array($decoded)) {
            foreach ($decoded as $key => $val) {
                switch ($key) {
                    case 0:
                        $decoded['name'] = $val;
                        unset($decoded[0]);
                        break;
                    case 1:
                        $decoded['quantity'] = $val;
                        unset($decoded[1]);
                        break;
                    case 2:
                        $decoded['location'] = $val;
                        unset($decoded[2]);
                        break;
                    default:
                        break;
                }
            }
        }
        return json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    // Если это не JSON, возвращаем значение как есть
    return htmlspecialchars($value);
}

// Функция для проверки, является ли строка валидным JSON
function is_json($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}
?>
