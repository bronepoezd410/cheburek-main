<?php session_start() ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Безупречная чебуречная</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto:wght@100;300;400;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/site.css">
</head>



<?php
require_once 'databases.php';
session_start();
$sql = "SELECT * FROM `MenuItems` WHERE 1=1"; // '1=1' is used to simplify the WHERE clause construction
$result = mysqli_query($induction, $sql);

$sqlcategories = "SELECT * FROM `Categories` WHERE 1=1"; // '1=1' is used to simplify the WHERE clause construction
$resultcategories = mysqli_query($induction, $sqlcategories);
?>




<div class="img-btn-container">
    <div class="images">
        <img src="../images/zagruzheno.png" alt="">
    </div>
    <a href="../index.php" class="back-btn">Назад</a>
</div>


<div class="col-md-6 input-container">
    <label for="customerName">Имя:</label>
    <input type="text" class="form-control" id="customerName">
</div>

<div class="col-md-6 input-container">
    <label for="inCity">Вы находитесь в городе?</label>
    <select class="form-control" id="inCity">
        <option value="" selected disabled>Выберите...</option>
        <option value="yes">Да</option>
        <option value="no">Нет</option>
    </select>
</div>

<div id="addressForm" class="col-md-6 input-container" style="display: none;">
    <label for="customerAddress">Адрес доставки:</label>
    <input type="text" class="form-control" id="customerAddress">
</div>

<div class="col-md-6 input-container">
    <label for="phoneNumber">Номер телефона: </label>
    <input type="tel" class="form-control" id="phoneNumber">
</div>



</div>
<hr>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 transparent-bg">
            <label for="productCategory" class="btn btn-primary">Категория продукта:</label>
            <select class="form-control" id="productCategory">
                <?php
                $categories = mysqli_query($induction, "SELECT * FROM Categories");
                while($row = mysqli_fetch_array($categories)){ ?>
                    <option value="<?php echo $row['category_id'];?>"><?php echo $row['category_name'];?></option>                
                <?php } ?>
            </select>
        </div>

        <div class="col-md-4 transparent-bg">
            <label for="productName" class="btn btn-primary">Название продукта:</label>
            <select class="form-control" id="productName"></select>
        </div>
            
        <div class="col-md-2 transparent-bg">
            <label for="productQuantity" class="btn btn-primary">Количество:</label>
            <input type="number" class="form-control" id="productQuantity" min="1" value="1">
        </div>
            
        <div class="col-md-2 transparent-bg">
            <label for="productPrice" class="btn btn-primary">Цена:</label>
            <input type="text" class="form-control" id="productPrice" disabled>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 btn btn-primary">Итого:</div>
        <div class="col-md-2 btn btn-primary" id="totalSum" style="background-color: var(--color-credit); color: white; font-weight: bold;">0</div>
    </div>
</div>

        <hr>
        <div class="text">
        <button type="button" id="addProduct" class="btn btn-primary">Добавить</button>
        <button type="button" id="removeLastProduct" class="btn btn-danger">Удалить</button>
        <button type="button" id="placeOrder" class="btn btn-success">Заказать</button>
        </div>
    </form>
    
    <hr>
    
    <div id="addedProducts"></div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
var totalSum = 0;
var deliveryIncluded = false;

$(document).ready(function(){

    $('#inCity').change(function(){

        $("#addressForm").show();

        if(deliveryIncluded) { // check if delivery fee was already included
            if($(this).val() == 'yes'){
                totalSum -= 500; // subtract the previously added fee
            } else {
                totalSum -= 200;
            }
        }

        if($(this).val() == 'yes'){
            totalSum += 200;
        } else {
            totalSum += 500;
        }

        deliveryIncluded = true; // mark that delivery fee was included

        $('#totalSum').text(totalSum);
    });

    $('#productCategory').change(function(){
        var categoryId = $(this).val();
        $.ajax({
            url: "get_products.php",
            method: "POST",
            data: {categoryId:categoryId},
            success: function(data){
                $('#productName').html(data);
            }
        });
    });
    
    $('#productName').change(function() {
        var productId = $(this).val();
        $.ajax({
            url: "get_product_price.php",
            method: "POST",
            data: {productId:productId},
            success: function(data){
                $('#productPrice').val(data);
            }
        });
    });
    
    $('#addProduct').click(function() {
        var categoryId = $('#productCategory').val();
        var productId = $('#productName').val();
        var quantity = $('#productQuantity').val();
        var price = $('#productPrice').val();
        
        var productRow = '<div class="product-row"><div class="col-md-4">' + '"' +$('#productCategory option:selected').text() + '" -'+
            '</div><div class="col-md-4">' + $('#productName option:selected').text() + "-" +
            '</div><div class="col-md-2">' + quantity + 'шт' +
            '</div><div class="col-md-2">' + price + 'руб'
            '</div></div><hr>';

        $('#addedProducts').append(productRow);
        totalSum += quantity * price; // add to total sum
        $('#totalSum').text(totalSum); // update total sum on page
    });
    $('#removeLastProduct').click(function() {
        var lastProductRow = $('#addedProducts .product-row').last();
        
        var quantity = parseFloat(lastProductRow.children().eq(2).text()); // get quantity of the last product
        var price = parseFloat(lastProductRow.children().eq(3).text()); // get price of the last product
        
        totalSum -= quantity * price; // subtract from total sum
        $('#totalSum').text(totalSum); // update total sum on page

        lastProductRow.next().remove(); // remove <hr>
        lastProductRow.remove(); // remove product row
    });
    $('#placeOrder').click(function() {
    let customerName = $('#customerName').val();
    let phoneNumber = $('#phoneNumber').val();
    let customerAddress = $('#customerAddress').val(); 
    let products = [];

    $('#addedProducts .product-row').each(function() {
        let productCategory = $(this).children().eq(0).text();
        let productName = $(this).children().eq(1).text();
        let quantity = $(this).children().eq(2).text();
        let price = $(this).children().eq(3).text();
        products.push({category: productCategory, name: productName, quantity: quantity, price: price});
    });
    
    let orderDetails = JSON.stringify(products);

    $.ajax({
        type: "POST",
        url: "place_order.php",
        data: {
            customerName: customerName,
            phoneNumber: phoneNumber,
            customerAddress: customerAddress, 
            orderDetails: orderDetails,
            totalPrice: totalSum
        },
        success: function (response) {
            alert("Order has been placed successfully!");
            
            // Очистить корзину после успешного создания заказа
            $('#addedProducts').empty();
            totalSum = 0;
            $('#totalSum').text(totalSum);
        },
    });
});
});
</script>
</body>
</html>