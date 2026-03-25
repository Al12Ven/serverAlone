<?php
/**
 * Серверная часть проекта AlR1_Beats
 * Обработка запросов от клиентской части (AJAX)
 * 
 * Поддерживаемые действия:
 * - register: регистрация пользователя
 * - feedback: отправка сообщения обратной связи
 * - application: заявка на выпуск музыки
 */


// Проверяем, что запрос методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Метод не разрешён']);
    exit;
}

try {
    // Создаём объект подключения к базе данных
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Создаём обработчик заявок
    $handler = new ApplicationHandler($pdo);
    
    // Получаем действие из запроса
    // action определяет, какую форму обрабатываем
    $action = $_POST['action'] ?? '';
    
    // Обрабатываем в зависимости от действия
    switch ($action) {
        case 'register':
            // Регистрация пользователя
            $result = $handler->register($_POST);
            break;
            
        case 'feedback':
            // Обратная связь
            $result = $handler->feedback($_POST);
            break;
            
        case 'application':
            // Заявка на выпуск музыки
            $result = $handler->application($_POST);
            break;
            
        default:
            // Неизвестное действие
            $result = ['status' => 'error', 'message' => 'Неизвестное действие'];
    }
    
    // Возвращаем результат клиенту
    echo json_encode($result);
    
} catch (Exception $e) {
    // Обработка любых исключений
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Внутренняя ошибка сервера: ' . $e->getMessage()
    ]);
}
?>
