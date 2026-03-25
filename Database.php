<?php
/**
 * Класс для подключения к базе данных
 * Использует PDO для безопасной работы с MySQL
 */
class Database {
    // Параметры подключения
    private $host = '127.0.0.1';
    private $db = 'alr1';
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';
    
    // Объект PDO
    private $pdo;
    
    /**
     * Конструктор класса
     * Подключается к базе данных при создании объекта
     */
    public function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            // Если ошибка подключения - возвращаем ошибку
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Ошибка подключения к БД: ' . $e->getMessage()
            ]);
            exit;
        }
    }
    
    /**
     * Метод для получения объекта PDO
     * @return PDO Объект для работы с базой данных
     */
    public function getConnection() {
        return $this->pdo;
    }
}
?>
