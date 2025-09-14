<?php ob_start(); ?>
<h1>車両を編集</h1>

<form method="post" action="/?r=vehicles_edit">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
  <input type="hidden" name="id" value="<?= (int)$record['id'] ?>">

  <div>
    <label>名前
      <input name="name" required value="<?= htmlspecialchars($vehicle['name'] ?? '') ?>">
    </label>
  </div>

  <div>
    <label>年式
      <input name="year" type="number" min="1900" max="2100"
             value="<?= htmlspecialchars((string)($vehicle['year'] ?? '')) ?>">
    </label>
  </div>

  <div>
    <label>グレード
      <input name="grade" value="<?= htmlspecialchars($vehicle['grade'] ?? '') ?>">
    </label>
  </div>

  <div>
    <label>ナンバー
      <input name="plate_no" value="<?= htmlspecialchars($vehicle['plate_no'] ?? '') ?>">
    </label>
  </div>

  <button type="submit">更新</button>
  <a href="/?r=vehicles">戻る</a>
</form>

<?php
$content = ob_get_clean();
$title = 'Edit Vehicle';
include __DIR__ . '/layout.php';
