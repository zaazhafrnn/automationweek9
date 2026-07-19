<?php

use App\Components\Sidebar;

$body_class = $body_class ?? 'antialiased';

$uri = $_SERVER['REQUEST_URI'];
$div = $_GET['div'] ?? '';

$sidebarItems = [];
$sidebarItems[] = ['label' => 'Dasbor', 'icon' => 'home', 'route' => '/admin/dashboard', 'active' => $uri === '/admin/dashboard'];
$sidebarItems[] = ['label' => 'Akun', 'icon' => 'users', 'route' => '/admin/members', 'active' => $uri === '/admin/members'];
$sidebarItems[] = ['label' => 'Tim', 'icon' => 'trophy', 'route' => '/admin/teams', 'active' => $uri === '/admin/teams'];
$sidebarItems[] = ['label' => 'Pembayaran', 'icon' => 'credit-card', 'route' => '/admin/payments', 'active' => $uri === '/admin/payments'];
$sidebarItems[] = ['label' => 'Upload karya', 'header' => true];

foreach (['FFR' => 'settings', 'LF' => 'settings', 'PLC' => 'settings', 'LKTI' => 'file'] as $d => $icon) {
    $sidebarItems[] = [
        'label' => $d,
        'icon' => $icon,
        'route' => "/admin/submissions?div=$d",
        'active' => strpos($uri, '/admin/submissions') !== false && $div === $d,
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?> - Admin</title>
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

</body>

</html>