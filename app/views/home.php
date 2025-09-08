<?php ob_start(); ?>
<h1>Home（ログイン済み）</h1>
<p><a href="/?r=vehicles">車両一覧へ</a></p>
<p><a href="/?r=vehicles_create">+ 車両を追加</a></p>
<?php
$content = ob_get_clean();
$title = 'Home';
include __DIR__.'/layout.php';