<?php
//    require_once __DIR__.'/../app/config.php';
//    echo "Hello PHP! DB接続テスト <br>";

// DB接続テスト
//    $stmt = $pdo->query("SELECT NOW() AS now_time");
//    $row  = $stmt->fetch();
//    echo "DBの現在時刻: " . htmlspecialchars($row['now_time'], ENT_QUOTES, 'UTF-8');

// ルーターを実装
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';

$r = $_GET['r'] ?? 'home';

switch ($r) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            check_csrf($_POST['csrf'] ?? '');
            $ok = login($_POST['email'] ?? '', $_POST['password'] ?? '');
            if ($ok) {
                header('Location: /');
                exit;
            }
            $error = 'メールまたはパスワードが違います';
        }
        include __DIR__ . '/../app/views/login.php';
        break;

    case 'logout':
        logout();
        header('Location: /?r=login');
        break;

    case 'home':
        default:
            require_login();
            include __DIR__ . '/../app/views/home.php';
            break;


    case 'vehicles':
        require_login();
        $st = $pdo -> prepare('SELECT * FROM vehicles WHERE user_id = ? ORDER BY id DESC');
        $st -> execute([current_user_id()]);
        $vehicles = $st->fetchAll();
        include __DIR__. '/../app/views/vehicles-index.php';
        break;

case 'vehicles_create':
    require_login();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_csrf($_POST['csrf'] ?? '');

        $name  = trim($_POST['name'] ?? '');
        $year  = ($_POST['year'] === '' ? null : (int)$_POST['year']);
        $grade = trim($_POST['grade'] ?? '');
        $plate = trim($_POST['plate_no'] ?? '');

        // 必須チェック（Bad Request の出所は基本ここ）
        if ($name === '') {
            http_response_code(400);
            exit('Bad Request');
        }

        $st = $pdo->prepare(
            'INSERT INTO vehicles (user_id, name, year, grade, plate_no) VALUES (?, ?, ?, ?, ?)'
        );
        $st->execute([ current_user_id(), $name, $year, $grade, $plate ]);

        header('Location: /?r=vehicles');
        exit;
    }

    // ← GETはフォームを表示
    include __DIR__ . '/../app/views/vehicles-form.php';
    break;

case 'vehicles_delete':
    require_login();

    // 削除は必ず POST で受ける
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Method Not Allowed');
    }

    // CSRFトークン検証
    check_csrf($_POST['csrf'] ?? '');

    // 入力チェック
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        exit('Bad Request');
    }

    // 所有者チェックを WHERE に含めて安全に削除
    $st = $pdo->prepare('DELETE FROM vehicles WHERE id = ? AND user_id = ?');
    $st->execute([$id, current_user_id()]);

    // PRG: 二重送信防止のため一覧へリダイレクト
    header('Location: /?r=vehicles');
    exit;


    
case 'vehicles_edit':
    require_login();

    // GET: フォーム表示（?id=xxx 必須）
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) { http_response_code(400); exit('Bad Request'); }

        // 所有者チェック込みで1件取得
        $st = $pdo->prepare('SELECT * FROM vehicles WHERE id = ? AND user_id = ?');
        $st->execute([$id, current_user_id()]);
        $vehicle = $st->fetch();

        if (!$vehicle) { http_response_code(404); exit('Not found'); }

        include __DIR__ . '/../app/views/vehicles-edit.php';
        break;
    }

    // POST: 更新実行
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_csrf($_POST['csrf'] ?? '');

        $id   = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        // 空文字ならNULLにしたい場合は以下
        $year = ($_POST['year'] === '' ? null : (int)$_POST['year']);
        $grade    = trim($_POST['grade'] ?? '');
        $plate_no = trim($_POST['plate_no'] ?? '');

        if ($id <= 0 || $name === '') {
            http_response_code(400); exit('Invalid input');
        }

        // 対象が本人のレコードか最終確認しつつUPDATE
        $st = $pdo->prepare(
            'UPDATE vehicles
             SET name = ?, year = ?, grade = ?, plate_no = ?
             WHERE id = ? AND user_id = ?'
        );
        $st->execute([$name, $year, $grade, $plate_no, $id, current_user_id()]);

        // 成功/不一致（0行更新）に関わらず一覧へ戻す
        header('Location: /?r=vehicles');
        exit;
    }

    // それ以外のメソッドは405
    http_response_code(405);
    exit('Method Not Allowed');

        include __DIR__.'/../app/views/vehicles-form.php';
        break;

}