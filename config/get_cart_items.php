<?php
// Подключение к базе данных
require_once 'databases.php';
session_start();
// Получение идентификатора сессии пользователя
$user_session_id = $_SESSION['user_id'];


// Запрос к базе данных для получения товаров в корзине
$sql = "SELECT MenuItems.item_name, cart.quantity FROM cart
        JOIN MenuItems ON cart.product_id = MenuItems.item_id
        WHERE cart.user_session_id = '$user_session_id'";

$result = mysqli_query($induction, $sql);

if (!$result) {
    die("Ошибка выполнения SQL-запроса: " . mysqli_error($induction));
}

$cart_items = array();

while ($row = mysqli_fetch_assoc($result)) {
    $cart_items[] = array(
        'item_name' => $row['item_name'],
        'quantity' => $row['quantity'],
        'image_url' => $row['image_url'], // Добавление URL изображения

    );
}

// Закрытие соединения с базой данных
mysqli_close($induction);

// Отправка данных в формате JSON
header('Content-Type: application/json');
echo json_encode($cart_items);
?>