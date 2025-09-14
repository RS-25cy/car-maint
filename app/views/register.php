<?php ob_start(); ?>
<h1>新規ユーザー登録</h1>

<?php if (!empty($register_errors)): ?>
  <ul style="color:red;">
    <?php foreach ($register_errors as $e): ?>
      <li><?= htmlspecialchars($e) ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<form method="post" action="/?r=register">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

  <div>
    <label>名前
      <input name="name" required value="<?= htmlspecialchars($old['name'] ?? '') ?>">
    </label>
  </div>

  <div>
    <label>メールアドレス
      <input name="email" type="email" required value="<?= htmlspecialchars($old['email'] ?? '') ?>">
    </label>
  </div>

  <div>
    <label>パスワード
      <input name="password" type="password" required minlength="8">
    </label>
  </div>

  <div>
    <label>パスワード（確認）
      <input name="password_confirm" type="password" required minlength="8">
    </label>
  </div>

  <button type="submit">登録</button>
  <a href="/?r=login">← ログインへ戻る</a>
</form>

<?php
$content = ob_get_clean();
$title = 'Register';
include __DIR__ . '/layout.php';
