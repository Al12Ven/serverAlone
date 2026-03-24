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

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'viewer';
    $artist_name = $_POST['artist_name'] ?? null;
    $artist_country = $_POST['artist_country'] ?? null;
    $artist_genre = $_POST['artist_genre'] ?? null;

    // Хеширование пароля
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Если роль artist и указан artist_name - создаем исполнителя
    $artist_id = null;
    if ($role === 'artist' && $artist_name) {
        $stmt = $pdo->prepare("INSERT INTO artists (name, country, genre) VALUES (?, ?, ?)");
        $stmt->execute([$artist_name, $artist_country, $artist_genre]);
        $artist_id = $pdo->lastInsertId();
    }

    // Регистрация пользователя
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, artist_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bindParam(1, $username);
    $stmt->bindParam(2, $email);
    $stmt->bindParam(3, $password_hash);
    $stmt->bindParam(4, $role);
    $stmt->bindParam(5, $artist_id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Регистрация успешна']);
}else{
    echo json_encode(['status' => 'error', 'message' => 'Нет данных']);
}
?>
