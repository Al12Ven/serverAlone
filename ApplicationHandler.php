<?php
/**
 * Класс для обработки заявок от пользователей
 * Обрабатывает формы: регистрация, обратная связь
 */
require_once 'pdo.php';

class ApplicationHandler {
    // Объект базы данных
    private $pdo;

    // Конструктор класса
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Обработка регистрации пользователя
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

            // Получаем ID нового пользователя
            $userId = $this->pdo->lastInsertId();

            // Назначаем роль 'user' по умолчанию
            $this->assignRole($userId, 'user');

            // Создаём пустой профиль
            $this->createProfile($userId);

            return ['status' => 'success', 'message' => 'Регистрация успешна', 'user_id' => $userId];

        } catch (PDOException $e) {
            // Если email уже существует
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'Такой email уже зарегистрирован'];
            }
            return ['status' => 'error', 'message' => 'Ошибка БД: ' . $e->getMessage()];
        }
    }

    // Обработка формы обратной связи
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

    // Создание профиля для пользователя
    public function createProfile($userId) {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO profiles (user_id, bio, avatar_url) VALUES (?, NULL, NULL)"
            );
            $stmt->execute([$userId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Назначение роли пользователю
    public function assignRole($userId, $roleName) {
        try {
            // Получаем ID роли по названию
            $stmt = $this->pdo->prepare("SELECT id FROM roles WHERE name = ?");
            $stmt->execute([$roleName]);
            $role = $stmt->fetch();

            if (!$role) {
                return false;
            }

            // Назначаем роль пользователю
            $stmt = $this->pdo->prepare(
                "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)"
            );
            $stmt->execute([$userId, $role['id']]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Получение профиля пользователя
    public function getProfile($userId) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT p.*, u.name, u.email_signup 
                 FROM profiles p 
                 JOIN users u ON p.user_id = u.id 
                 WHERE p.user_id = ?"
            );
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    // Обновление профиля пользователя
    public function updateProfile($userId, $data) {
        try {
            $bio = $data['bio'] ?? null;
            $avatarUrl = $data['avatar_url'] ?? null;

            $stmt = $this->pdo->prepare(
                "UPDATE profiles SET bio = ?, avatar_url = ? WHERE user_id = ?"
            );
            $stmt->execute([$bio, $avatarUrl, $userId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Получение ролей пользователя
    public function getUserRoles($userId) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT r.name, r.description
                 FROM roles r
                 JOIN user_roles ur ON r.id = ur.role_id
                 WHERE ur.user_id = ?"
            );
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // Обработка формы входа пользователя
    public function login($data) {
        $email = trim($data['email_signup'] ?? '');
        $password = trim($data['password_signup'] ?? '');

        // Валидация
        if (empty($email) || empty($password)) {
            return ['status' => 'error', 'message' => 'Заполните все поля'];
        }

        try {
            // Ищем пользователя по email
            $stmt = $this->pdo->prepare(
                "SELECT id, name, email_signup, password_signup FROM users WHERE email_signup = ?"
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Если пользователь не найден
            if (!$user) {
                return ['status' => 'error', 'message' => 'Пользователь не найден'];
            }

            // Проверяем пароль (в реальном проекте используйте password_verify)
            if ($user['password_signup'] !== $password) {
                return ['status' => 'error', 'message' => 'Неверный пароль'];
            }

            // Вход успешен
            return [
                'status' => 'success',
                'message' => 'Вход выполнен успешно',
                'user_id' => $user['id'],
                'name' => $user['name']
            ];

        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Ошибка БД: ' . $e->getMessage()];
        }
    }

    // Обработка заявки на выпуск музыки
    public function application($data) {
        // Получаем и очищаем данные
        $artistName = trim($data['artist_name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $genre = trim($data['genre'] ?? '');
        $trackCount = (int)($data['track_count'] ?? 0);
        $trackName = trim($data['track_name'] ?? '');
        $experience = trim($data['experience'] ?? '');
        $socialLinks = trim($data['social_links'] ?? '');
        $agreeRules = isset($data['agree_rules']) && $data['agree_rules'] === '1';
        $agreeNewsletter = isset($data['agree_newsletter']) && $data['agree_newsletter'] === '1';

        // Валидация
        if (empty($artistName) || empty($email) || empty($genre) || empty($trackName)) {
            return ['status' => 'error', 'message' => 'Заполните все обязательные поля'];
        }

        if ($trackCount < 1 || $trackCount > 100) {
            return ['status' => 'error', 'message' => 'Неверное количество треков'];
        }

        if (empty($experience)) {
            return ['status' => 'error', 'message' => 'Выберите ваш опыт'];
        }

        if (!$agreeRules) {
            return ['status' => 'error', 'message' => 'Необходимо согласие с правилами'];
        }

        try {
            // Вставляем заявку в базу данных
            $stmt = $this->pdo->prepare(
                "INSERT INTO applications
                 (artist_name, email, phone, genre, track_count, track_name, experience, social_links, agree_rules, agree_newsletter)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $artistName,
                $email,
                $phone,
                $genre,
                $trackCount,
                $trackName,
                $experience,
                $socialLinks,
                $agreeRules ? 1 : 0,
                $agreeNewsletter ? 1 : 0
            ]);

            return ['status' => 'success', 'message' => 'Заявка отправлена! Мы свяжемся с вами в ближайшее время.'];

        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Ошибка БД: ' . $e->getMessage()];
        }
    }
}
?>
