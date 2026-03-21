<?php
// Подключение к базе данных
if($_GET !== null){



    
     $host = '127.0.0.1';
     $db = 'name date base';
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


        //$result = json_encode($_GET );
    $name = htmlspecialchars($_GET['name']);
    $email_signup = htmlspecialchars($_GET['email_signup']);
    $password_confirmword_signup = htmlspecialchars($_GET['password_confirmword_signup']);
    $password_confirmword_confirm = htmlspecialchars($_GET['password_confirmword_confirm']);


    $stmt = $pdo->prepare("INSERT INTO "name date base" (name, email_signup, password_confirmword_signup, password_confirm) VALUES (?, ?, ?, ?)");
    $stmt->bindParam(1, $name);
    $stmt->bindParam(2, $email_signup);
    $stmt->bindParam(3, $password_confirmword_signup);
    $stmt->bindParam(4, $password_confirm);
    $stmt->execute();

}
