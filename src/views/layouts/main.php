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
        function showToast(msg, variant) {
            variant = variant || 'error';
            var styles = {
                success: 'border-green-500/50',
                error: 'border-red-500/50',
                warning: 'border-amber-500/50',
                info: 'border-blue-500/50'
            };
            var icons = {
                success: 'check',
                error: 'alert-circle',
                warning: 'alert-circle',
                info: 'alert-circle'
            };
            var iconColors = {
                success: 'text-green-400',
                error: 'text-red-400',
                warning: 'text-amber-400',
                info: 'text-blue-400'
            };
            var t = document.createElement('div');
            t.id = 'flash-toast';
            t.setAttribute('role', 'alert');
            t.className = 'pointer-events-auto relative w-full rounded-xl border ' + styles[variant] + ' bg-[#1e1d1a] px-4 py-3 pl-10 text-sm text-white shadow-lg opacity-0 -translate-x-2 transition-all duration-300';
            t.innerHTML = '<svg class="w-4 h-4 absolute left-4 top-4 ' + iconColors[variant] + '" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></svg>'
                + '<p class="pr-6 font-semibold">' + msg + '</p>'
                + '<button type="button" data-toast-close aria-label="Tutup" class="absolute right-3 top-3 text-gray-500 hover:text-white cursor-pointer transition-colors">\u2715</button>';
            var root = document.getElementById('toast-root');
            root.appendChild(t);
            requestAnimationFrame(() => t.classList.remove('opacity-0', '-translate-x-2'));
            var close = () => {
                t.classList.add('opacity-0', '-translate-x-2');
                setTimeout(() => t.remove(), 300);
            };
            t.querySelector('[data-toast-close]').addEventListener('click', close);
            setTimeout(close, 5000);
        }

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