<?php

/** @var array $submissions */
/** @var string $csrf_token */
/** @var string $page_title */

use App\Components\Button;
use App\Components\DataTable;

$typeLabels = [
    'abstract' => 'Abstrak',
    'full_paper' => 'Full Paper',
    'file' => 'Karya',
    'youtube_link' => 'Video YouTube',
];

$statusMap = [
    'submitted' => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800">Menunggu</span>',
    'approved' => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800">Disetujui</span>',
    'rejected' => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-red-100 text-red-800">Ditolak</span>',
    'qualified' => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800">Lolos</span>',
    'not_qualified' => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-red-100 text-red-800">Tidak Lolos</span>',
];

$rows = [];
foreach ($submissions as $s) {
    $type = $s['type'];
    if (str_starts_with($type, 'plc_')) {
        $kategori = 'PLC ' . trim(str_replace(['plc_', '_'], ['', ' '], $type));
        $isPlc = true;
    } else {
        $kategori = $typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type));
        $isPlc = false;
    }

    if ($type === 'youtube_link') {
        $karya = '<a href="' . htmlspecialchars((string) $s['value']) . '" target="_blank" class="text-primary underline-offset-4 hover:underline">' . htmlspecialchars((string) $s['value']) . '</a>';
    } else {
        $karya = '<a href="/uploads/submissions/' . htmlspecialchars((string) $s['value']) . '" target="_blank" class="text-primary underline-offset-4 hover:underline">' . htmlspecialchars((string) $s['value']) . '</a>';
    }

    $form = fn(string $action, string $label, string $variant, string $confirm) =>
    '<form action="/admin/submissions/process" method="POST" onsubmit="return confirm(\'' . $confirm . '\')">'
        . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
        . '<input type="hidden" name="submission_id" value="' . $s['id'] . '">'
        . '<input type="hidden" name="action" value="' . $action . '">'
        . Button::make()->label($label)->variant($variant)->size('sm')->tag('button')->attr('type', 'submit')
        . '</form>';

    $aksi = ($statusMap[$s['status']] ?? htmlspecialchars($s['status'])) . '<div class="mt-2 space-y-2">';

    if ($isPlc && $s['status'] === 'submitted') {
        $aksi .= $form('qualify', 'Lolos', 'default', 'Loloskan tim ini ke babak berikutnya?')
            . $form('disqualify', 'Tidak Lolos', 'destructive', 'Tandai tim ini tidak lolos?');
    } elseif (!$isPlc && $type !== 'youtube_link' && $s['status'] === 'submitted') {
        $aksi .= $form('approve', 'Setujui', 'default', 'Setujui pengumpulan ini?')
            . $form('reject', 'Tolak', 'destructive', 'Tolak pengumpulan ini?');
    } elseif ($s['status'] !== 'submitted') {
        $aksi .= $form('reset', 'Reset', 'secondary', 'Reset status menjadi menunggu?');
    }

    $aksi .= '</div>';

    $rows[] = [
        'team_name' => htmlspecialchars($s['team_name']),
        'leader' => htmlspecialchars($s['leaderName']),
        'school' => htmlspecialchars($s['teamSchool'] ?? '-'),
        'kategori' => $kategori,
        'karya' => $karya,
        'uploaded' => date('d M Y H:i', strtotime($s['updated_at'] ?: $s['created_at'])),
        'aksi' => $aksi,
    ];
}
?>

<div class="rounded-xl border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="text-2xl font-semibold leading-none tracking-tight">Pengumpulan Karya</h3>
        <p class="text-sm text-muted-foreground">Kelola abstrak, full paper, dan karya dari setiap tim.</p>
    </div>
    <div class="p-6 pt-0">
        <?= DataTable::make()
            ->columns([
                ['key' => 'team_name', 'label' => 'Tim'],
                ['key' => 'leader', 'label' => 'Ketua'],
                ['key' => 'school', 'label' => 'Asal Sekolah'],
                ['key' => 'kategori', 'label' => 'Kategori', 'render' => fn($row) => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-secondary text-secondary-foreground">' . $row['kategori'] . '</span>'],
                ['key' => 'karya', 'label' => 'Karya', 'sortable' => false, 'render' => fn($row) => $row['karya']],
                ['key' => 'uploaded', 'label' => 'Diupload', 'tdClass' => 'text-muted-foreground'],
                ['key' => 'aksi', 'label' => 'Aksi', 'sortable' => false, 'render' => fn($row) => $row['aksi']],
            ])
            ->searchable()
            ->columnSelectable()
            ->pageable()
            ->rows($rows)
            ->emptyText('Belum ada pengumpulan karya.')
            ->render() ?>
    </div>
</div>