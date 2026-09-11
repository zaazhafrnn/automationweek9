<?php

/** @var array $payments */
/** @var string $csrf_token */
/** @var string $page_title */

use App\Components\Button;
use App\Components\DataTable;
use App\Components\Dialog;
use App\Components\Icon;

$previewDialog = Dialog::make()->id('previewDialog')->title('Bukti Pembayaran');

$rows = [];
foreach ($payments as $p) {
    $statusMap = [
        'verified' => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800">Terverifikasi</span>',
        'rejected' => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-red-100 text-red-800">Ditolak</span>',
        'pending' => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800">Pending</span>',
    ];

    $bukti = !empty($p['proofImage'])
        ? (string) Button::make()->label('Lihat')->variant('link')->size('sm')
            ->attr('onclick', "document.getElementById('previewImg').src='/uploads/payments/" . htmlspecialchars($p['proofImage']) . "';openDialog('previewDialog')")
        : '<span class="text-muted-foreground">-</span>';

    $aksi = '';
    $dialogHtml = '';
    if ($p['status'] === 'pending') {
        $verifyDialogId = 'dialog-verify-' . $p['id'];
        $verifyMessage = "Apakah Anda yakin ingin memverifikasi pembayaran dari tim <b>" . htmlspecialchars($p['team_name']) . "</b>?";
        $verifyForm = '<form action="/admin/payments/process" method="POST">'
            . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
            . '<input type="hidden" name="payment_id" value="' . $p['id'] . '">'
            . '<input type="hidden" name="action" value="verify">'
            . '<p class="text-sm text-gray-600 mb-6 break-words whitespace-normal w-full">' . $verifyMessage . '</p>'
            . '<div class="flex justify-end space-x-2">'
            . '<button type="button" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors" onclick="closeDialog(\'' . $verifyDialogId . '\')">Batal</button>'
            . '<button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700">Ya, Verifikasi</button>'
            . '</div>'
            . '</form>';
        $dialogHtml .= Dialog::make()->id($verifyDialogId)->title('Konfirmasi Verifikasi')->width('max-w-md')->content($verifyForm)->render();

        $rejectDialogId = 'dialog-reject-' . $p['id'];
        $rejectMessage = "Apakah Anda yakin ingin menolak pembayaran dari tim <b>" . htmlspecialchars($p['team_name']) . "</b>?";
        $rejectForm = '<form action="/admin/payments/process" method="POST">'
            . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
            . '<input type="hidden" name="payment_id" value="' . $p['id'] . '">'
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

        $aksi = '<div class="mt-2 flex gap-1">'
            . '<button type="button" ' . Dialog::make()->id($verifyDialogId)->getOpenAttr() . ' class="text-green-600 bg-gray-100 rounded p-1" data-tooltip="Verifikasi">' . Icon::make()->name('check')->class('w-5 h-5') . '</button>'
            . '<button type="button" ' . Dialog::make()->id($rejectDialogId)->getOpenAttr() . ' class="text-red-600 bg-gray-100 rounded p-1" data-tooltip="Tolak">' . Icon::make()->name('x')->class('w-5 h-5') . '</button>'
            . '</div>';
        $aksi .= $dialogHtml;
    } elseif ($p['status'] === 'verified') {
        $aksi = '<form action="/admin/payments/process" method="POST" onsubmit="return confirm(\'Batalkan verifikasi pembayaran ini?\')">'
            . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
            . '<input type="hidden" name="payment_id" value="' . $p['id'] . '">'
            . '<input type="hidden" name="action" value="cancel">'
            . Button::make()->label('Batalkan')->variant('secondary')->size('sm')->tag('button')->attr('type', 'submit')
            . '</form>';
    } elseif ($p['status'] === 'rejected') {
        if (!empty($p['note'])) {
            $aksi .= '<div class="mb-2 text-xs text-muted-foreground"><span class="font-medium">Alasan:</span> ' . htmlspecialchars($p['note']) . '</div>';
        }
        $aksi .= '<form action="/admin/payments/process" method="POST" onsubmit="return confirm(\'Reset status pembayaran ini menjadi pending?\')">'
            . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
            . '<input type="hidden" name="payment_id" value="' . $p['id'] . '">'
            . '<input type="hidden" name="action" value="cancel">'
            . Button::make()->label('Reset')->variant('secondary')->size('sm')->tag('button')->attr('type', 'submit')
            . '</form>';
    }

    $rows[] = [
        'team_name' => htmlspecialchars($p['team_name']),
        'school' => htmlspecialchars($p['teamSchool'] ?? '-'),
        'divisi' => htmlspecialchars($p['division']),
        'leader' => htmlspecialchars($p['leaderName']),
        'leaderPhone' => htmlspecialchars($p['leaderPhoneNumber']),
        'status' => $statusMap[$p['status']] ?? $p['status'],
        'bukti' => $bukti,
        'submitted' => date('d M Y H:i', strtotime($p['submittedAt'])),
        'aksi' => $aksi,
    ];
}
?>

<div class="rounded-xl border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="text-2xl font-semibold leading-none tracking-tight">Status Pembayaran Tim</h3>
        <p class="text-sm text-muted-foreground">Kelola verifikasi pembayaran dari setiap tim.</p>
    </div>
    <div class="p-6 pt-0">
        <?= DataTable::make()
            ->columns([
                ['key' => 'team', 'label' => 'Tim', 'render' => fn($row) => '<div class="font-medium">' . $row['team_name'] . '</div><div class="text-sm text-muted-foreground">' . ($row['school'] ?? '-') . '</div>'],
                ['key' => 'divisi', 'label' => 'Kategori', 'render' => fn($row) => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-secondary text-secondary-foreground">' . $row['divisi'] . '</span>'],
                ['key' => 'ketua', 'label' => 'Anggota 1 (Ketua)', 'render' => fn($row) => '<div class="font-medium">' . $row['leader'] . '</div><div class="text-sm text-muted-foreground">' . $row['leaderPhone'] . '</div>'],
                ['key' => 'status', 'label' => 'Status', 'sortable' => false, 'render' => fn($row) => $row['status']],
                ['key' => 'bukti', 'label' => 'Bukti', 'sortable' => false, 'render' => fn($row) => $row['bukti']],
                ['key' => 'submitted', 'label' => 'Dikirim', 'tdClass' => 'text-muted-foreground'],
                ['key' => 'aksi', 'label' => 'Aksi', 'sortable' => false, 'render' => fn($row) => $row['aksi']],
            ])
            ->searchable()
            ->columnSelectable()
            ->pageable()
            ->rows($rows)
            ->emptyText('Belum ada pembayaran.')
            ->render() ?>
    </div>
</div>

<?= $previewDialog->content('<img id="previewImg" src="" alt="Preview Bukti Pembayaran" class="w-full h-auto max-h-[75vh] object-contain rounded-md">')->render() ?>