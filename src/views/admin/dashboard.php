<?php

use App\Components\Icon;

/** @var string $user_name */
/** @var string|null $division */
/** @var int $total_users */
/** @var int $total_teams */
/** @var array $all_divisions */

$divisionMeta = [
    'LF'   => ['Line Follower', '/image/lf_icon.png', 'text-emerald-600'],
    'PLC'  => ['Programmable Logic Controller', '/image/plc_icon.png', 'text-accent'],
    'FFR'  => ['Fire Fighting Robot', '/image/ffr_icon.png', 'text-cyan-600'],
    'LKTI' => ['Lomba Karya Tulis Ilmiah', '/image/lkti_icon.png', 'text-yellow-500'],
    'PROG' => ['Algoritma Program', '/image/program_icon.png', 'text-amber-600'],
];

$isAll = $division === null;
$hero = $isAll
    ? ['title' => 'Semua Divisi', 'name' => 'Dashboard Admin', 'img' => null, 'color' => 'text-accent']
    : [
        'title' => $division,
        'name' => $divisionMeta[$division][0],
        'img' => $divisionMeta[$division][1],
        'color' => $divisionMeta[$division][2],
    ];

$shortcuts = [
    ['Akun', 'users', '/admin/accounts'],
    ['Tim', 'trophy', '/admin/teams'],
];
if ($isAll || in_array($division, ['PLC', 'LKTI'], true)) {
    $shortcuts[] = ['Karya', 'file-text', '/admin/submissions'];
}
?>

<div class="mx-auto space-y-6">
    <div class="rounded-2xl border bg-card shadow-sm p-6 sm:p-8 flex items-center gap-5 sm:gap-8">
        <?php if ($hero['img']): ?>
            <img src="<?= $hero['img'] ?>" alt="<?= $hero['title'] ?>" class="w-20 h-20 sm:w-24 sm:h-24 object-contain shrink-0">
        <?php else: ?>
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-accent/10 text-accent flex items-center justify-center shrink-0">
                <?= Icon::make()->name('users')->class('w-10 h-10 sm:w-12 sm:h-12') ?>
            </div>
        <?php endif; ?>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-muted-foreground"><?= $isAll ? 'Dashboard Admin' : 'Divisi' ?></p>
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight mt-1"><?= $hero['title'] ?></h1>
            <p class="text-sm text-muted-foreground mt-1 truncate"><?= $hero['name'] ?></p>
        </div>
        <div class="text-right shrink-0">
            <p class="text-4xl sm:text-5xl font-extrabold tracking-tight leading-none <?= $hero['color'] ?>"><?= $total_teams ?></p>
            <p class="text-xs font-semibold text-muted-foreground mt-1">Tim terdaftar</p>
        </div>
    </div>

    <?php if (!$isAll): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
            <div class="rounded-xl border bg-card shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">Total Peserta</p>
                        <p class="text-3xl font-bold tracking-tight mt-1"><?= $total_users ?></p>
                    </div>
                    <div class="p-3 rounded-xl bg-accent/10 text-accent">
                        <?= Icon::make()->name('users')->class('w-6 h-6') ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div>
            <h3 class="text-sm font-semibold mb-3">Tim per Divisi</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <?php foreach ($divisionMeta as $code => [$name, $img, $color]): ?>
                    <div class="rounded-xl border bg-card shadow-sm p-4 flex items-center gap-3">
                        <img src="<?= $img ?>" alt="<?= $code ?>" class="w-10 h-10 object-contain shrink-0">
                        <div class="min-w-0">
                            <p class="text-2xl font-bold tracking-tight leading-none <?= $color ?>"><?= $all_divisions[$code] ?? 0 ?></p>
                            <p class="text-xs font-semibold truncate" title="<?= $name ?>"><?= $code ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div>
        <h3 class="text-sm font-semibold mb-3">Kelola</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <?php foreach ($shortcuts as [$label, $icon, $route]): ?>
                <a href="<?= $route ?>" class="group flex items-center justify-between rounded-xl border bg-card shadow-sm px-4 py-3.5 no-underline hover:border-accent transition-colors">
                    <span class="flex items-center gap-2.5 text-sm font-semibold text-foreground">
                        <?= Icon::make()->name($icon)->class('w-4 h-4') ?>
                        <?= $label ?>
                    </span>
                    <?= Icon::make()->name('chevron-right')->class('w-4 h-4 text-muted-foreground group-hover:translate-x-0.5 transition-colors') ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>