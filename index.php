<?php
// Взаимодействие с front
if($_POST !== null){

    $host = '127.0.0.1';
    $db = 'alr1';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    // Получение объекта PDO
    $pdo = new PDO($dsn, $user, $pass, $opt);

    $name = $_POST['name'];
    $email_signup = $_POST['email_signup'];
    $password_signup = $_POST['password_signup'];
    $password_confirm = $_POST['password_confirm'];

    // Регистрация пользователя
    $stmt = $pdo->prepare("INSERT INTO users (name, email_signup, password_signup, password_confirm) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email_signup, $password_signup, $password_confirm]);

    echo json_encode(['status' => 'success', 'message' => 'Регистрация успешна']);
}else{
    echo json_encode(['status' => 'error', 'message' => 'Нет данных']);
}
?>
