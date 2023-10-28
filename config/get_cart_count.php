<?php
session_start();

require_once 'databases.php';

// Запрос к базе данных для получения количества товаров в корзине (замените на свой SQL-запрос)
$user_session_id = $_SESSION['user_id']; // Получаем идентификатор сессии пользователя

$sql = "SELECT COUNT(*) AS cart_count FROM cart WHERE user_session_id = '$user_session_id'";
$result = mysqli_query($induction, $sql);

if (!$result) {
    die("Ошибка выполнения SQL-запроса: " . mysqli_error($induction));
}

$row = mysqli_fetch_assoc($result);
$cart_count = $row['cart_count'];

// Возвращаем количество товаров в корзине
echo $cart_count;

// Закрываем соединение с базой данных
mysqli_close($induction);
?>