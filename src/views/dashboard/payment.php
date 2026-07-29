<?php

use App\Components\Attachment;
use App\Components\Icon;

/** @var string $csrf_token */
/** @var string $user_name */
/** @var array|null $team */
/** @var array|null $payment */

$status = $payment['status'] ?? null;
$uploadIcon = Icon::make()->name('upload')->class('size-6 text-black');
?>
<div class="min-h-screen bg-gray-50">
  <div class="bg-brand border-b border-gray-200 text-white">
    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-14">
      <div>
        <h1 class="text-lg font-bold">Hi, <?= htmlspecialchars(explode(' ', $user_name ?? '')[0]) ?>!</h1>
        <p class="text-xs -mt-0.5">Kelola pendaftaran tim kamu.</p>
      </div>
      <?php $current = 'payment';
      include __DIR__ . '/partials/nav-tabs.php'; ?>
      <form action="/logout" method="POST" class="m-0">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-black bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
          <?= Icon::make()->name('log-out')->class('w-3.5 h-3.5') ?>
          Logout
        </button>
      </form>
    </div>
  </div>

  <div class="px-4 sm:px-6 lg:px-8 py-4">
    <?php if (!$team): ?>
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 max-w-lg">
        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-4">
          <?= Icon::make()->name('alert-circle')->class('w-7 h-7 text-gray-400') ?>
        </div>
        <h2 class="text-lg font-bold text-gray-800 mb-1">Belum ada tim</h2>
        <p class="text-sm text-gray-500 mb-5">Kamu perlu mendaftarkan tim terlebih dahulu sebelum melakukan pembayaran.</p>
        <a href="/application/team-register" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand rounded-xl hover:bg-red-800 transition-colors no-underline">
          <?= Icon::make()->name('arrow-left')->class('w-4 h-4') ?>
          Daftarkan Tim
        </a>
      </div>
    <?php else: ?>
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="border-b border-gray-200 px-4 sm:px-6 py-3">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-sm font-bold text-gray-800">Pembayaran</h2>
              <p class="text-xs text-gray-500">Upload bukti transfer untuk menyelesaikan pendaftaran.</p>
            </div>
            <?php if ($status): ?>
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                <?= match ($status) {
                  'verified' => 'bg-green-50 text-green-700 border border-green-200',
                  'rejected' => 'bg-red-50 text-red-700 border border-red-200',
                  default => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                } ?>">
                <?= Icon::make()->name(match ($status) {
                  'verified' => 'check-circle',
                  'rejected' => 'x-circle',
                  default => 'clock'
                })->class('w-3.5 h-3.5') ?>
                <?= ucfirst(htmlspecialchars($status)) ?>
              </span>
            <?php endif; ?>
          </div>
        </div>

        <div class="border-t border-gray-100 p-4 sm:p-6">
          <?php if ($status === 'verified'): ?>
            <div class="flex items-start gap-4 py-4">
              <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                <?= Icon::make()->name('check-circle')->class('w-7 h-7 text-green-500') ?>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="text-lg font-bold text-gray-800 mb-1">Pembayaran Terverifikasi</h3>
                <p class="text-sm text-gray-500">Bukti transfer kamu telah diterima dan diverifikasi oleh admin. Tim kamu sudah siap untuk submit karya.</p>
                <a href="/application/review" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand rounded-xl hover:bg-red-800 transition-colors no-underline">
                  Lanjut ke Review
                  <?= Icon::make()->name('chevron-right')->class('w-4 h-4') ?>
                </a>
              </div>
              <?php if (!empty($payment['proofImage'])): ?>
                <div class="rounded-xl overflow-hidden border border-gray-200 w-24 h-24 shrink-0">
                  <img src="/uploads/payments/<?= htmlspecialchars($payment['proofImage']) ?>" class="w-full h-full object-cover" alt="Bukti transfer">
                </div>
              <?php endif; ?>
            </div>

          <?php elseif ($status === 'rejected'): ?>
            <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl mb-6">
              <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-red-500 shrink-0 mt-0.5') ?>
              <div>
                <p class="text-sm font-medium text-red-700">Bukti transfer ditolak</p>
                <p class="text-xs text-red-600 mt-0.5"><?= htmlspecialchars($payment['note'] ?? 'Silakan upload ulang bukti transfer yang valid.') ?></p>
              </div>
            </div>
            <?php if (!empty($payment['proofImage'])): ?>
              <div class="mb-5">
                <p class="text-xs text-gray-500 mb-2">Bukti sebelumnya:</p>
                <div class="rounded-xl overflow-hidden border border-gray-200 w-32 h-32 opacity-50">
                  <img src="/uploads/payments/<?= htmlspecialchars($payment['proofImage']) ?>" class="w-full h-full object-cover" alt="Bukti sebelumnya">
                </div>
              </div>
            <?php endif; ?>
            <form action="/payments" method="POST" enctype="multipart/form-data" novalidate>
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
              <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Upload Ulang Bukti Transfer <span class="text-red-500">*</span></label>
                <?= Attachment::make()
                  
                  ->media($uploadIcon)
                  ->title('Upload Ulang Bukti Transfer')
                  ->description('PNG, JPG, GIF, WebP — maks 2MB')
                  ->clearable()
                  ->withPreview()
                  ->originalMedia($uploadIcon)
                  ->fileInput('proofImage', ['accept' => 'image/*', 'required' => true, 'data-error' => 'err-payment-proof'])
                  ->render() ?>
                <p id="err-payment-proof" class="text-xs text-red-500 mt-1 hidden">Bukti transfer wajib diupload</p>
              </div>
              <div class="flex justify-end mt-6">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand rounded-xl hover:bg-red-800 transition-colors">
                  Upload Ulang
                  <?= Icon::make()->name('upload')->class('w-4 h-4') ?>
                </button>
              </div>
            </form>

          <?php elseif ($status === 'pending'): ?>
            <div class="flex items-start gap-4 py-4">
              <div class="w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center shrink-0">
                <?= Icon::make()->name('clock')->class('w-7 h-7 text-yellow-500') ?>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="text-lg font-bold text-gray-800 mb-1">Menunggu Verifikasi</h3>
                <p class="text-sm text-gray-500">Bukti transfer kamu sedang ditinjau oleh admin. Kamu akan diberitahu setelah diverifikasi.</p>
                <p class="text-xs text-gray-400 mt-2">Biasanya diverifikasi dalam 1×24 jam.</p>
              </div>
              <?php if (!empty($payment['proofImage'])): ?>
                <div class="rounded-xl overflow-hidden border border-gray-200 w-24 h-24 shrink-0">
                  <img src="/uploads/payments/<?= htmlspecialchars($payment['proofImage']) ?>" class="w-full h-full object-cover" alt="Bukti transfer">
                </div>
              <?php endif; ?>
            </div>

          <?php else: ?>
            <p class="text-sm text-gray-600 mb-5">Upload bukti transfer pembayaran untuk menyelesaikan pendaftaran tim kamu.</p>
            <form action="/payments" method="POST" enctype="multipart/form-data" novalidate>
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
              <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Upload Bukti Transfer <span class="text-red-500">*</span></label>
                <?= Attachment::make()
                  
                  ->media($uploadIcon)
                  ->title('Upload Bukti Transfer')
                  ->description('PNG, JPG, GIF, WebP — maks 2MB')
                  ->clearable()
                  ->withPreview()
                  ->originalMedia($uploadIcon)
                  ->fileInput('proofImage', ['accept' => 'image/*', 'required' => true, 'data-error' => 'err-payment-proof'])
                  ->render() ?>
                <p id="err-payment-proof" class="text-xs text-red-500 mt-1 hidden">Bukti transfer wajib diupload</p>
              </div>
              <div class="flex justify-end mt-6">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand rounded-xl hover:bg-red-800 transition-colors">
                  Upload Bukti Transfer
                  <?= Icon::make()->name('upload')->class('w-4 h-4') ?>
                </button>
              </div>
            </form>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    function formatFileSize(bytes) {
      if (!bytes) return '';
      if (bytes < 1024) return bytes + ' B';
      if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
      return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    document.addEventListener('change', function(e) {
      if (!e.target.matches('[data-preview]')) return;
      var file = e.target.files && e.target.files[0];
      if (!file) return;
      var att = e.target.closest('[data-slot="attachment"]');
      if (!att) return;
      att.dataset.state = 'done';
      var title = att.querySelector('[data-slot="attachment-title"]');
      if (title) title.textContent = file.name;
      var desc = att.querySelector('[data-slot="attachment-description"]');
      if (desc) desc.textContent = formatFileSize(file.size);

      var errId = e.target.dataset.error;
      if (errId) {
        var errEl = document.getElementById(errId);
        if (errEl) errEl.classList.add('hidden');
      }
      e.target.classList.remove('border-red-500');
      att.classList.remove('border-red-500');

      var media = att.querySelector('[data-slot="attachment-media"]');
      if (!media) return;
      var reader = new FileReader();
      reader.onload = function(ev) {
        media.innerHTML = '<img src="' + ev.target.result + '" class="w-full h-full object-cover">';
      };
      reader.readAsDataURL(file);
    });

    document.addEventListener('click', function(e) {
      var clearBtn = e.target.closest('[data-clear-attachment]');
      if (!clearBtn) return;
      e.preventDefault();
      e.stopPropagation();
      var att = clearBtn.closest('[data-slot="attachment"]');
      if (!att) return;
      var input = att.querySelector('input[type="file"]');
      if (input) input.value = '';
      att.dataset.state = 'idle';
      var title = att.querySelector('[data-slot="attachment-title"]');
      if (title) title.textContent = att.dataset.idleTitle || att.dataset.originalTitle || '';
      var desc = att.querySelector('[data-slot="attachment-description"]');
      if (desc) desc.textContent = att.dataset.idleDescription || '';
      var media = att.querySelector('[data-slot="attachment-media"]');
      var origMedia = att.dataset.originalMedia;
      if (media && origMedia) media.innerHTML = origMedia;
    });
  });
</script>
