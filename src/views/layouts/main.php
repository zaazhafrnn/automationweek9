<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="text-gray-800 font-sans antialiased min-h-screen flex flex-col">
    <main class="flex-grow w-full max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <?= $content ?? '' ?>
    </main>
</body>
</html>
