<?php
require_once __DIR__ . '/config.php';

function current_user_id(): ?int {
    return $_SESSION['user_id'] ?? null;
}

function require_login(): void {
    if (!current_user_id()) {
        header('Location: /?r=login');
        exit;
    }
}

function check_csrf(?string $token): void {
    if (!isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token ?? '')) {
        http_response_code(400);
        exit('Invalid CSRF token');
    }
}

function login(string $email, string $password): bool {
    global $pdo;
    $st = $pdo->prepare('SELECT id, password_hash FROM users WHERE email = ?');
    $st->execute([$email]);
    $u = $st->fetch();
    if ($u && password_verify($password, $u['password_hash'])) {
        $_SESSION['user_id'] = (int)$u['id'];
        return true;
    }
    return false;
}

function logout(): void {
    $_SESSION = [];
    session_destroy();
}