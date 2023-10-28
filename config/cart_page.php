<?php
require_once 'databases.php';
session_start();

$session_id = $_SESSION['user_id']; 

$sql = "SELECT * FROM cart JOIN MenuItems ON cart.product_id = MenuItems.item_id WHERE user_session_id = ?";
$stmt = mysqli_prepare($induction, $sql);

if($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $session_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    echo "Ошибка: " . mysqli_error($induction);
    exit();
}

$items_in_cart = [];
$total_price = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $items_in_cart[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

// Add order to the database
if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $customer_name = $_SESSION['username']; // assuming the username is added to session
    $phone_number = $_SESSION['phone']; // assuming the phone number is added to session
    $customer_address = $_SESSION['address']; // assuming the address is added to session
    $order_details = json_encode($items_in_cart);
  
    $sql = "INSERT INTO AdminOrders (customer_name, phone_number, customer_address, order_details, total_price, order_date) 
            VALUES (?, ?, ?, ?, ?, now())";
  
    $stmt = mysqli_prepare($induction, $sql);
    mysqli_stmt_bind_param($stmt, "ssssd", $customer_name, $phone_number, $customer_address, $order_details, $total_price);
    mysqli_stmt_execute($stmt);
}

if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST['submit_order'])) {
    $customer_name = $_POST['customer_name'];
    $phone_number = $_POST['phone_number'];
    $customer_address = $_POST['customer_address'];
    $city_dwell = $_POST['city_dwell'];

    if ($city_dwell == 'yes') {
        $total_price += 200;
    } elseif ($city_dwell == 'no')  {
        $total_price += 500;
    }
    else{
        $total_price += 0;
    }
    
    // подробная информация о заказе
    $order_details = json_encode($items_in_cart);
  
    // Вставьте данные из формы в таблицу бд
    $sql = "INSERT INTO AdminOrders (customer_name, phone_number, customer_address, order_details, total_price) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($induction, $sql);
    
    if($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssd", $customer_name, $phone_number, $customer_address, $order_details, $total_price);
        mysqli_stmt_execute($stmt);
    } else {
        echo "Ошибка: " . mysqli_error($induction);
        exit();
    }
}

  if($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action']) && ($_POST['action'] == 'increment' || $_POST['action'] == 'decrement'))) {
    $product_id = $_POST['product_id'];
    $action = $_POST['action'];
    
    // Найти товар в корзине
    foreach($items_in_cart as $key => $item) {
        if($item['item_id'] == $product_id) {
            if($action == 'increment') {
                $items_in_cart[$key]['quantity'] += 1;
                // Обновите количество в базе данных
            } else if($action == 'decrement') {
                // Если количество до уменьшения больше 1, уменьшите его на 1
                if($items_in_cart[$key]['quantity'] > 1) {
                    $items_in_cart[$key]['quantity'] -= 1;
                    // Обновите количество в базе данных
                } 
                // Если количество до уменьшения равно 1, удалите товар из корзины
                else {
                    unset($items_in_cart[$key]);
                    // Удалите товар из базы данных
                }
                    
            }
            break;
        }
    }
    // Перезагрузите страницу (или перенаправьте на ту же страницу), чтобы обновить отображаемые данные
    header("Location: {$_SERVER['REQUEST_URI']}");
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazin - Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue&display=swap" rel="stylesheet">
    <link rel="icon" href="images/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto:wght@100;300;400;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/cart_page.css">
</head>

<body>

<h2>Корзина</h2>

<?php 
if(count($items_in_cart) > 0): ?>

    <table>
        <tr>
            <th>Товар</th>
            <th>Цена</th>
            <th>Количество</th>
            <th>Итого</th>
        </tr>

        <?php foreach($items_in_cart as $key => $value): ?>

        <tr>
            <td><?php echo $value['item_name']; ?></td>
            <td><?php echo $value['price'] ?>₽</td>
            <td><?php echo $value['quantity']; ?></td>
            <td><?php echo $value['price'] * $value['quantity']; ?>₽</td>
        </tr>
        

        <?php endforeach; ?>    
        

        <tr>
            <td colspan="3" style="text-align: right;"><b>Общая сумма:</b></td>
            <td><b><?php echo $total_price; ?>₽</b></td>
        </tr>
        


        

        

    </table>
    <p style="color: #ff0000; font-size: 18px; font-weight: bold;">Примечание. Учтите, что цена доставки в городе 200 рублей, а за его пределами 500 рублей.</p>

<?php else: ?>

<p>Корзина пуста</p>

<?php 

endif; 
?>

<form method="POST" action="">
    <h2>Информация о заказчике</h2>
    <input type="text" name="customer_name" placeholder="Ваше имя" required>
    <input type="text" name="phone_number" placeholder="Ваш телефон" required>
    <input type="text" name="customer_address" placeholder="Ваш адрес" required>
    
    <label>Вы проживаете в городе?</label>
    <select name="city_dwell" id="city_dwell" required>
        <option value="" selected disabled>Выберите...</option>
        <option value="yes">Да</option>
        <option value="no">Нет</option>
    </select>

    <input type="submit" name="submit_order" value="Оформить заказ">
</form>
</body>
</html>