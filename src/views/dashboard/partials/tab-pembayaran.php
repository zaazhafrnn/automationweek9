<?php
use App\Components\Attachment;
use App\Components\Icon;

/** @var array|null $team */
/** @var array|null $payment */
/** @var string $csrf_token */
$pay_error = \App\Utils\Session::flash('payment_error');
?>
<?php if (!$team): ?>
  <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-200 rounded-xl">
    <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-gray-400 shrink-0') ?>
    <p class="text-sm text-gray-500">Daftarkan tim terlebih dahulu.</p>
  </div>
<?php else: ?>
  <?php if ($pay_error): ?>
    <div class="flex items-center gap-2 p-3 mb-6 bg-red-50 border border-red-200 rounded-xl">
      <?= Icon::make()->name('alert-circle')->class('w-4 h-4 text-red-600 shrink-0') ?>
      <span class="text-sm text-red-800"><?= htmlspecialchars($pay_error) ?></span>
    </div>
  <?php endif; ?>

  <?php if (!$payment): ?>
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
            ->attr('id', 'paymentDropzone')
            ->render() ?>
        <p id="err-payment-proof" class="text-xs text-red-500 mt-1 hidden">Bukti transfer wajib diupload</p>
      </div>
    </form>

  <?php elseif ($payment['status'] === 'verified'): ?>
    <div class="flex items-start gap-3">
      <?= Icon::make()->name('check')->class('w-5 h-5 text-green-500 shrink-0 mt-0.5') ?>
      <div>
        <p class="text-sm text-green-800 font-medium">Pembayaran telah diverifikasi!</p>
        <p class="text-xs text-green-600 mt-0.5">Pendaftaran tim kamu sudah lengkap.</p>
        <?php if (!empty($payment['proofImage'])): ?>
          <?= Attachment::make()
              ->state('done')
              ->mediaVariant('image')
              ->media('<img src="/uploads/payments/' . htmlspecialchars($payment['proofImage']) . '" class="w-full h-full object-cover">')
              ->title(basename($payment['proofImage']))
              ->description('Terverifikasi')
              ->render() ?>
        <?php endif; ?>
      </div>
    </div>

  <?php elseif ($payment['status'] === 'pending'): ?>
    <div class="flex items-start gap-3">
      <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-yellow-500 shrink-0 mt-0.5') ?>
      <div class="flex-1 min-w-0">
        <p class="text-sm text-yellow-800 font-medium">Pembayaran menunggu verifikasi</p>
        <p class="text-xs text-yellow-600 mt-1">Admin akan memeriksa bukti pembayaran kamu.</p>
        <?php if (!empty($payment['proofImage'])): ?>
          <?= Attachment::make()
              ->state('done')
              ->mediaVariant('image')
              ->media('<img src="/uploads/payments/' . htmlspecialchars($payment['proofImage']) . '" class="w-full h-full object-cover">')
              ->title(basename($payment['proofImage']))
              ->description('Menunggu verifikasi')
              ->render() ?>
        <?php endif; ?>
      </div>
    </div>

  <?php elseif ($payment['status'] === 'rejected'): ?>
    <div class="flex items-start gap-3 mb-5">
      <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-red-500 shrink-0 mt-0.5') ?>
      <div class="flex-1 min-w-0">
        <p class="text-sm text-red-800 font-medium">Pembayaran ditolak</p>
        <?php if (!empty($payment['note'])): ?><p class="text-sm text-red-700 mt-1">Alasan: <?= htmlspecialchars($payment['note']) ?></p><?php endif; ?>
      </div>
    </div>
    <form action="/dashboard/payment" method="POST" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Ulang Bukti Transfer</label>
        <?= Attachment::make()
            ->state('idle')
            ->media(Icon::make()->name('upload')->class('size-6 text-gray-400'))
            ->title('Upload Ulang Bukti Transfer')
            ->description('PNG, JPG, GIF, WebP — maks 2MB')
            ->withPreview()
            ->fileInput('proofImage', ['accept' => 'image/*', 'required' => true, 'data-error' => 'err-payment-proof2'])
            ->attr('id', 'paymentDropzone2')
            ->render() ?>
        <p id="err-payment-proof2" class="text-xs text-red-500 mt-1 hidden">Bukti transfer wajib diupload</p>
      </div>
    </form>
  <?php endif; ?>
<?php endif; ?>
