<?php
require_once 'databases.php';
$output = '';
if(isset($_POST["productId"])){
    $product = mysqli_query($induction, "SELECT * FROM MenuItems WHERE item_id = '".$_POST["productId"]."'");
    $row = mysqli_fetch_array($product);
    $output = $row["price"];
}
echo $output;