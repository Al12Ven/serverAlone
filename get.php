<?php
include_once 'pdo.php';

$pdo = new DB();
$action = $_GET['action'] ?? '';

header('Content-Type: application/json');

if ($action === 'get_applications') {
    try {
        $stmt = $pdo->prepare(
            'SELECT id, artist_name, track_name, track_count, genre, email, created_at
             FROM applications ORDER BY created_at DESC LIMIT 100'
        );
        $stmt->execute();
        echo json_encode([
            'status' => 'success',
            'applications' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Ошибка БД: ' . $e->getMessage()
        ]);
    }
} elseif ($action === 'get_user_applications') {
    $email = $_GET['email'];
    $stmt = $pdo->prepare(
        'SELECT id, artist_name, track_name, track_count, genre, email, phone, experience, social_links, agree_rules, agree_newsletter, created_at
         FROM applications WHERE email = ? ORDER BY created_at DESC'
    );
    $stmt->execute([$email]);
    echo json_encode([
        'status' => 'success',
        'applications' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
} elseif ($action === 'get_profiles') {
    try {
        $stmt = $pdo->prepare(
            'SELECT u.id, u.name, u.email_signup, p.bio, p.avatar_url
             FROM users u LEFT JOIN profiles p ON u.id = p.user_id
             ORDER BY u.id DESC LIMIT 50'
        );
        $stmt->execute();
        echo json_encode([
            'status' => 'success',
            'profiles' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Ошибка БД: ' . $e->getMessage()
        ]);
    }
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare('SELECT name FROM users WHERE id = ?');
    $stmt->execute([$id]);
    echo json_encode($stmt->fetchAll());
} else {
    return false;
}
