<?php
$body_class = $body_class ?? 'text-gray-800 font-sans antialiased min-h-screen flex flex-col bg-gray-50';
$main_class = $main_class ?? 'flex-grow w-full';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?></title>
    <link rel="stylesheet" href="/css/app.css">
</head>

<body class="<?= htmlspecialchars($body_class) ?>">
    <main class="<?= htmlspecialchars($main_class) ?>">
        <?= $content ?? '' ?>
    </main>
</body>

</html>