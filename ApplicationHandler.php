<?php
/**
 * Класс для обработки заявок от пользователей
 * Обрабатывает формы: регистрация, обратная связь, заявка на выпуск
 */
class ApplicationHandler {
    // Объект базы данных
    private $pdo;
    
    /**
     * Конструктор класса
     * @param PDO $pdo - объект для работы с БД
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Обработка регистрации пользователя
     * @param array $data - данные из формы
     * @return array - ответ со статусом
     */
    public function register($data) {
        // Получаем и очищаем данные
        $name = trim($data['name'] ?? '');
        $email = trim($data['email_signup'] ?? '');
        $password = trim($data['password_signup'] ?? '');
        $passwordConfirm = trim($data['password_confirm'] ?? '');
        
        // Валидация на стороне сервера
        if (empty($name) || empty($email) || empty($password)) {
            return ['status' => 'error', 'message' => 'Заполните все обязательные поля'];
        }
        
        if ($password !== $passwordConfirm) {
            return ['status' => 'error', 'message' => 'Пароли не совпадают'];
        }
        
        try {
            // Подготавливаем SQL-запрос (защищено от SQL-инъекций)
            $stmt = $this->pdo->prepare(
                "INSERT INTO users (name, email_signup, password_signup, password_confirm) 
                 VALUES (?, ?, ?, ?)"
            );
            
            // Выполняем запрос с параметрами
            $stmt->execute([$name, $email, $password, $passwordConfirm]);
            
            return ['status' => 'success', 'message' => 'Регистрация успешна'];
            
        } catch (PDOException $e) {
            // Если email уже существует
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'Такой email уже зарегистрирован'];
            }
            return ['status' => 'error', 'message' => 'Ошибка БД: ' . $e->getMessage()];
        }
    }
    
    /**
     * Обработка формы обратной связи
     * @param array $data - данные из формы
     * @return array - ответ со статусом
     */
    public function feedback($data) {
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $message = trim($data['message'] ?? '');
        
        // Валидация
        if (empty($name) || empty($email) || empty($message)) {
            return ['status' => 'error', 'message' => 'Все поля обязательны'];
        }
        
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)"
            );
            $stmt->execute([$name, $email, $message]);
            
            return ['status' => 'success', 'message' => 'Сообщение отправлено!'];
            
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Ошибка БД: ' . $e->getMessage()];
        }
    }
    
    /**
     * Обработка заявки на выпуск музыки
     * @param array $data - данные из формы заявки
     * @return array - ответ со статусом
     */
    public function application($data) {
        // Получаем данные
        $artistName = trim($data['artist_name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $genre = trim($data['genre'] ?? '');
        $trackCount = (int)($data['track_count'] ?? 0);
        $experience = trim($data['experience'] ?? '');
        $socialLinks = trim($data['social_links'] ?? '');
        $agreeRules = isset($data['agree_rules']) && $data['agree_rules'] === '1';
        $agreeNewsletter = isset($data['agree_newsletter']) && $data['agree_newsletter'] === '1';
        
        // Валидация
        if (empty($artistName) || empty($email) || empty($genre)) {
            return ['status' => 'error', 'message' => 'Заполните обязательные поля'];
        }
        
        if ($trackCount < 1 || $trackCount > 100) {
            return ['status' => 'error', 'message' => 'Количество треков должно быть от 1 до 100'];
        }
        
        if (empty($experience)) {
            return ['status' => 'error', 'message' => 'Выберите опыт работы'];
        }
        
        if (!$agreeRules) {
            return ['status' => 'error', 'message' => 'Необходимо согласиться с правилами'];
        }
        
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO applications 
                (artist_name, email, phone, genre, track_count, experience, social_links, agree_rules, agree_newsletter) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            
            $stmt->execute([
                $artistName,
                $email,
                $phone,
                $genre,
                $trackCount,
                $experience,
                $socialLinks,
                $agreeRules ? 1 : 0,
                $agreeNewsletter ? 1 : 0
            ]);
            
            return ['status' => 'success', 'message' => 'Заявка отправлена!'];
            
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Ошибка БД: ' . $e->getMessage()];
        }
    }
}
?>
