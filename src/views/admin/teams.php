<?php

/** @var array $teams */
/** @var string $page_title */

use App\Components\DataTable;
use App\Components\Dialog;
use App\Components\Icon; ?>

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
                [
                    'key' => 'leader',
                    'label' => 'Anggota 1 (Ketua)',
                    'render' => fn($row) => htmlspecialchars($row['leaderName']),
                ],
                [
                    'key' => 'leaderPhone',
                    'label' => 'No. HP Ketua',
                    'render' => fn($row) => $row['leaderPhone']
                        ? htmlspecialchars($row['leaderPhone'])
                        : '<span class="text-muted-foreground">-</span>',
                ],
                [
                    'key' => 'm1',
                    'label' => 'Anggota 2',
                    'render' => fn($row) => $row['m1Name']
                        ? htmlspecialchars($row['m1Name'])
                        : '<span class="text-muted-foreground">-</span>',
                ],
                [
                    'key' => 'm1Phone',
                    'label' => 'No. HP Anggota 2',
                    'render' => fn($row) => $row['m1Name'] && $row['m1Phone']
                        ? htmlspecialchars($row['m1Phone'])
                        : '<span class="text-muted-foreground">-</span>',
                ],
                [
                    'key' => 'm2',
                    'label' => 'Anggota 3',
                    'render' => fn($row) => $row['m2Name']
                        ? htmlspecialchars($row['m2Name'])
                        : '<span class="text-muted-foreground">-</span>',
                ],
                [
                    'key' => 'm2Phone',
                    'label' => 'No. HP Anggota 3',
                    'render' => fn($row) => $row['m2Name'] && $row['m2Phone']
                        ? htmlspecialchars($row['m2Phone'])
                        : '<span class="text-muted-foreground">-</span>',
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
            ->toolbarActions('<button type="button" onclick="openDialog(\'export-csv-dialog\')" class="inline-flex items-center justify-center gap-2 rounded-lg border border-border bg-green-700 px-3 py-2 text-xs font-semibold shadow-sm transition-all hover:bg-green-800 hover:text-white text-white"><span class="inline-block">' . Icon::make()->name('file-spreadsheet')->class('w-4 h-4') . '</span>Export CSV</button>')
            ->pageable()
            ->emptyText('Belum ada tim yang terdaftar.')
            ->render() ?>
    </div>
</div>

<?= (new Dialog())->id('export-csv-dialog')->title('Ekspor Data Tim')->width('max-w-md')->content(
    '<div>'
        . '<p class="text-sm text-gray-500">File akan berupa <strong class="font-semibold text-gray-700">.csv</strong> pastikan anda lakukan konversi ke <strong class="font-semibold text-green-700">Excel / Spreadsheet</strong></p>'
        . '<div class="flex items-center justify-center gap-4 mt-5">'
        . '<div class="flex flex-col items-center gap-2">'
        . '<span class="flex items-center justify-center w-16 h-16 rounded-2xl border border-gray-200 bg-gray-50">' . Icon::make()->name('file-text')->class('w-8 h-8 text-gray-500') . '</span>'
        . '<span class="text-xs font-bold text-gray-700">.csv</span>'
        . '</div>'
        . '<span class="text-gray-400">' . Icon::make()->name('arrow-right')->class('w-6 h-6') . '</span>'
        . '<div class="flex flex-col items-center gap-2">'
        . '<span class="flex items-center justify-center w-16 h-16 rounded-2xl border border-green-200 bg-green-50">' . Icon::make()->name('file-spreadsheet')->class('w-8 h-8 text-green-700') . '</span>'
        . '<span class="text-xs font-bold text-green-700">Excel / Spreadsheet</span>'
        . '</div>'
        . '</div>'
        . '<div class="flex justify-end gap-2 mt-6">'
        . '<button type="button" onclick="closeDialog(\'export-csv-dialog\')" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>'
        . '<a href="/admin/teams/export" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-green-700 hover:bg-green-800 rounded-xl transition-colors">' . Icon::make()->name('download')->class('w-4 h-4') . 'Download</a>'
        . '</div>'
        . '</div>'
) ?>