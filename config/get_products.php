<?php
require_once 'databases.php';
$output = '';
if(isset($_POST["categoryId"])){
    $output .= '<option value="">Выберите продукт</option>';
    $products = mysqli_query($induction, "SELECT * FROM MenuItems WHERE category_id = '".$_POST["categoryId"]."'");
    while($row = mysqli_fetch_array($products)){
        $output .= '    <option value="'. $row['item_id'] .'">'. $row['item_name'] .'</option>';
    }
}
echo $output;
