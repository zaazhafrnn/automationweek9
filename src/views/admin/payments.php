<?php

/** @var array $payments */
/** @var string $csrf_token */
/** @var string $page_title */

use App\Components\Button;
use App\Components\DataTable;
use App\Components\Dialog;

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
    if ($p['status'] === 'pending') {
        $aksi = '<form action="/admin/payments/process" method="POST" class="space-y-2">'
            . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
            . '<input type="hidden" name="payment_id" value="' . $p['id'] . '">'
            . Button::make()->label('Terima')->variant('default')->size('sm')->tag('button')->attr('type', 'submit')->attr('name', 'action')->attr('value', 'verify')
            . '<input type="text" name="note" placeholder="Alasan (opsional)" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">'
            . Button::make()->label('Tolak')->variant('destructive')->size('sm')->tag('button')->attr('type', 'submit')->attr('name', 'action')->attr('value', 'reject')
            . '</form>';
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
                ['key' => 'ketua', 'label' => 'Ketua', 'render' => fn($row) => '<div class="font-medium">' . $row['leader'] . '</div><div class="text-sm text-muted-foreground">' . $row['leaderPhone'] . '</div>'],
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