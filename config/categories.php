<?php
require_once 'databases.php';

try {
    $pdo = new PDO("mysql:host=$par1_ip;dbname=$par4_db;charset=utf8", $par2_name, $par3_p);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL-запрос для извлечения категорий из таблицы
    $sql = "SELECT category_id, category_name FROM Categories";
    #$sql = "SELECT Categories.category_id, Categories.category_name, MenuItems.item_id FROM Categories INNER JOIN MenuItems ON Categories.category_id = MenuItems.category_id;";
    
    $stmt = $pdo->query($sql);

    // Массив для хранения данных о категориях
    $categories = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Добавление данных категории в массив
        $category = [
            'id' => $row['category_id'],
            'name' => $row['category_name'],
        ];
        $categories[] = $category;
    }

    // Отправляем данные в формате JSON
    echo json_encode($categories, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    // В случае ошибки базы данных, вернуть пустой массив и сообщение об ошибке
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
