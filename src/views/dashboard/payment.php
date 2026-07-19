<?php
use App\Components\Attachment;
use App\Components\Icon;

/** @var string $csrf_token */
/** @var array $team */
/** @var array|null $payment */
/** @var string|null $error */ ?>
<div class="bg-white shadow-xl rounded-2xl overflow-hidden max-w-4xl mx-auto mt-8">
    <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Upload Bukti Pembayaran</h1>
        <a href="/dashboard" class="text-sm font-medium text-blue-600 hover:text-blue-500">Kembali ke Dashboard</a>
    </div>

    <div class="px-6 py-8">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800">
                <strong>Tim:</strong> <?= htmlspecialchars($team['name']) ?> —
                <strong>Divisi:</strong> <?= htmlspecialchars($team['division']) ?>
            </p>
        </div>

        <?php if (isset($error) && $error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
                <p class="text-sm"><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($payment) && $payment && $payment['status'] === 'pending'): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 flex items-center gap-2">
                <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-yellow-600 shrink-0') ?>
                <p class="text-sm text-yellow-800">Pembayaran sebelumnya sedang menunggu verifikasi. Anda dapat mengunggah ulang bukti pembayaran.</p>
            </div>
        <?php endif; ?>

        <form action="/dashboard/payment" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

            <div>
                <label for="proofImage" class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Bukti Transfer <span class="text-red-500">*</span>
                </label>
                <?= Attachment::make()
                    ->state('idle')
                    ->media(Icon::make()->name('upload')->class('size-6 text-gray-400'))
                    ->title('Upload Bukti Transfer')
                    ->description('PNG, JPG, GIF, WebP — maks 2MB')
                    ->withPreview()
                    ->fileInput('proofImage', ['accept' => 'image/*', 'required' => true])
                    ->render() ?>
            </div>

            <div class="pt-4 border-t border-gray-200">
                <button type="submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-brand hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                    Upload Bukti Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>
