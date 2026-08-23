<?php

use App\Components\Icon;

/** @var int $total_users */
/** @var int $total_teams */
/** @var array $divisions */

$divisionMeta = [
    'LF'   => ['Line Follower', '/image/lf_icon.png', 'text-emerald-600'],
    'PLC'  => ['Programmable Logic Controller', '/image/plc_icon.png', 'text-accent'],
    'FFR'  => ['Fire Fighting Robot', '/image/ffr_icon.png', 'text-cyan-600'],
    'LKTI' => ['Lomba Karya Tulis Ilmiah', '/image/lkti_icon.png', 'text-yellow-500'],
    'PROG' => ['Algoritma Program', '/image/program_icon.png', 'text-amber-600'],
];

$shortcuts = [
    ['Akun', 'users', '/admin/accounts'],
    ['Tim', 'trophy', '/admin/teams'],
    ['Pembayaran', 'credit-card', '/admin/payments'],
    ['Karya', 'file-text', '/admin/submissions'],
];
?>

<div class="mx-auto space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-xl border bg-card shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">Total Pengguna</p>
                    <p class="text-3xl font-bold tracking-tight mt-1"><?= $total_users ?></p>
                </div>
                <div class="p-3 rounded-xl bg-accent/10 text-accent">
                    <?= Icon::make()->name('users')->class('w-6 h-6') ?>
                </div>
            </div>
        </div>
        <div class="rounded-xl border bg-card shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">Total Tim</p>
                    <p class="text-3xl font-bold tracking-tight mt-1"><?= $total_teams ?></p>
                </div>
                <div class="p-3 rounded-xl bg-yellow-500/10 text-yellow-500">
                    <?= Icon::make()->name('trophy')->class('w-6 h-6') ?>
                </div>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-sm font-semibold mb-3">Tim per Divisi</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <?php foreach ($divisionMeta as $code => [$name, $img, $color]): ?>
                <div class="rounded-xl border bg-card shadow-sm p-4 flex items-center gap-3">
                    <img src="<?= $img ?>" alt="<?= $code ?>" class="w-10 h-10 object-contain shrink-0">
                    <div class="min-w-0">
                        <p class="text-2xl font-bold tracking-tight leading-none <?= $color ?>"><?= $divisions[$code] ?? 0 ?></p>
                        <p class="text-xs font-semibold truncate" title="<?= $name ?>"><?= $code ?></p>
                        <p class="text-xs truncate"><?= $name ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div>
        <h3 class="text-sm font-semibold mb-3">Kelola</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <?php foreach ($shortcuts as [$label, $icon, $route]): ?>
                <a href="<?= $route ?>" class="group flex items-center justify-between rounded-xl border bg-card shadow-sm px-4 py-3.5 no-underline hover:border-accent transition-colors">
                    <span class="flex items-center gap-2.5 text-sm font-semibold text-foreground">
                        <?= Icon::make()->name($icon)->class('w-4 h-4') ?>
                        <?= $label ?>
                    </span>
                    <?= Icon::make()->name('chevron-right')->class('w-4 h-4 text-muted-foreground group-hover:translate-x-0.5 transition-transform') ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>