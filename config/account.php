<?php require_once 'databases.php';
session_start();

if (!isset($_SESSION["login_sess"])) {
  header("location:login.php");
}

$email = $_SESSION["login_email"];
$id = $_SESSION["id"];
$findresult = mysqli_query($induction, "SELECT * FROM customers WHERE `customer_id` = $id");
$ordersResult = mysqli_query($induction, "SELECT * FROM AdminOrders ORDER BY order_date DESC");

if ($id == 1){
  $_SESSION["admink"] = true;
}


if ($res = mysqli_fetch_array($findresult)) {
  $name = $res['first_name'];
  $secondname= $res['last_name'];
  $email = $res['email'];
}
?>
<!DOCTYPE html>
<html>

<head>
  <title> Мой аккаунт</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../styles/styles.css" rel="stylesheet" />
  <link rel="icon" href="images/favicon.png" type="image/x-icon">
  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
  <link href="styles/styles.css" rel="stylesheet" />
</head>

<body>


  <div class="container">
    <div class="row">
      <div class="col-sm-3">

      </div>
      <div class="col-sm-6">
        <div class="login_form">
          <p><a href="/"><img src="/images/zagruzheno.png" width="300" height="3  00" alt="logo" class="logo img-fluid"></a></p>
          <div class="row">
            <div class="col"></div>
            <div class="col-6">
              <?php if (isset($_GET['profile_updated'])) { ?>
                <div class="successmsg">Профиль сохранен ..</div>
              <?php } ?>
              <?php if (isset($_GET['password_updated'])) { ?>
                <div class="successmsg">Пароль был изменен...</div>
              <?php } ?>
              <center>

                <p> Добро пожаловать! <br> <b style="color:black"><?php echo $name . ' ' . $secondname; ?> 
                <?php if ($_SESSION["admink"]) { ?>
                <div >У вас есть права администратора и вы можете изменять меню на главной странице сайта.</div>
              <?php } ?></b> </p>
              </center>
            </div>
            <div class="col">
              <p><a href="logout.php"><span style="color:red;">Выйти</span> </a></p>
            </div>
          </div>

          <table class="table">
            <tr>
              <th>Идентификационный номер: </th>
              <td><?php echo $id; ?></td>
            </tr>
            <tr>
              <th>Имя Фамилия </th>
              <td><?php echo $name . ' '. $secondname ?></td>
            </tr>

          </table>
          <div class="row">
            <div class="col-sm-2">
            </div>
          </div>
        </div>
        <div class="col-sm-3">
        </div>
      </div>
    </div>

    <div class="orders_section">
    <h2>Заказы: <a href="clear_orders.php" class="btn btn-danger" onClick="return confirm('Вы уверены что хотите удалить все заказы?')">Очистить</a></h2>
  <table class="table">
    <thead>
      <tr>
        <th>Номер заказа</th>
        <th>Имя клиента</th>
        <th>Телефон</th>
        <th>Детали заказа</th>
        <th>Общая цена</th>
        <th>Дата и время заказа</th>
        <th>Адрес клиента</th>
      </tr>
    </thead>
    <tbody>
    <?php 
while($order = mysqli_fetch_array($ordersResult)) {
  echo '<tr>';
  echo '<td>' . $order['order_id'] . '</td>';
  echo '<td>' . $order['customer_name'] . '</td>';
  echo '<td>' . $order['phone_number'] . '</td>';
  
  // Раскодируйте строку JSON
  $orderDetails = json_decode($order['order_details'], true);
  
  // Задайте пустую строку для деталей заказа
  $detailText = '';
  
  // Пройдитесь по массиву деталей заказа
  foreach($orderDetails as $detail) {
    // Добавьте детали к строке, как вы их хотели
    $detailText .= $detail['item_name'] . ' - ' . $detail['name'] . ' - ' . $detail['quantity'] . '- ' . $detail['price'] . '<br>';
  }

  
  
  echo '<td>' . $detailText . '</td>';
  echo '<td>' . $order['total_price'] . ' Руб.</td>';
  echo '<td>' . date("Y-m-d H:i", strtotime($order['order_date'])) . '</td>';
  echo '<td>' . $order['customer_address'] . '</td>';  // Here is the address details
  echo '</tr>';
} 
?>
    </tbody>
  </table>
</div>
    
</body>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>


<style>
.btn {

background-color: brown;
border-color: black;
color: white;
display: block;
margin-left: auto;
margin-right: auto;
}

img {
display: block;
margin-left: auto;
margin-right: auto;
background-color: brown;
}

</style>

</html>