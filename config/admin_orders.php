<?php
require_once 'databases.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'administrator') { // заменить 'admin' на правильное имя пользователя администратора
    echo "Нет доступа. Пожалуйста, войдите в систему как администратор, чтобы просмотреть эту страницу.";
    exit;
}

// код страницы с заказами для администратора
