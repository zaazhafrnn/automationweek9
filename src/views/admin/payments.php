<?php

/** @var array $payments */
/** @var string $csrf_token */
/** @var string $page_title */

use App\Components\Button;
use App\Components\Dialog;

$previewDialog = Dialog::make()
    ->id('previewDialog')
    ->title('Bukti Pembayaran');
?>
<div class="w-full bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800">
            Status Pembayaran Tim
        </h2>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="w-full table-fixed divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="w-16 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">No</th>
                    <th scope="col" class="w-1/6 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tim</th>
                    <th scope="col" class="w-28 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kategori</th>
                    <th scope="col" class="w-1/6 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Ketua</th>
                    <th scope="col" class="w-28 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th scope="col" class="w-28 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Bukti</th>
                    <th scope="col" class="w-32 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Dikirim</th>
                    <th scope="col" class="w-36 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                            Belum ada pembayaran.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $index => $p): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4 align-top text-sm text-gray-600"><?= $index + 1 ?></td>
                            <td class="px-4 py-4 align-top">
                                <div class="font-semibold text-gray-900 break-words"><?= htmlspecialchars($p['team_name']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($p['teamSchool'] ?? '-') ?></div>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-800">
                                    <?= htmlspecialchars($p['division']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <div class="font-medium text-gray-900"><?= htmlspecialchars($p['leaderName']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($p['leaderPhoneNumber']) ?></div>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <?php if ($p['status'] === 'verified'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Terverifikasi</span>
                                <?php elseif ($p['status'] === 'rejected'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <?php if (!empty($p['proofImage'])): ?>
                                    <?= Button::make()
                                        ->label('Lihat')
                                        ->variant('link')
                                        ->size('sm')
                                        ->attr('onclick', "document.getElementById('previewImg').src='/uploads/payments/" . htmlspecialchars($p['proofImage']) . "';openDialog('previewDialog')")
                                    ?>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 align-top text-sm text-gray-600">
                                <?= date('d M Y H:i', strtotime($p['submittedAt'])) ?>
                            </td>
                            <td class="px-4 py-4 align-top text-black">
                                <?php if ($p['status'] === 'pending'): ?>
                                    <form action="/admin/payments/process" method="POST" class="space-y-2">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                        <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                                        <?= Button::make()->label('Terima')->variant('success')->size('sm')->tag('button')->attr('type', 'submit')->attr('name', 'action')->attr('value', 'verify') ?>
                                        <input type="text" name="note" placeholder="Alasan (opsional)" class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                                        <?= Button::make()->label('Tolak')->variant('danger')->size('sm')->tag('button')->attr('type', 'submit')->attr('name', 'action')->attr('value', 'reject') ?>
                                    </form>
                                <?php elseif ($p['status'] === 'verified'): ?>
                                    <form action="/admin/payments/process" method="POST" onsubmit="return confirm('Batalkan verifikasi pembayaran ini?')">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                        <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="action" value="cancel">
                                        <?= Button::make()->label('Batalkan')->variant('secondary')->size('sm')->tag('button')->attr('type', 'submit') ?>
                                    </form>
                                <?php elseif ($p['status'] === 'rejected'): ?>
                                    <?php if (!empty($p['note'])): ?>
                                        <div class="text-xs text-gray-600 mb-2">
                                            <span class="font-medium">Alasan:</span> <?= htmlspecialchars($p['note']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <form action="/admin/payments/process" method="POST" onsubmit="return confirm('Reset status pembayaran ini menjadi pending?')">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                        <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="action" value="cancel">
                                        <?= Button::make()->label('Reset')->variant('secondary')->size('sm')->tag('button')->attr('type', 'submit') ?>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $previewDialog->content('<img id="previewImg" src="" alt="Preview Bukti Pembayaran" class="w-full h-auto max-h-[75vh] object-contain rounded">')->render() ?>