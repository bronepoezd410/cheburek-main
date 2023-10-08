<?php
// Подключение к базе данных
require_once 'databases.php';

// Проверка, является ли запрос POST-запросом
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из тела запроса
    $requestData = json_decode(file_get_contents("php://input"));

    // Получаем данные из запроса
    
    $itemId = $requestData->itemId;
    $itemName = $requestData->itemName;
    $itemPrice = $requestData->itemPrice;
    $categoryId = $requestData->categoryId;

    // Обновляем информацию о товаре в базе данных, включая категорию
    $sql = "UPDATE MenuItems SET item_name = ?, price = ?, category_id = ? WHERE item_id = ?";
    $stmt = mysqli_prepare($induction, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $itemName, $itemPrice, $categoryId, $itemId);
    
    if (mysqli_stmt_execute($stmt)) {
        // Возвращаем успешный результат
        echo json_encode(['success' => true]);
    } else {
        // Возвращаем ошибку, если обновление не удалось
        echo json_encode(['error' => 'Error updating item']);
    }

    // Закрываем подготовленное выражение
    mysqli_stmt_close($stmt);
} else {
    // Возвращаем ошибку, если запрос не является POST-запросом
    echo json_encode(['error' => 'Invalid request method']);
}
?>