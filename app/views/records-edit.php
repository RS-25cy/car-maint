<?php ob_start(); ?>
<h1>メンテ記録を編集</h1>

<form method="post" action="/?r=records_edit">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
  <input type="hidden" name="id" value="<?= (int)$record['id'] ?>">

  <div>
    <label>日付
      <input type="date" name="serviced_at" value="<?= htmlspecialchars($record['serviced_at']) ?>" required>
    </label>
  </div>

  <div>
    <label>走行距離 (km)
      <input type="number" name="odometer" value="<?= htmlspecialchars($record['odometer']) ?>">
    </label>
  </div>

  <div>
    <label>内容
      <input type="text" name="title" value="<?= htmlspecialchars($record['title']) ?>" required>
    </label>
  </div>

  <div>
    <label>費用 (円)
      <input type="number" name="cost" value="<?= htmlspecialchars($record['cost']) ?>">
    </label>
  </div>

  <div>
    <label>整備したお店
      <input type="text" name="shop" value="<?= htmlspecialchars($record['shop']) ?>">
    </label>
  </div>

  <div>
    <label>メモ
      <textarea name="memo" rows="4" cols="40"><?= htmlspecialchars($record['memo']) ?></textarea>
    </label>
  </div>

  <button type="submit">更新</button>
  <a href="/?r=records&vehicle_id=<?= (int)$record['vehicle_id'] ?>">← 記録一覧へ</a>
</form>

<?php $content = ob_get_clean(); $title='Edit Record'; include __DIR__ . '/layout.php'; ?>
