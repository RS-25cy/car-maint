<?php ob_start();?>
<h1>車両一覧</h1>
<p><a href="/?r=vehicles_create">+車両の追加</a></p>
<table border="1" cellpadding="6">
    <tr>
        <th>ID</th>
        <th>名前</th>
        <th>年式</th>
        <th>グレード</th>
        <th>ナンバー</th>
    </tr>
    <?php foreach($vehicles as $v): ?>
        <tr>
            <td><?=(int)$v['id']?></td>
            <td><?=htmlspecialchars($v['name']) ?></td>
            <td><?=htmlspecialchars($v['year']) ?></td>
            <td><?=htmlspecialchars($v['grade']) ?></td>
            <td><?=htmlspecialchars($v['plate_no']) ?></td>
            <td>
            <a href="/?r=vehicles_edit&id=<?= (int)$v['id'] ?>">編集</a>
            |
                <form action="/?r=vehicles_delete" method="post" style="display:inline"
                        onsubmit="return confirm('本当に削除しますか？');">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                    <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                    <button type="submit">削除</button>
                    <a href="/?r=records&vehicle_id=<?= (int)$v['id'] ?>">記録</a>

                </form>
            </td>

        </tr>
    <?php endforeach;?>
</table>
<p><a href="javascript:history.back()">戻る</a></p>

<?php 
$content = ob_get_clean();
$title = 'Vehicles';
include __DIR__ . '/layout.php';
?>