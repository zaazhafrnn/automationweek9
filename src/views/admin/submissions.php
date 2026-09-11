<?php

/** @var array $submissions */
/** @var string $csrf_token */
/** @var string $page_title */

use App\Components\Button;
use App\Components\DataTable;
use App\Components\Dialog;
use App\Components\Icon;

$typeLabels = [
    'abstract' => 'Abstrak',
    'full_paper' => 'Full Paper',
    'file' => 'Karya',
    'youtube_link' => 'Video YouTube',
    'originality' => 'Surat Orisinalitas',
    'approval' => 'Lembar Pengesahan',
];

$categoryLabels = [
    'gagasan' => 'Gagasan',
    'prototype' => 'Prototype',
];

$reviewableTypes = ['abstract', 'full_paper', 'file'];

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
        $kategori = $categoryLabels[$s['category'] ?? ''] ?? '-';
        $jenis = $typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type));
        $isPlc = false;
    }

    if ($type === 'youtube_link') {
        $karya = '<span data-tooltip="klik untuk melihat"><a href="' . htmlspecialchars((string) $s['value']) . '" target="_blank" class="text-primary underline-offset-4 hover:underline">' . htmlspecialchars((string) $s['value']) . '</a></span>';
    } else {
        $karya = '<span data-tooltip="klik untuk melihat"><a href="/uploads/submissions/' . htmlspecialchars((string) $s['value']) . '" target="_blank" class="text-primary underline-offset-4 hover:underline">' . htmlspecialchars((string) $s['value']) . '</a></span>';
    }
    $statusBadge = $statusMap[$s['status']] ?? htmlspecialchars($s['status']);

    $form = fn(string $action, string $label, string $variant, string $confirm) =>
    '<form action="/admin/submissions/process" method="POST" onsubmit="return confirm(\'' . $confirm . '\')">'
        . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
        . '<input type="hidden" name="submission_id" value="' . $s['id'] . '">'
        . '<input type="hidden" name="action" value="' . $action . '">'
        . Button::make()->label($label)->variant($variant)->size('sm')->tag('button')->attr('type', 'submit')
        . '</form>';

    $dialogHtml = '';
    if (!$isPlc && $type !== 'youtube_link' && in_array($type, $reviewableTypes, true) && $s['status'] === 'submitted') {
        $typeLabel = $typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type));
        $submissionTitle = isset($s['title']) && $s['title'] ? $s['title'] : $s['value'];
        $teamName = $s['team_name'];
        $approveDialogId = 'dialog-approve-' . $s['id'];
        $approveMessage = "Apakah Anda yakin ingin menandai $typeLabel <b>\"" . htmlspecialchars($submissionTitle) . "\"</b> dari tim <b>" . htmlspecialchars($teamName) . "</b> sebagai lolos seleksi?";
        $approveForm = '<form action="/admin/submissions/process" method="POST">'
            . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
            . '<input type="hidden" name="submission_id" value="' . $s['id'] . '">'
            . '<input type="hidden" name="action" value="approve">'
            . '<p class="text-sm text-gray-600 mb-6 break-words whitespace-normal w-full">' . $approveMessage . '</p>'
            . '<div class="flex justify-end space-x-2">'
            . '<button type="button" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors" onclick="closeDialog(\'' . $approveDialogId . '\')">Batal</button>'
            . '<button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700">Ya, Terima</button>'
            . '</div>'
            . '</form>';

        $dialogHtml .= Dialog::make()->id($approveDialogId)->title('Konfirmasi Terima')->width('max-w-md')->content($approveForm)->render();

        $rejectDialogId = 'dialog-reject-' . $s['id'];
        $rejectMessage = "Apakah Anda yakin ingin menolak $typeLabel <b>\"" . htmlspecialchars($submissionTitle) . "\"</b> dari tim <b>" . htmlspecialchars($teamName) . "</b>?";
        $rejectForm = '<form action="/admin/submissions/process" method="POST">'
            . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
            . '<input type="hidden" name="submission_id" value="' . $s['id'] . '">'
            . '<input type="hidden" name="action" value="reject">'
            . '<p class="text-sm text-gray-600 mb-6 break-words whitespace-normal w-full">' . $rejectMessage . '</p>'
            . '<div class="mb-2"><label class="block text-sm font-medium mb-1">Catatan (opsional)</label>'
            . '<textarea name="note" class="w-full border rounded p-1 min-h-[120px] md:min-h-[160px] text-base" placeholder="Opsional: beri catatan penolakan" rows="5" style="min-height:120px"></textarea></div>'
            . '<div class="flex justify-end space-x-2">'
            . '<button type="button" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors" onclick="closeDialog(\'' . $rejectDialogId . '\')">Batal</button>'
            . '<button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-red-700 rounded-lg hover:bg-red-800">Ya, Tolak</button>'
            . '</div>'
            . '</form>';
        $dialogHtml .= Dialog::make()->id($rejectDialogId)->title('Konfirmasi Tolak')->width('max-w-md')->content($rejectForm)->render();
    }


    $aksi = '<div class="mt-2 flex gap-1">';
    if ($isPlc && $s['status'] === 'submitted') {
        $aksi .= $form('qualify', 'Lolos', 'default', 'Loloskan tim ini ke babak berikutnya?')
            . $form('disqualify', 'Tidak Lolos', 'destructive', 'Tandai tim ini tidak lolos?');
    } elseif (!$isPlc && $type !== 'youtube_link' && in_array($type, $reviewableTypes, true) && $s['status'] === 'submitted') {
        $aksi .= '<button type="button" ' . Dialog::make()->id('dialog-approve-' . $s['id'])->getOpenAttr() . ' class="text-green-600 bg-gray-100 rounded p-1" data-tooltip="Terima">' . Icon::make()->name('check')->class('w-5 h-5') . '</button>'
            . '<button type="button" ' . Dialog::make()->id('dialog-reject-' . $s['id'])->getOpenAttr() . ' class="text-red-600 bg-gray-100 rounded p-1" data-tooltip="Tolak">' . Icon::make()->name('x')->class('w-5 h-5') . '</button>';
    } elseif ($s['status'] !== 'submitted') {
        $aksi .= $form('reset', 'Reset', 'secondary', 'Reset status menjadi menunggu?');
    }
    $aksi .= '</div>' . $dialogHtml;

    $rows[] = [
        'team_name' => htmlspecialchars($s['team_name']),
        'leader' => htmlspecialchars($s['leaderName']),
        'school' => htmlspecialchars($s['teamSchool'] ?? '-'),
        'kategori' => $kategori,
        'jenis' => $jenis,
        'karya' => $karya,
        'status' => $statusBadge,
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
                ['key' => 'leader', 'label' => 'Anggota 1 (Ketua)'],
                ['key' => 'school', 'label' => 'Asal Sekolah'],
                ['key' => 'kategori', 'label' => 'Kategori', 'render' => fn($row) => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-secondary text-secondary-foreground">' . $row['kategori'] . '</span>'],
                ['key' => 'jenis', 'label' => 'Jenis', 'render' => fn($row) => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-secondary text-secondary-foreground">' . $row['jenis'] . '</span>'],
                ['key' => 'karya', 'label' => 'Karya', 'sortable' => false, 'render' => fn($row) => $row['karya']],
                ['key' => 'status', 'label' => 'Status', 'render' => fn($row) => $row['status']],
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