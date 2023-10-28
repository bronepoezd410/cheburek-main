<?php
require_once 'databases.php';
session_start();

if (!isset($_POST['products'])) {
    // there is an error; you might want to handle it
}

$data = $_POST['products'];

// assuming the customer is logged in and we have the id
$customerId = $_SESSION["id"]; 

// create an order
$sqlOrder = "INSERT INTO Orders (customer_id) VALUES ('$customerId')";
if (mysqli_query($induction, $sqlOrder)) {
    $orderId = mysqli_insert_id($induction);
} else {
    echo "Error: " . $sqlOrder . "<br>" . mysqli_error($induction);
}

foreach ($data as $product) {
    $productId = 1; // you need to figure out how to get product id based on product category and name
    $quantity = $product->quantity;
    $subtotal = $product->quantity * $product->price;

    // create an order item
    $sqlOrderItem = "INSERT INTO OrderItems (order_id, item_id, quantity, subtotal) VALUES ('$orderId', '$productId', '$quantity', '$subtotal')";
    if (!mysqli_query($induction, $sqlOrderItem)) {
        echo "Error: " . $sqlOrderItem . "<br>" . mysqli_error($induction);
    }
}
