<?php require_once 'databases.php'; 
session_start();
session_destroy(); 
header("location:/index.php"); 
?>