<?php
// Обработка GET-запросов
include_once 'pdo.php';

$pdo = new DB();

// Проверяем, есть ли данные в GET-запросе
if ($_GET !== null) {
    // Получаем action из запроса
    $action = $_GET['action'] ?? '';

    // Если запрос на получение заявок
    if ($action === 'get_applications') {
        try {
            // Подготавливаем SQL-запрос для получения всех заявок
            $stmt = $pdo->prepare(
                'SELECT id, artist_name, track_name, track_count, genre, email, created_at
                 FROM applications
                 ORDER BY created_at DESC
                 LIMIT 100'
            );
            $stmt->execute();

            // Получаем результат
            $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Возвращаем результат в формате JSON
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'applications' => $applications
            ]);

        } catch (PDOException $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Ошибка БД: ' . $e->getMessage()
            ]);
        }
    }
    // Если запрос на получение профилей пользователей
    elseif ($action === 'get_profiles') {
        try {
            // Подготавливаем SQL-запрос для получения всех пользователей
            $stmt = $pdo->prepare(
                'SELECT u.id, u.name, u.email_signup, p.bio, p.avatar_url
                 FROM users u
                 LEFT JOIN profiles p ON u.id = p.user_id
                 ORDER BY u.id DESC
                 LIMIT 50'
            );
            $stmt->execute();

            // Получаем результат
            $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Возвращаем результат в формате JSON
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'profiles' => $profiles
            ]);

        } catch (PDOException $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Ошибка БД: ' . $e->getMessage()
            ]);
        }
    }
    // Если запрос на получение пользователя по id
    elseif (isset($_GET['id'])) {
        // Получаем id из запроса и преобразуем в целое число для безопасности
        $id = intval($_GET['id']);

        // Подготавливаем SQL-запрос для получения имени пользователя по id
        $stmt = $pdo->prepare('SELECT name FROM users WHERE id = ?');
        $stmt->bindParam(1, $id);
        $stmt->execute();

        // Получаем результат
        $results = $stmt->fetchAll();
        $result = json_encode($results);

        // Возвращаем результат в формате JSON
        header('Content-Type: application/json');
        echo $result;
    } else {
        // Если GET-данных нет, возвращаем false
        return false;
    }
} else {
    // Если GET-данных нет, возвращаем false
    return false;
}
