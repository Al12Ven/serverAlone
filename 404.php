<?php
/**
 * Страница ошибки 404
 * Возвращается при неизвестном маршруте
 */
http_response_code(404);
echo json_encode([
    'status' => 'error',
    'message' => 'Страница не найдена (404)'
]);
?>
