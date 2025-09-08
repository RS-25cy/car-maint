<?php ob_start(); ?>
<h1>車両を追加</h1>
<form method="post" action="/?r=vehicles_create">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
  <div><label>名前 <input name="name" required></label></div>
  <div><label>年式 <input name="year" type="number" min="1900" max="2100"></label></div>
  <div><label>グレード <input name="grade"></label></div>
  <div><label>ナンバー <input name="plate_no"></label></div>
  <button type="submit">保存</button>
</form>
<p><a href="javascript:history.back()">← 戻る</a></p>
<?php $content = ob_get_clean(); $title='Add Vehicle'; include __DIR__ . '/layout.php'; ?>
