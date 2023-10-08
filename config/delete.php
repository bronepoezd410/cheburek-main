<?php

require_once 'databases.php';

$id = $_GET['id'];

mysqli_query($induction, "DELETE FROM `MenuItems` WHERE `MenuItems`.`item_id` = '$id'");


header('Location:/');