<?php ob_start(); ?>
<h1>メンテ記録を追加（<?= htmlspecialchars($vehicle['name']) ?>）</h1>

<form method="post" action="/?r=records_create&vehicle_id=<?= (int)$vehicle['id'] ?>">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
  <div><label>日付 <input type="date" name="serviced_at" required></label></div>
  <div><label>距離(km) <input type="number" name="odometer" min="0"></label></div>
  <div><label>内容 <input type="text" name="title" required></label></div>
  <div><label>費用(円) <input type="number" name="cost" min="0"></label></div>
  <div><label>店名 <input type="text" name="shop"></label></div>
  <div><label>メモ <br><textarea name="memo" rows="4" cols="40"></textarea></label></div>
  <button type="submit">保存</button>
  <a href="/?r=records&vehicle_id=<?= (int)$vehicle['id'] ?>">← 記録一覧へ</a>
</form>

<?php $content = ob_get_clean(); $title='Add Record'; include __DIR__ . '/layout.php'; ?>
