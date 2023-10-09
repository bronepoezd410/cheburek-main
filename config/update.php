<?php
// Подключение к базе данных
require_once 'databases.php';

// Функция для сохранения изображения
function saveItemImage($itemId, $itemImage)
{
    // Здесь ваш код для сохранения изображения
    // Например, вы можете использовать move_uploaded_file для сохранения файла
    
    $uploadDir = '../images/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Создать папку, если она не существует
    }
    $imageName = $itemId . '_' . basename($itemImage['name']);
    $targetFilePath = $uploadDir . $imageName;

    if (move_uploaded_file($itemImage['tmp_name'], $targetFilePath)) {
        return $targetFilePath; // Возвращаем путь к сохраненному изображению
    } else {
        // Добавьте отладочный вывод для вывода сообщения об ошибке
        error_log("Error saving image: Failed to move the uploaded file");
        return false; // Возвращаем false в случае ошибки сохранения
    }
}

// Проверка, является ли запрос POST-запросом
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы
    $itemId = $_POST['itemId'];
    $itemName = $_POST['itemName'];
    $itemPrice = $_POST['itemPrice'];
    $categoryId = $_POST['categoryId'];

    // Обработка изображения, если оно передано
    if (isset($_FILES['itemImage'])) {
        $itemImage = $_FILES['itemImage'];

        // Сохраняем изображение и получаем путь к нему
        $imagePath = saveItemImage($itemId, $itemImage);

        if ($imagePath) {
            // Если изображение успешно сохранено, обновляем информацию о товаре в базе данных
            $sql = "UPDATE MenuItems SET item_name = ?, price = ?, category_id = ?, image_url = ? WHERE item_id = ?";
            $stmt = mysqli_prepare($induction, $sql);
            mysqli_stmt_bind_param($stmt, "sssss", $itemName, $itemPrice, $categoryId, $imagePath, $itemId);

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
            // Добавьте отладочный вывод для вывода сообщения об ошибке
            error_log("Error saving image: Image was not saved successfully");
            // Возвращаем ошибку, если сохранение изображения не удалось
            echo json_encode(['error' => 'Error saving image']);
        }
    } else {
        // Если изображение не передано, обновляем информацию о товаре без изображения
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
    }
} else {
    // Возвращаем ошибку, если запрос не является POST-запросом
    echo json_encode(['error' => 'Invalid request method']);
}
?>