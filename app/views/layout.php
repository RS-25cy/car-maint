<?php /* app/views/layout.php */ ?>
<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($title ?? 'App') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: sans-serif; margin: 0; padding: 0; }
    header, footer { background: #f9f9f9; padding: 10px; }
    header a { margin-right: 10px; }
    main { padding: 16px; }
  </style>
</head>
<body>
<header>
  <a href="/">Home</a> |
  <a href="/?r=vehicles">Vehicles</a> |
  <a href="/?r=vehicles_create">Add Vehicle</a> |
  <a href="/?r=logout">Logout</a>
</header>

<main>
  <?= $content ?? '' ?>
</main>

<footer>
  <small>&copy; <?= date('Y') ?> My Car Maintenance App</small>
</footer>
</body>
</html>
