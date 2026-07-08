<?php
$body_class = $body_class ?? 'text-gray-800 font-sans antialiased min-h-screen bg-gray-100 flex';
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

    <aside class="w-64 bg-gray-900 text-white min-h-screen flex flex-col shadow-xl flex-shrink-0">
        <div class="p-6 border-b border-gray-800">
            <h2 class="text-2xl font-bold text-indigo-400">Panel Admin</h2>
        </div>

        <nav class="flex-grow py-4">
            <ul class="space-y-1">
                <li>
                    <a href="/admin/dashboard" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors <?= $_SERVER['REQUEST_URI'] === '/admin/dashboard' ? 'bg-gray-800 text-white border-l-4 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">📊</span>
                        Dasbor
                    </a>
                </li>
                <li>
                    <a href="/admin/members" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors <?= $_SERVER['REQUEST_URI'] === '/admin/members' ? 'bg-gray-800 text-white border-l-4 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">👥</span>
                        Anggota
                    </a>
                </li>
                <li>
                    <a href="/admin/teams" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors <?= $_SERVER['REQUEST_URI'] === '/admin/teams' ? 'bg-gray-800 text-white border-l-4 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">🏆</span>
                        Tim
                    </a>
                </li>
                <li>
                    <a href="/admin/payments" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors <?= $_SERVER['REQUEST_URI'] === '/admin/payments' ? 'bg-gray-800 text-white border-l-4 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">💳</span>
                        Pembayaran
                    </a>
                </li>
                <li class="px-6 py-2 text-xs font-semibold uppercase tracking-wider text-gray-500">Karya Per Divisi</li>
                <?php
                $divs = ['FFR' => '🤖', 'LF' => '🏎️', 'PLC' => '💻', 'LKTI' => '📄'];
                $current_div = $_GET['div'] ?? '';
                foreach ($divs as $d => $icon): ?>
                    <li>
                        <a href="/admin/submissions?div=<?= $d ?>" class="flex items-center px-6 py-2 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/submissions') !== false && $current_div === $d ? 'bg-gray-800 text-white border-l-4 border-indigo-500' : '' ?>">
                            <span class="mr-3"><?= $icon ?></span>
                            <?= $d ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="p-4 border-t border-gray-800">
            <form action="/logout" method="POST" class="m-0">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Utils\Security::generateCsrfToken() ?? '') ?>">
                <button type="submit" class="w-full flex justify-center items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors text-sm font-medium">
                    <span class="mr-2">🚪</span> Keluar
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-grow flex flex-col min-h-screen overflow-x-hidden">
        <header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center z-10">
            <h1 class="text-xl font-semibold text-gray-800">
                <?= isset($page_title) ? htmlspecialchars($page_title) : 'Dasbor' ?>
            </h1>
            <div class="flex items-center text-sm font-medium text-gray-600">
                <span class="mr-2">👤</span> <?= htmlspecialchars(\App\Utils\Session::get('user_name') ?? 'Admin') ?>
            </div>
        </header>

        <div class="flex-grow p-8 bg-gray-50 overflow-y-auto">
            <?= $content ?? '' ?>
        </div>
    </main>

</body>

</html>