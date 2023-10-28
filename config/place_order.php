<?php
require_once 'databases.php';

if ($_POST) {
    $customerName = $_POST['customerName'];
    $phoneNumber = $_POST['phoneNumber'];
    $customerAddress = $_POST['customerAddress'];
    $orderDetails = $_POST['orderDetails'];
    $totalPrice = $_POST['totalPrice'];

    $sql = "INSERT INTO `AdminOrders` (customer_name, phone_number, customer_address, order_details, total_price) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($induction, $sql);
    mysqli_stmt_bind_param($stmt, "ssssd", $customerName, $phoneNumber, $customerAddress, $orderDetails, $totalPrice);

    if (mysqli_stmt_execute($stmt)) {
        echo "Заказ успешно создан!";
    } else {
        echo "Ошибка при создании заказа: " . mysqli_error($induction);
    }
} else {
    echo "Недопустимый метод запроса";
}
?>