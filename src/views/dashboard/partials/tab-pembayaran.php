<?php

use App\Components\Attachment;
use App\Components\Icon;

/** @var array|null $team */
/** @var array|null $payment */
/** @var string $csrf_token */
?>
<?php if (!$team): ?>
  <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-200 rounded-xl">
    <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-gray-400 shrink-0') ?>
    <p class="text-sm text-gray-500">Daftarkan tim terlebih dahulu.</p>
  </div>
<?php else: ?>
  <?php if ($payment && $payment['status'] === 'rejected'): ?>
    <form action="/dashboard/payment" method="POST" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <div class="mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Ulang Bukti Transfer</label>
        <?= Attachment::make()
          ->state('idle')
          ->media(Icon::make()->name('upload')->class('size-6 text-gray-400'))
          ->title('Upload Ulang Bukti Transfer')
          ->description('PNG, JPG, GIF, WebP — maks 2MB')
          ->withPreview()
          ->fileInput('proofImage', ['accept' => 'image/*', 'required' => true, 'data-error' => 'err-payment-proof2'])
          ->render() ?>
        <p id="err-payment-proof2" class="text-xs text-red-500 mt-1 hidden">Bukti transfer wajib diupload</p>
      </div>
    </form>
  <?php elseif ($payment && !empty($payment['proofImage'])): ?>
    <div>
      <?= Attachment::make()
        ->state('done')
        ->mediaVariant('image')
        ->media('<img src="/uploads/payments/' . htmlspecialchars($payment['proofImage']) . '" class="w-full h-full object-cover">')
        ->title(basename($payment['proofImage']))
        ->render() ?>
    </div>
  <?php endif; ?>

  <?php if (!$payment || $payment['status'] === 'rejected'): ?>
    <form action="/dashboard/payment" method="POST" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Transfer <span class="text-red-500">*</span></label>
        <?= Attachment::make()
          ->state('idle')
          ->media(Icon::make()->name('upload')->class('size-6 text-gray-400'))
          ->title('Upload Bukti Transfer')
          ->description('PNG, JPG, GIF, WebP — maks 2MB')
          ->withPreview()
          ->fileInput('proofImage', ['accept' => 'image/*', 'required' => true, 'data-error' => 'err-payment-proof'])
          ->render() ?>
        <p id="err-payment-proof" class="text-xs text-red-500 mt-1 hidden">Bukti transfer wajib diupload</p>
      </div>
    </form>
  <?php endif; ?>
<?php endif; ?>