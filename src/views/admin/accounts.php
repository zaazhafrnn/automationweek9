<?php

/** @var array $members */
/** @var string $page_title */

use App\Components\DataTable; ?>

<div class="rounded-xl border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="text-2xl font-semibold leading-none tracking-tight">Semua Akun</h3>
        <p class="text-sm text-muted-foreground">Daftar semua akun yang terdaftar.</p>
    </div>
    <div class="p-6 pt-0">
        <?= DataTable::make()
            ->columns([
                ['key' => 'name', 'label' => 'Nama'],
                ['key' => 'email', 'label' => 'Email'],
            ])
            ->rows(array_map(fn($m) => ['name' => $m['name'], 'email' => $m['email']], $members))
            ->searchable()
            ->columnSelectable()
            ->pageable()
            ->emptyText('Belum ada akun yang terdaftar.')
            ->render() ?>
    </div>
</div>