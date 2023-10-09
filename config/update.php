<?php
// Подключение к базе данных
require_once 'databases.php';

// Функция для сохранения изображения
function saveItemImage($itemId, $itemImage)
{
    // Здесь ваш код для сохранения изображения
    // Например, вы можете использовать move_uploaded_file для сохранения файла
    $uploadDir = 'images';
    $imageName = $itemId . '_' . $itemImage->name;
    $targetFilePath = $uploadDir . $imageName;
    if (move_uploaded_file($itemImage->tmp_name, $targetFilePath)) {
        return $targetFilePath; // Возвращаем путь к сохраненному изображению
    } else {
        error_log('Error saving image'); // Ошибка сохранения изображения
        return false; // Возвращаем false в случае ошибки сохранения
    }
}

// Проверка, является ли запрос POST-запросом
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из тела запроса
    $requestData = json_decode(file_get_contents("php://input"));
    // Получаем данные из запроса
    $itemId = $requestData->itemId;
    $itemName = $requestData->itemName;
    $itemPrice = $requestData->itemPrice;
    $categoryId = $requestData->categoryId;

    // Обработка изображения, если оно передано
    if (isset($requestData->itemImage)) {
        $itemImage = $requestData->itemImage;
        error_log("requestData: " . print_r($requestData, true));
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
                error_log('Error updating item'); // Ошибка при обновлении
                echo json_encode(['error' => 'Error updating item']);
            }

            // Закрываем подготовленное выражение
            mysqli_stmt_close($stmt);
        } else {
            error_log('Error saving image'); // Ошибка сохранения изображения
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
            error_log('Error updating item'); // Ошибка при обновлении
            echo json_encode(['error' => 'Error updating item']);
        }

        // Закрываем подготовленное выражение
        mysqli_stmt_close($stmt);
    }
} else {
    error_log('Invalid request method'); // Ошибка недопустимого метода запроса
    // Возвращаем ошибку, если запрос не является POST-запросом
    echo json_encode(['error' => 'Invalid request method']);
}
?>