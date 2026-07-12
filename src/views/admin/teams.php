<?php /** @var array $teams */ /** @var string $page_title */

use App\Components\DataTable; ?>

<div class="rounded-xl border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="text-2xl font-semibold leading-none tracking-tight">Semua Tim Terdaftar</h3>
        <p class="text-sm text-muted-foreground">Daftar semua tim yang terdaftar.</p>
    </div>
    <div class="p-6 pt-0">
        <?= DataTable::make()
            ->columns([
                ['key' => 'name', 'label' => 'Nama Tim'],
                ['key' => 'school', 'label' => 'Asal Sekolah'],
                ['key' => 'divisi', 'label' => 'Kategori', 'render' => fn($row) => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-secondary text-secondary-foreground">' . htmlspecialchars($row['divisi']) . '</span>'],
                ['key' => 'leader', 'label' => 'Ketua', 'render' => fn($row) =>
                    '<div class="font-medium">' . htmlspecialchars($row['leaderName']) . '</div>' .
                    ($row['leaderPhone'] ? '<div class="mt-1 text-sm text-muted-foreground">' . htmlspecialchars($row['leaderPhone']) . '</div>' : '')
                ],
                ['key' => 'm1', 'label' => 'Anggota 1', 'render' => fn($row) =>
                    $row['m1Name']
                        ? '<div class="font-medium">' . htmlspecialchars($row['m1Name']) . '</div><div class="mt-1 text-sm text-muted-foreground">' . htmlspecialchars($row['m1Phone'] ?? '') . '</div>'
                        : '<span class="text-muted-foreground">-</span>'
                ],
                ['key' => 'm2', 'label' => 'Anggota 2', 'render' => fn($row) =>
                    $row['m2Name']
                        ? '<div class="font-medium">' . htmlspecialchars($row['m2Name']) . '</div><div class="mt-1 text-sm text-muted-foreground">' . htmlspecialchars($row['m2Phone'] ?? '') . '</div>'
                        : '<span class="text-muted-foreground">-</span>'
                ],
            ])
            ->rows(array_map(fn($t) => [
                'name' => $t['name'],
                'school' => $t['teamSchool'] ?? '-',
                'divisi' => $t['division'],
                'leaderName' => $t['leaderName'],
                'leaderPhone' => $t['leaderPhoneNumber'] ?? '',
                'm1Name' => $t['firstMemberName'] ?? '',
                'm1Phone' => $t['firstMemberPhoneNumber'] ?? '',
                'm2Name' => $t['secondMemberName'] ?? '',
                'm2Phone' => $t['secondMemberPhoneNumber'] ?? '',
            ], $teams))
            ->searchable()
            ->columnSelectable()
            ->pageable()
            ->emptyText('Belum ada tim yang terdaftar.')
            ->render() ?>
    </div>
</div>
