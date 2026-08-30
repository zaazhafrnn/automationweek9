<?php
$body_class = $body_class ?? 'text-gray-800 font-sans antialiased min-h-screen flex flex-col bg-gray-50';
$main_class = $main_class ?? 'flex-grow w-full';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Automation Week 9 2026 PPNS — kompetisi otomasi & teknologi: Line Follower(LF), Programmable Logic Controller(PLC), Fire Fighting Robot(FFR), Lomba Karya Tulis Ilmiah(LKTI), dan Algoritma Program. Total hadiah puluhan juta rupiah & free pass teknik otomasi. Dibuka 24 Agustus hingga 1 Oktober 2026 Daftarkan tim Anda sekarang!">
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
            var iconPaths = {
                success: '<path d="M20 6 9 17l-5-5"/>',
                error: '<circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/>',
                warning: '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
                info: '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>'
            };
            t.innerHTML = '<svg class="w-4 h-4 absolute left-4 top-4 ' + iconColors[variant] + '" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + iconPaths[variant] + '</svg>' +
                '<p class="pr-6 font-semibold">' + msg + '</p>' +
                '<button type="button" data-toast-close aria-label="Tutup" class="absolute right-3 top-3 text-gray-500 hover:text-white cursor-pointer transition-colors">\u2715</button>';
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

        function presentToast(el, duration) {
            const root = document.getElementById('toast-root');
            root.appendChild(el);
            requestAnimationFrame(() => el.classList.remove('opacity-0', '-translate-x-2'));
            const close = () => {
                el.classList.add('opacity-0', '-translate-x-2');
                setTimeout(() => el.remove(), 300);
            };
            el.querySelector('[data-toast-close]')?.addEventListener('click', close);
            setTimeout(close, duration || 90000);
        }

        const trigger = document.getElementById('flash-toast');
        if (trigger) presentToast(trigger);
    </script>
</body>

</html>