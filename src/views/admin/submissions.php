<?php

/** @var array $submissions */
/** @var string $csrf_token */
/** @var string $page_title */

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
    'approved' => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800">Lolos</span>',
    'rejected' => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-red-100 text-red-800">Tidak Lolos</span>',
    'qualified' => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800">Lolos Seleksi</span>',
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

    $fullName = htmlspecialchars((string) ($s['original_name'] ?: $s['value']), ENT_QUOTES);
    $karya = '<span data-tooltip="' . $fullName . '"><a href="/uploads/submissions/' . htmlspecialchars((string) $s['value']) . '" target="_blank" class="text-primary underline-offset-4 hover:underline inline-block align-middle max-w-[220px] truncate text-xs md:text-sm">' . htmlspecialchars((string) ($s['original_name'] ?: $s['value'])) . '</a></span>';
    $statusBadge = $statusMap[$s['status']] ?? htmlspecialchars($s['status']);

    $dialogHtml = '';
    if (!$isPlc && $type !== 'youtube_link' && in_array($type, $reviewableTypes, true) && $s['status'] === 'submitted') {
        $typeLabel = $typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type));
        $submissionTitle = isset($s['title']) && $s['title'] ? $s['title'] : $s['value'];
        $teamName = $s['team_name'];
        $approveDialogId = 'dialog-approve-' . $s['id'];
        $approveMessage = "Apakah Anda yakin ingin meloloskan $typeLabel <b>\"" . htmlspecialchars($submissionTitle) . "\"</b> dari tim <b>" . htmlspecialchars($teamName) . "</b> ke tahap berikutnya?";
        $approveForm = '<form action="/admin/submissions/process" method="POST">'
            . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
            . '<input type="hidden" name="submission_id" value="' . $s['id'] . '">'
            . '<input type="hidden" name="action" value="approve">'
            . '<p class="text-sm text-gray-600 mb-6 break-words whitespace-normal w-full">' . $approveMessage . '</p>'
            . '<div class="flex justify-end space-x-2">'
            . '<button type="button" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors" onclick="closeDialog(\'' . $approveDialogId . '\')">Batal</button>'
            . '<button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700">Ya, Loloskan</button>'
            . '</div>'
            . '</form>';

        $dialogHtml .= Dialog::make()->id($approveDialogId)->title('Loloskan Karya?')->width('max-w-md')->content($approveForm)->render();

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
        $dialogHtml .= Dialog::make()->id($rejectDialogId)->title('Tolak Karya?')->width('max-w-md')->content($rejectForm)->render();
    }

    $resetDialogId = 'dialog-reset-' . $s['id'];
    $resetForm = '<form action="/admin/submissions/process" method="POST">'
        . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
        . '<input type="hidden" name="submission_id" value="' . $s['id'] . '">'
        . '<input type="hidden" name="action" value="reset">'
        . '<p class="text-sm text-gray-600 mb-6 break-words whitespace-normal w-full">Reset status karya tim <b>' . htmlspecialchars($s['team_name']) . '</b> menjadi menunggu?</p>'
        . '<div class="flex justify-end space-x-2">'
        . '<button type="button" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors" onclick="closeDialog(\'' . $resetDialogId . '\')">Batal</button>'
        . '<button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-amber-600 rounded-lg hover:bg-amber-700">Ya, Reset</button>'
        . '</div>'
        . '</form>';
    $dialogHtml .= Dialog::make()->id($resetDialogId)->title('Reset Status')->width('max-w-md')->content($resetForm)->render();


    $dis = 'opacity-40 cursor-not-allowed pointer-events-none';
    $btnBase = 'rounded p-1.5 transition-colors disabled:opacity-40 disabled:cursor-not-allowed';
    $iconForm = function (string $action, string $icon, string $color, string $tip, string $confirm, bool $enabled) use ($csrf_token, $s, $btnBase, $dis) {
        return '<form action="/admin/submissions/process" method="POST"' . ($enabled ? ' onsubmit="return confirm(\'' . $confirm . '\')"' : '') . '>'
            . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
            . '<input type="hidden" name="submission_id" value="' . $s['id'] . '">'
            . '<input type="hidden" name="action" value="' . $action . '">'
            . '<button type="submit"' . ($enabled ? '' : ' disabled') . ' class="' . $color . ' bg-gray-100 hover:bg-gray-200 ' . $btnBase . ($enabled ? '' : ' ' . $dis) . '" data-tooltip="' . $tip . '">' . Icon::make()->name($icon)->class('w-5 h-5') . '</button>'
            . '</form>';
    };

    $isReviewable = !$isPlc && $type !== 'youtube_link' && in_array($type, $reviewableTypes, true);
    $isPending = $s['status'] === 'submitted';
    $isDone = !$isPending;
    $canReview = ($isPlc || $isReviewable) && $isPending;

    $aksi = '<div class="flex gap-1 items-center">';
    if ($canReview && $isPlc) {
        $aksi .= $iconForm('qualify', 'check', 'text-green-600', 'Loloskan karya', 'Loloskan tim ini ke babak berikutnya?', true);
        $aksi .= $iconForm('disqualify', 'x', 'text-red-600', 'Tolak karya', 'Tandai tim ini tidak lolos?', true);
    } else {
        $aksi .= '<button type="button" ' . ($canReview ? Dialog::make()->id('dialog-approve-' . $s['id'])->getOpenAttr() : '') . ' ' . ($canReview ? '' : 'disabled') . ' class="text-green-600 bg-gray-100 hover:bg-gray-200 ' . $btnBase . ($canReview ? '' : ' ' . $dis) . '" data-tooltip="Loloskan karya">' . Icon::make()->name('check')->class('w-5 h-5') . '</button>'
            . '<button type="button" ' . ($canReview ? Dialog::make()->id('dialog-reject-' . $s['id'])->getOpenAttr() : '') . ' ' . ($canReview ? '' : 'disabled') . ' class="text-red-600 bg-gray-100 hover:bg-gray-200 ' . $btnBase . ($canReview ? '' : ' ' . $dis) . '" data-tooltip="Tolak karya">' . Icon::make()->name('x')->class('w-5 h-5') . '</button>';
    }
    $aksi .= '<button type="button" ' . ($isDone ? Dialog::make()->id($resetDialogId)->getOpenAttr() : '') . ' ' . ($isDone ? '' : 'disabled') . ' class="text-amber-600 bg-gray-100 hover:bg-gray-200 ' . $btnBase . ($isDone ? '' : ' ' . $dis) . '" data-tooltip="Reset Status">' . Icon::make()->name('rotate-cw')->class('w-5 h-5') . '</button>';
    $fileUrl = $type === 'youtube_link' ? htmlspecialchars((string) $s['value']) : '/uploads/submissions/' . htmlspecialchars((string) $s['value']);
    $aksi .= '<details class="relative inline-block overflow-visible" data-kebab-menu>'
        . '<summary class="list-none cursor-pointer text-gray-700 bg-gray-100 hover:bg-gray-200 ' . $btnBase . ' [&::-webkit-details-marker]:hidden" data-tooltip="Lainnya">' . Icon::make()->name('more-horizontal')->class('w-5 h-5 rotate-90') . '</summary>'
        . '<div class="absolute right-10 z-50 top-10 -translate-y-full min-w-[170px] overflow-visible rounded-xl border border-border bg-card p-1.5 shadow-lg text-sm">'
        . '<a href="' . $fileUrl . '" target="_blank" class="w-full flex items-center gap-2 px-2.5 py-2 rounded-lg hover:bg-secondary/50 text-left font-medium cursor-pointer whitespace-nowrap">'
        . Icon::make()->name('eye')->class('w-4 h-4')
        . 'Lihat Karya</a>'
        . '</div></details>';
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

<script>
    document.addEventListener('click', function(e) {
        document.querySelectorAll('details[data-kebab-menu][open]').forEach(function(d) {
            if (!d.contains(e.target)) d.removeAttribute('open');
        });
    });
</script>