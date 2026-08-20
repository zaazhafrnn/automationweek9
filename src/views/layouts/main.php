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
    <link rel="icon" type="image/png" href="/image/faveicon.png">
    <link rel="stylesheet" href="/css/app.css">
</head>

<body class="<?= htmlspecialchars($body_class) ?>">
    <main class="<?= htmlspecialchars($main_class) ?>">
        <?= $content ?? '' ?>
    </main>

    <div id="toast-root" class="pointer-events-none fixed bottom-4 left-4 z-50 flex w-[calc(100vw-2rem)] max-w-sm flex-col gap-2"></div>

    <script>
        (async () => {
            const trigger = document.getElementById('flash-toast');
            if (!trigger) return;

            const root = document.getElementById('toast-root');
            root.appendChild(trigger);

            requestAnimationFrame(() => trigger.classList.remove('opacity-0', '-translate-x-2'));

            const close = () => {
                trigger.classList.add('opacity-0', '-translate-x-2');
                setTimeout(() => trigger.remove(), 300);
            };
            trigger.querySelector('[data-toast-close]')?.addEventListener('click', close);
            setTimeout(close, 90000);
        })();
    </script>
</body>

</html>