<?php
// Подключение к базе данных
require_once 'databases.php';

// Проверка, что пришел GET-запрос с параметром "itemId"
if (isset($_GET['itemId'])) {
    // Получение ID товара из GET-запроса
    $itemId = $_GET['itemId'];

    // Запрос к базе данных для получения данных о товаре по ID
    $sql = "SELECT * FROM `MenuItems` WHERE `item_id` = ?";
    
    // Подготовка SQL-запроса с использованием подготовленных выражений для безопасности
    if ($stmt = mysqli_prepare($induction, $sql)) {
        // Привязываем значение параметра к запросу
        mysqli_stmt_bind_param($stmt, 'i', $itemId);
        
        // Выполняем запрос
        if (mysqli_stmt_execute($stmt)) {
            // Получаем результат запроса
            $result = mysqli_stmt_get_result($stmt);

            // Проверяем, есть ли результат
            if ($row = mysqli_fetch_assoc($result)) {
                // Отправляем данные о товаре в формате JSON
                echo json_encode($row);
            } else {
                echo json_encode(['error' => 'Товар не найден']);
            }
        } else {
            echo json_encode(['error' => 'Ошибка выполнения запроса']);
        }

        // Закрываем подготовленный запрос
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['error' => 'Ошибка подготовки запроса']);
    }
} else {
    echo json_encode(['error' => 'Отсутствует параметр "itemId" в запросе']);
}
?>