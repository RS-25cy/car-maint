<?php
declare(strict_types=1);
session_start();

define('DB_DSN','mysql:host=127.0.0.1;port=8889;dbname=car_maint;charset=utf8mb4');
define('DB_USER','root');
define('DB_PASS','root');


try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch(PDOException  $e) {
    exit('DB接続エラー:' . $e->getMessage());
}

// CSRFトークン
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}