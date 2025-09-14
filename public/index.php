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

    // 新規ユーザー登録
    case 'register':
    // 既ログインならホームへ
    if (!empty($_SESSION['user_id'])) { header('Location: /'); exit; }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_csrf($_POST['csrf'] ?? '');

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $pw       = $_POST['password'] ?? '';
        $pw_conf  = $_POST['password_confirm'] ?? '';

        // サーバ側バリデーション
        $errors = [];
        if ($name === '') $errors[] = '名前は必須です';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'メールアドレスの形式が不正です';
        if (strlen($pw) < 8) $errors[] = 'パスワードは8文字以上にしてください';
        if ($pw !== $pw_conf) $errors[] = 'パスワード（確認）と一致しません';

        // 既存メールチェック
        if (!$errors) {
            $st = $pdo->prepare('SELECT 1 FROM users WHERE email = ?');
            $st->execute([$email]);
            if ($st->fetch()) $errors[] = 'そのメールアドレスは既に使われています';
        }

        if ($errors) {
            // エラー時はフォームを再表示（メッセージ＆入力の一部を保持）
            $register_errors = $errors;
            $old = ['name' => $name, 'email' => $email];
            include __DIR__ . '/../app/views/register.php';
            break;
        }

        // 登録
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $st = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
        $st->execute([$name, $email, $hash]);

        // そのままログインさせる（好みで：ログイン画面へリダイレクトでもOK）
        $_SESSION['user_id'] = (int)$pdo->lastInsertId();
        header('Location: /');
        exit;
    }

    // GET: フォーム表示
    include __DIR__ . '/../app/views/register.php';
    break;

    // ある車両記録一覧
    case 'records':
    require_login();
    $vehicle_id = (int)($_GET['vehicle_id'] ?? 0);
    if ($vehicle_id <= 0) { http_response_code(400); exit('Bad Request'); }

    // 車両が自分のものか確認
    $st = $pdo->prepare('SELECT id, name FROM vehicles WHERE id = ? AND user_id = ?');
    $st->execute([$vehicle_id, current_user_id()]);
    $vehicle = $st->fetch();
    if (!$vehicle) { http_response_code(404); exit('Not Found'); }

    // 記録一覧
    $st = $pdo->prepare(
        'SELECT * FROM maintenance_records
         WHERE vehicle_id = ? AND user_id = ?
         ORDER BY serviced_at DESC, id DESC'
    );
    $st->execute([$vehicle_id, current_user_id()]);
    $records = $st->fetchAll();

    include __DIR__ . '/../app/views/records-index.php';
    break;

    

    // 記録追加
    case 'records_create':
    require_login();

    $vehicle_id = (int)($_GET['vehicle_id'] ?? $_POST['vehicle_id'] ?? 0);
    if ($vehicle_id <= 0) { http_response_code(400); exit('Bad Request'); }

    // 所有車両かチェック（GET/POST 共通）
    $st = $pdo->prepare(
        'SELECT id, name FROM vehicles WHERE id = ? AND user_id = ?'
    );
    $st->execute([$vehicle_id, current_user_id()]);
    $vehicle = $st->fetch();
    if (!$vehicle) { http_response_code(404); exit('Not Found'); }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_csrf($_POST['csrf'] ?? '');

        $serviced_at = trim($_POST['serviced_at'] ?? '');
        $title       = trim($_POST['title'] ?? '');
        $odometer    = ($_POST['odometer'] === '' ? null : (int)$_POST['odometer']);
        $cost        = ($_POST['cost']     === '' ? null : (int)$_POST['cost']);
        $shop = trim($_POST['shop'] ?? '');
        $memo        = trim($_POST['memo'] ?? '');

        if ($serviced_at === '' || $title === '') {
            http_response_code(400); exit('Bad Request');
        }

        $st = $pdo->prepare(
            'INSERT INTO maintenance_records
             (user_id, vehicle_id, serviced_at, odometer, title, memo, cost, shop)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $st->execute([current_user_id(), $vehicle_id, $serviced_at,  $odometer, $title, $memo, $cost,$shop]);

        header('Location: /?r=records&vehicle_id=' . $vehicle_id);
        exit;
    }

    include __DIR__ . '/../app/views/records-form.php';
    break;


    // 記録編集
    case 'records_edit':
    require_login();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { http_response_code(400); exit('Bad Request'); }

        $st = $pdo->prepare(
        'SELECT * FROM maintenance_records WHERE id = ? AND user_id = ?'
        );
        $st->execute([$id, current_user_id()]);
        $record = $st->fetch();
        if (!$record) { http_response_code(404); exit('Not Found'); }

        // 所属車両の名前も出したい場合
        $st2 = $pdo->prepare('SELECT id, name FROM vehicles WHERE id = ? AND user_id = ?');
        $st2->execute([$record['vehicle_id'], current_user_id()]);
        $vehicle = $st2->fetch();

        include __DIR__ . '/../app/views/records-edit.php';
        break;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_csrf($_POST['csrf'] ?? '');

        $id          = (int)($_POST['id'] ?? 0);
        $serviced_at = trim($_POST['serviced_at'] ?? '');
        $title       = trim($_POST['title'] ?? '');
        $odometer    = ($_POST['odometer'] === '' ? null : (int)$_POST['odometer']);
        $cost        = ($_POST['cost']     === '' ? null : (int)$_POST['cost']);
        $shop        = trim($_POST['shop'] ?? ''); 
        $memo        = trim($_POST['memo'] ?? '');

        if ($id <= 0 || $serviced_at === '' || $title === '') {
            http_response_code(400); exit('Bad Request');
        }

        $st = $pdo->prepare(
            'UPDATE maintenance_records
             SET serviced_at = ?, odometer = ?, title = ?, memo = ?, cost = ?, shop = ?
             WHERE id = ? AND user_id = ?'
        );
        $st->execute([$serviced_at, $odometer, $title, $memo, $cost, $shop, $id, current_user_id()]);

        // vehicle_id を取り直して一覧へ
        $st2 = $pdo->prepare('SELECT vehicle_id FROM maintenance_records WHERE id = ?');
        $st2->execute([$id]);
        $v = $st2->fetch();
        $vehicle_id = $v ? (int)$v['vehicle_id'] : 0;

        header('Location: /?r=records&vehicle_id=' . $vehicle_id);
        exit;
    }

    http_response_code(405); exit('Method Not Allowed');

    // 記録削除
    case 'records_delete':
    require_login();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); exit('Method Not Allowed');
    }
    check_csrf($_POST['csrf'] ?? '');

    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); exit('Bad Request'); }

    // vehicle_id を先に取得しておく（リダイレクト用）
    $st = $pdo->prepare('SELECT vehicle_id FROM maintenance_records WHERE id = ? AND user_id = ?');
    $st->execute([$id, current_user_id()]);
    $rec = $st->fetch();
    if (!$rec) { http_response_code(404); exit('Not Found'); }
    $vehicle_id = (int)$rec['vehicle_id'];

    $st = $pdo->prepare('DELETE FROM maintenance_records WHERE id = ? AND user_id = ?');
    $st->execute([$id, current_user_id()]);

    header('Location: /?r=records&vehicle_id=' . $vehicle_id);
    exit;

}