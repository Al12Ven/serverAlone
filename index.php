<?php
// Подключаем базу данных и обработчик
require_once 'pdo.php';
require_once 'ApplicationHandler.php';

// Обработка POST-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new DB();
        $handler = new ApplicationHandler($pdo);
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'register':
                $result = $handler->register($_POST);
                break;

            case 'feedback':
                $result = $handler->feedback($_POST);
                break;

            case 'get_profile':
                $userId = (int)($_POST['user_id'] ?? 0);
                if ($userId > 0) {
                    $profile = $handler->getProfile($userId);
                    $result = $profile ?
                        ['status' => 'success', 'profile' => $profile] :
                        ['status' => 'error', 'message' => 'Профиль не найден'];
                } else {
                    $result = ['status' => 'error', 'message' => 'Неверный ID пользователя'];
                }
                break;

            case 'update_profile':
                $userId = (int)($_POST['user_id'] ?? 0);
                if ($userId > 0) {
                    $result = $handler->updateProfile($userId, $_POST) ?
                        ['status' => 'success', 'message' => 'Профиль обновлён'] :
                        ['status' => 'error', 'message' => 'Ошибка обновления профиля'];
                } else {
                    $result = ['status' => 'error', 'message' => 'Неверный ID пользователя'];
                }
                break;

            case 'get_roles':
                $userId = (int)($_POST['user_id'] ?? 0);
                if ($userId > 0) {
                    $roles = $handler->getUserRoles($userId);
                    $result = ['status' => 'success', 'roles' => $roles];
                } else {
                    $result = ['status' => 'error', 'message' => 'Неверный ID пользователя'];
                }
                break;

            case 'login':
                $result = $handler->login($_POST);
                break;

            case 'application':
                $result = $handler->application($_POST);
                break;

            default:
                $result = ['status' => 'error', 'message' => 'Неизвестное действие'];
        }

        echo json_encode($result);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Ошибка сервера: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Маршрутизация для GET-запросов
include_once "Route.php";
$uri = explode('?', $_SERVER['REQUEST_URI']);
Route::getRoute($uri[0]);
?>
