<?php

require_once 'databases.php';


mysqli_query($induction, "INSERT INTO `MenuItems` (`item_id`, `item_name`, `category_id`, `description`, `price`, `image_url`) VALUES (NULL, 'Новый продукт', NULL, 'Описание', '0', NULL)");


header('Location:/');