<?php /** @var array $submissions */ /** @var string $division */ /** @var string $page_title */

use App\Components\DataTable; ?>

<div class="rounded-xl border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-secondary text-secondary-foreground"><?= $division ?></span>
            <h3 class="text-2xl font-semibold leading-none tracking-tight">Karya Divisi <?= $division ?></h3>
        </div>
        <p class="text-sm text-muted-foreground">Daftar karya yang dikirim untuk divisi <?= $division ?>.</p>
    </div>
    <div class="p-6 pt-0">
        <?= DataTable::make()
            ->columns([
                ['key' => 'team', 'label' => 'Tim', 'render' => fn($row) =>
                    '<div class="font-medium">' . htmlspecialchars($row['team_name']) . '</div>' .
                    '<div class="text-sm text-muted-foreground">Ketua: ' . htmlspecialchars($row['leader']) . '</div>'
                ],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'file', 'label' => 'Karya', 'render' => fn($row) =>
                    $row['type'] === 'youtube_link'
                        ? '<a href="' . htmlspecialchars($row['file']) . '" target="_blank" class="text-primary underline-offset-4 hover:underline">' . htmlspecialchars($row['file']) . '</a>'
                        : '<a href="/uploads/submissions/' . htmlspecialchars($row['file']) . '" target="_blank" class="text-primary underline-offset-4 hover:underline">' . htmlspecialchars($row['file']) . '</a>'
                ],
                ['key' => 'uploaded', 'label' => 'Diupload', 'tdClass' => 'text-muted-foreground'],
            ])
            ->rows(array_map(fn($s) => [
                'team_name' => $s['team_name'],
                'leader' => $s['leaderName'],
                'email' => $s['email'],
                'file' => $s['value'],
                'type' => $s['type'],
                'uploaded' => $s['updated_at'] ?: $s['created_at'],
            ], $submissions))
            ->searchable()
            ->columnSelectable()
            ->pageable()
            ->emptyText('Belum ada karya untuk divisi ' . $division . '.')
            ->render() ?>
    </div>
</div>
