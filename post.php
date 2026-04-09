<?php
// Обработка POST-запросов
include_once 'pdo.php';

$pdo = new DB();

// Проверяем, есть ли данные в POST-запросе
if ($_POST !== null) {
    // Получаем данные из формы
    $name = $_POST['name'];
    $email_signup = $_POST['email_signup'];

    // Подготавливаем SQL-запрос для вставки данных в таблицу users
    $stmt = $pdo->prepare('INSERT INTO users (name, email_signup) VALUES (?, ?)');
    $stmt->bindParam(1, $name);
    $stmt->bindParam(2, $email_signup);
    $stmt->execute();

    // Возвращаем успех
    echo json_encode(['status' => 'success', 'message' => 'Данные сохранены']);
} else {
    // Если POST-данных нет, возвращаем false
    return false;
}
