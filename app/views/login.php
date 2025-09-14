<?php
// $error があれば表示される想定
?>
<!doctype html>
<html lang="ja">
<head><meta charset="utf-8"><title>ログイン</title></head>
<body>
  <h1>ログイン</h1>
  <?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
  <?php endif; ?>
  <form method="post" action="/?r=login">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
    <div><label>Email <input type="email" name="email" required></label></div>
    <div><label>Password <input type="password" name="password" required></label></div>
    <button type="submit">ログイン</button>
    <p>初めての方は <a href="/?r=register">新規登録</a> へ</p>
  </form>
</body>
</html>