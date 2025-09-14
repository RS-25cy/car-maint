<?php ob_start(); ?>
<h1>メンテ記録一覧（<?= htmlspecialchars($vehicle['name']) ?>）</h1>
<p>
  <a href="/?r=records_create&vehicle_id=<?= (int)$vehicle['id'] ?>">記録を追加</a> |
  <a href="/?r=vehicles">車両一覧へ</a>
</p>

<table border="1" cellpadding="6">
<tr>
  <th>ID</th><th>日付</th><th>走行距離</th><th>内容</th><th>費用</th><th>店名</th><th>メモ</th><th>操作</th>
</tr>

<?php foreach ($records as $r): ?>
    <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><?= htmlspecialchars($r['serviced_at']) ?></td>
        <td><?= htmlspecialchars($r['odometer']) ?></td>
        <td><?= htmlspecialchars($r['title']) ?></td>
        <td><?= htmlspecialchars($r['cost']) ?></td>
        <td><?= htmlspecialchars($r['shop']) ?></td>
        <td><?= htmlspecialchars($r['memo']) ?></td>
        <td>

            <a href="/?r=records_edit&id=<?= (int)$r['id'] ?>">編集</a> |
            <form method="post" action="/?r=records_delete" style="display:inline"
                onsubmit="return confirm('削除しますか？');">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button type="submit">削除</button>
            </form>
        </td>
    </tr>
  <?php endforeach; ?>
</table>

<?php $content = ob_get_clean(); $title='Records'; include __DIR__ . '/layout.php'; ?>
