<?php

require_once 'databases.php';
session_start();

function addtocart($induction, $item_id, $quantity) {
    // Ваши операции по добавлению товара в корзину
    // Здесь вы должны выполнить SQL-запрос для добавления товара в корзину
    // Например, выполните SQL-запрос для добавления записи в таблицу cart

    $user_session_id = $_SESSION['user_id']; // Получаем идентификатор сессии пользователя

    $sql = "INSERT INTO cart (user_session_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($induction, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $user_session_id, $item_id, $quantity);

    if (mysqli_stmt_execute($stmt)) {
        echo "Товар успешно добавлен в корзину";
    } else {
        echo "Ошибка при добавлении товара в корзину: " . mysqli_error($induction);
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['item_id']) && isset($_POST['quantity'])) {
        $item_id = $_POST['item_id'];
        $quantity = $_POST['quantity'];

        // Здесь вы можете выполнить операции добавления товара в корзину
        // Например, выполните SQL-запрос для добавления товара в таблицу cart

        // Ваша функция addtocart
        addtocart($induction, $item_id, $quantity);

        // echo "Товар успешно добавлен в корзину";
    } else {
        echo "Неверные данные";
    }
} else {
    echo "Недопустимый метод запроса";
}
?>