<?php
/** @var array|null $team */
/** @var array|null $payment */
/** @var string $csrf_token */
$pay_error = \App\Utils\Session::flash('payment_error');
?>
<?php if (!$team): ?>
  <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-200 rounded-xl">
    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm text-gray-500">Daftarkan tim terlebih dahulu.</p>
  </div>
<?php else: ?>
  <?php if ($pay_error): ?>
    <div class="flex items-center gap-2 p-3 mb-6 bg-red-50 border border-red-200 rounded-xl">
      <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <span class="text-sm text-red-800"><?= htmlspecialchars($pay_error) ?></span>
    </div>
  <?php endif; ?>

  <?php if (!$payment): ?>
    <form action="/dashboard/payment" method="POST" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Transfer <span class="text-red-500">*</span></label>
        <div class="relative overflow-hidden rounded-xl border-2 border-dashed border-gray-300 hover:border-brand transition cursor-pointer" id="paymentDropzone">
          <input id="proofImage" name="proofImage" type="file" class="hidden" accept="image/*" required data-error="err-payment-proof"
            onchange="document.getElementById('err-payment-proof')?.classList.add('hidden')">
          <div id="dropzonePlaceholder" class="flex flex-col items-center justify-center px-6 py-10">
            <svg class="w-12 h-12 text-gray-400 mb-3" stroke="currentColor" fill="none" viewBox="0 0 48 48">
              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <p class="text-sm text-gray-600"><span class="font-medium text-brand">Pilih file</span> atau seret ke sini</p>
            <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF, WebP — maks 2MB</p>
          </div>
          <div id="dropzonePreview" class="hidden relative">
            <img id="previewImage" class="w-full max-h-64 object-contain bg-gray-50">
            <div class="absolute inset-0 bg-black/0 hover:bg-black/20 transition-colors flex items-center justify-center">
              <span class="text-white font-semibold text-sm bg-black/60 px-4 py-2 rounded-xl opacity-0 hover:opacity-100 transition-opacity">Klik untuk ganti</span>
            </div>
          </div>
        </div>
        <p id="err-payment-proof" class="text-xs text-red-500 mt-1 hidden">Bukti transfer wajib diupload</p>
      </div>
    </form>

  <?php elseif ($payment['status'] === 'verified'): ?>
    <div class="flex items-start gap-3">
      <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <div>
        <p class="text-sm text-green-800 font-medium">Pembayaran telah diverifikasi!</p>
        <p class="text-xs text-green-600 mt-0.5">Pendaftaran tim kamu sudah lengkap.</p>
        <?php if (!empty($payment['proofImage'])): ?>
          <img src="/uploads/payments/<?= htmlspecialchars($payment['proofImage']) ?>" class="mt-3 max-h-48 rounded-xl border border-gray-200 object-contain bg-gray-50">
        <?php endif; ?>
      </div>
    </div>

  <?php elseif ($payment['status'] === 'pending'): ?>
    <div class="flex items-start gap-3">
      <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <div class="flex-1 min-w-0">
        <p class="text-sm text-yellow-800 font-medium">Pembayaran menunggu verifikasi</p>
        <p class="text-xs text-yellow-600 mt-1">Admin akan memeriksa bukti pembayaran kamu.</p>
        <?php if (!empty($payment['proofImage'])): ?>
          <img src="/uploads/payments/<?= htmlspecialchars($payment['proofImage']) ?>" class="mt-3 max-h-48 rounded-xl border border-gray-200 object-contain bg-gray-50">
        <?php endif; ?>
      </div>
    </div>

  <?php elseif ($payment['status'] === 'rejected'): ?>
    <div class="flex items-start gap-3 mb-5">
      <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <div class="flex-1 min-w-0">
        <p class="text-sm text-red-800 font-medium">Pembayaran ditolak</p>
        <?php if (!empty($payment['note'])): ?><p class="text-sm text-red-700 mt-1">Alasan: <?= htmlspecialchars($payment['note']) ?></p><?php endif; ?>
      </div>
    </div>
    <form action="/dashboard/payment" method="POST" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Ulang Bukti Transfer</label>
        <div class="relative overflow-hidden rounded-xl border-2 border-dashed border-gray-300 hover:border-brand transition cursor-pointer" id="paymentDropzone2">
          <input id="proofImage2" name="proofImage" type="file" class="hidden" accept="image/*" required data-error="err-payment-proof2"
            onchange="document.getElementById('err-payment-proof2')?.classList.add('hidden')">
          <div id="dropzonePlaceholder2" class="flex flex-col items-center justify-center px-6 py-8">
            <p class="text-sm text-gray-600"><span class="font-medium text-brand">Pilih file</span> atau seret ke sini</p>
            <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF, WebP — maks 2MB</p>
          </div>
          <div id="dropzonePreview2" class="hidden relative">
            <img id="previewImage2" class="w-full max-h-48 object-contain bg-gray-50">
            <div class="absolute inset-0 bg-black/0 hover:bg-black/20 transition-colors flex items-center justify-center">
              <span class="text-white font-semibold text-sm bg-black/60 px-4 py-2 rounded-xl opacity-0 hover:opacity-100 transition-opacity">Klik untuk ganti</span>
            </div>
          </div>
        </div>
        <p id="err-payment-proof2" class="text-xs text-red-500 mt-1 hidden">Bukti transfer wajib diupload</p>
      </div>
    </form>
  <?php endif; ?>
<?php endif; ?>
