<?php
  session_start();
require_once 'databases.php';

if (isset($_POST['sublogin'])) {

    $login = $_POST['login_var'];

    $password = $_POST['password'];

    $query = "SELECT * FROM `Customers` WHERE (first_name = '$login' OR email = '$login')";
    $res = mysqli_query($induction, $query);

    $numRows = mysqli_num_rows($res);
    var_dump($numRows);

    if ($numRows  == 1) {
        $row = mysqli_fetch_assoc($res);
        if ($password == $row['password']) {
            $_SESSION["login_sess"] = "1";
            $_SESSION["login_email"] = $row['email'];
            $_SESSION["id"] = $row['customer_id'];
            $_SESSION["admink"] = false;
            var_dump($_SESSION);
            header("location:account.php");
        } else {
            header("location:login.php?loginerror=" . $login);
        }
    } else {
        header("location:login.php?loginerror=" . $login);
    }
}
