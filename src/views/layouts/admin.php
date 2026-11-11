<?php

use App\Components\Sidebar;

$body_class = $body_class ?? 'antialiased';

$uri = $_SERVER['REQUEST_URI'];
$div = $_GET['div'] ?? '';
$type = $_GET['type'] ?? '';

$user_name = (string) \App\Utils\Session::get('user_name');
$isSuperAdmin = stripos($user_name, 'superadmin') !== false;
$isLkti = stripos($user_name, 'lkti') !== false || $isSuperAdmin;
$isPlc = stripos($user_name, 'plc') !== false || $isSuperAdmin;
$isBendahara = stripos($user_name, 'bendahara') !== false || stripos($user_name, 'keuangan') !== false || $isSuperAdmin;

$sidebarItems = [];
$sidebarItems[] = ['label' => 'Dasbor', 'icon' => 'home', 'route' => '/admin/dashboard', 'active' => $uri === '/admin/dashboard'];
$sidebarItems[] = ['label' => 'Akun', 'icon' => 'users', 'route' => '/admin/accounts', 'active' => $uri === '/admin/accounts'];
$sidebarItems[] = ['label' => 'Tim', 'icon' => 'trophy', 'route' => '/admin/teams', 'active' => $uri === '/admin/teams'];
if ($isBendahara) {
    $sidebarItems[] = ['label' => 'Pembayaran', 'icon' => 'credit-card', 'route' => '/admin/payments', 'active' => $uri === '/admin/payments'];
}

if ($isLkti) {
    $sidebarItems[] = ['label' => 'Upload karya', 'header' => true];
    foreach (
        [
            'full_paper' => 'Full Paper',
            'abstract' => 'Abstrak',
        ] as $t => $label
    ) {
        $sidebarItems[] = [
            'label' => $label,
            'icon' => 'file',
            'route' => "/admin/submissions?div=LKTI&type=$t",
            'active' => strpos($uri, '/admin/submissions') !== false && $type === $t,
        ];
    }
}

if ($isPlc) {
    $sidebarItems[] = ['label' => 'PLC', 'header' => true];
    foreach ([1 => 'Round 1', 2 => 'Round 2', 3 => 'Round 3 (Final)'] as $r => $label) {
        $sidebarItems[] = [
            'label' => $label,
            'icon' => 'file-text',
            'route' => "/admin/plc-submissions?round=$r",
            'active' => $uri === '/admin/plc-submissions' && (int) ($_GET['round'] ?? 1) === $r,
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?> - Admin</title>
    <link rel="icon" type="image/png" href="/image/faveicon.png">
    <link rel="stylesheet" href="/css/app.css">
</head>

<body class="<?= htmlspecialchars($body_class) ?>">

    <div class="group/sidebar-wrapper flex min-h-svh w-full">

        <?= Sidebar::make()->title('Admin Dashboard AW9')->items($sidebarItems) ?>

        <main class="flex flex-1 flex-col">
            <header class="flex h-10 shrink-0 items-center gap-2 border-b bg-background px-4">
                <nav class="flex items-center text-sm font-medium text-muted-foreground gap-2">
                    <a href="/admin/dashboard" class="hover:text-foreground transition-colors">Admin</a>
                    <span class="text-muted-foreground/50">/</span>
                    <span class="text-foreground">
                        <?= isset($page_title) ? htmlspecialchars($page_title) : 'Dasbor' ?>
                    </span>
                </nav>
                <div class="ml-auto text-sm font-medium text-muted-foreground">
                    <?= htmlspecialchars(\App\Utils\Session::get('user_name') ?? 'Admin') ?>
                </div>
            </header>
            <div class="flex-1 overflow-y-auto p-4">
                <?= $content ?? '' ?>
            </div>
        </main>

    </div>

    <div id="page-loader" class="fixed inset-0 z-[100] hidden items-center justify-center bg-background/70 backdrop-blur-sm">
        <div class="h-10 w-10 animate-spin rounded-full border-4 border-primary/20 border-t-primary"></div>
    </div>

    <script>
        (function () {
            var loader = document.getElementById('page-loader');
            if (!loader) return;
            var show = function () {
                loader.classList.remove('hidden');
                loader.classList.add('flex');
            };
            var hide = function () {
                loader.classList.add('hidden');
                loader.classList.remove('flex');
            };
            window.addEventListener('pageshow', hide);
            document.addEventListener('click', function (e) {
                var a = e.target.closest('a[href]');
                if (!a || a.target === '_blank' || a.hasAttribute('download') || a.hasAttribute('data-no-loader')) return;
                var url;
                try { url = new URL(a.href, location.href); } catch (err) { return; }
                if (url.origin !== location.origin) return;
                if (url.pathname === location.pathname && url.search === location.search) return;
                if (url.protocol !== 'http:' && url.protocol !== 'https:') return;
                show();
            });
            document.addEventListener('submit', function (e) {
                if (!e.defaultPrevented) show();
            });
        })();
    </script>

</body>

</html>