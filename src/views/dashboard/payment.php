<?php

use App\Components\Dialog;
use App\Components\Icon;

/** @var string $csrf_token */
/** @var string $user_name */
/** @var array|null $team */
/** @var array|null $payment */
/** @var bool $is_reviewed */

$status = $payment['status'] ?? null;
$locked = !$team || !$is_reviewed;
$hasProof = !empty($payment['proofImage']);
$verified = ($status === 'verified');
?>
<div class="min-h-screen bg-gray-50">
  <?php $current = 'payment';
  include BASE_PATH . '/src/Components/nav-tabs.php'; ?>

  <div class="px-4 sm:px-6 lg:px-8 py-4 mx-auto">
    <?php if ($locked): ?>
      <div class="relative">
        <div class="pointer-events-none select-none opacity-50">
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="border-b border-gray-200 px-5 py-3.5">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="font-display text-xl font-bold tracking-tight text-gray-900 mt-0.5">Pembayaran</h2>
                <p class="text-sm text-gray-500 mt-0.5">Upload bukti transfer untuk menyelesaikan pendaftaran.</p>
              </div>
            </div>
          </div>

          <div class="p-5 sm:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-semibold mb-3">Metode pembayaran</label>
                <p class="text-sm text-gray-600 leading-relaxed">Pembayaran melalui salah satu dari 2 rekening berikut</p>
                <div class="mt-4 space-y-8">
                  <div class="flex items-center gap-3">
                    <img src="/image/logo-seabank.png" alt="Logo Seabank" class="h-10 w-auto shrink-0" draggable="false" style="user-select: none;">
                    <div>
                      <p class="text-sm font-semibold text-gray-900 leading-none">Seabank</p>
                      <p class="text-base font-bold tracking-wide text-gray-900 leading-none">901531540263</p>
                      <p class="text-sm leading-none">Titis Nabila</p>
                    </div>
                  </div>
                  <div class="flex items-center gap-3 mt-0.5">
                    <img src="/image/logo-bri.png" alt="Logo BRI" class="h-10 p-2 w-auto shrink-0" draggable="false" style="user-select: none;">
                    <div>
                      <p class="text-sm font-semibold text-gray-900 leading-none">BRI</p>
                      <p class="text-base font-bold tracking-wide text-gray-900 leading-none">375701061838531</p>
                      <p class="text-sm leading-none">Shafaatur R.</p>
                    </div>
                  </div>

                </div>
                <p class="text-xs text-gray-400 mt-5 leading-relaxed">Setelah mengupload bukti transfer, mohon tunggu beberapa saat hingga pembayaran kamu diverifikasi oleh admin.</p>
              </div>

              <div>
                <label class="block text-sm font-semibold mb-3">Upload bukti pembayaran<span class="text-red-500">*</span></label>
                <form action="/payments" method="POST" enctype="multipart/form-data" novalidate id="payment-form">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                  <div class="flex flex-col sm:flex-row sm:items-end gap-5 <?= $verified ? 'pointer-events-none opacity-60 select-none' : '' ?>">
                    <div data-slot="attachment" class="shrink-0 w-80 max-w-full">
                      <label class="dropzone relative block w-full max-w-xs aspect-[3/4] rounded-2xl border-2 border-dashed border-gray-300 bg-transparent hover:border-brand transition-colors cursor-pointer overflow-hidden">

                        <div data-slot="attachment-idle" class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none p-6 text-center <?= $hasProof ? 'hidden' : '' ?>">
                          <?= Icon::make()->name('upload')->class('size-20 mb-4') ?>
                          <p class="text-lg font-semibold text-gray-800">Seret & lepas file ke sini</p>
                          <p class="text-sm text-gray-500 mt-1">atau klik untuk memilih file (PNG, JPG, maks 5MB)</p>
                        </div>

                        <div data-slot="attachment-preview" class="absolute inset-0 <?= $hasProof ? '' : 'hidden' ?> bg-white">
                          <img src="<?= $hasProof ? '/uploads/payments/' . htmlspecialchars($payment['proofImage']) : '' ?>" class="w-full h-full object-contain" alt="Preview bukti transfer">
                        </div>

                        <button type="button" data-clear-attachment
                          class="absolute top-4 right-4 z-20 <?= $hasProof ? 'flex' : 'hidden' ?> items-center justify-center w-10 h-10 bg-white rounded-full shadow-md border border-gray-200 hover:bg-red-50 hover:border-red-300 transition-colors">
                          <?= Icon::make()->name('trash-2')->class('w-5 h-5 text-red-500') ?>
                        </button>

                        <input type="file" name="proofImage" accept="image/*" required
                          data-error="err-payment-proof" data-max-size="<?= 5 * 1024 * 1024 ?>" data-preview="true"
                          class="absolute inset-0 opacity-0 cursor-pointer z-10">
                      </label>
                    </div>
                    <div class="flex flex-col items-start min-w-0">
                      <div data-slot="attachment-fileinfo" class="<?= $hasProof ? '' : 'hidden' ?> mb-4 max-w-[200px]">
                        <p class="text-sm font-medium text-gray-900 truncate" data-slot="file-name"><?= $hasProof ? htmlspecialchars($payment['original_name'] ?? $payment['proofImage']) : '' ?></p>
                        <p class="text-xs text-gray-500 mt-0.5" data-slot="file-size"></p>
                      </div>
                      <button type="submit" disabled id="payment-submit-btn" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-gray-300 rounded-xl transition-colors <?= $verified ? 'hidden' : '' ?>">
                        Upload Bukti Transfer
                        <?= Icon::make()->name('upload')->class('w-4 h-4') ?>
                      </button>
                    </div>
                  </div>
                  <p id="err-payment-proof" class="text-xs text-red-500 mt-1.5 hidden">Bukti transfer wajib diupload</p>
                </form>
              </div>
            </div>
          </div>
        </div>

        <?php if ($status === 'pending'): ?>
          <div class="flex items-start gap-3 p-4 bg-yellow-50 border border-yellow-200 rounded-2xl mt-4">
            <?= Icon::make()->name('clock')->class('w-5 h-5 text-yellow-500 shrink-0 mt-0.5') ?>
            <div>
              <p class="text-sm font-semibold text-yellow-700">Menunggu Verifikasi</p>
              <p class="text-xs text-yellow-600 mt-0.5">Bukti pembayaran kamu sedang ditinjau oleh admin. Mohon tunggu dan kembali beberapa saat.</p>
            </div>
          </div>
        <?php elseif ($status === 'verified'): ?>
          <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-2xl mt-4">
            <?= Icon::make()->name('check')->class('w-5 h-5 text-green-500 shrink-0 mt-0.5') ?>
            <div>
              <p class="text-sm font-semibold text-green-700">Pembayaran Terverifikasi</p>
              <p class="text-xs text-green-600 mt-0.5">Terimakasih sudah berpartisipasi. Selamat berkompetisi dengan jujur dan penuh semangat, semoga sukses!</p>
              <a href="/home" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 text-xs font-semibold text-green-700 bg-green-100 hover:bg-green-200 rounded-lg transition-colors no-underline">
                Kembali ke beranda
                <?= Icon::make()->name('chevron-right')->class('w-3.5 h-3.5') ?>
              </a>
            </div>
          </div>
        <?php elseif ($status === 'rejected'): ?>
          <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl mt-4">
            <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-red-500 shrink-0 mt-0.5') ?>
            <div>
              <p class="text-sm font-semibold text-red-700">Bukti transfer ditolak</p>
              <p class="text-xs text-red-600 mt-0.5"><?= htmlspecialchars($payment['note'] ?? 'Silakan upload ulang bukti transfer yang valid.') ?></p>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($locked): ?>
        </div>

        <div class="absolute inset-0 flex items-center justify-center z-10">
          <div class="w-32 h-32 rounded-full bg-white/90 shadow-xl flex items-center justify-center border border-gray-200">
            <?= Icon::make()->name('lock')->class('w-14 h-14 text-gray-400') ?>
          </div>
        </div>
      </div>


      <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl mt-4">
        <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-amber-500 shrink-0 mt-0.5') ?>
        <div>
          <p class="text-sm font-semibold text-amber-700">Mohon perhatian!</p>
          <p class="text-xs text-amber-600 mt-0.5">Pembayaran belum dapat diakses. Mohon <span class="font-semibold">selesaikan</span> seluruh tahap <span class="font-semibold">pendaftaran tim</span> terlebih dahulu, lengkapi data anggota tim, upload twibbon, kemudian submit pada halaman review.</p>
          <a href="/application" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 text-xs font-semibold text-amber-700 bg-amber-100 hover:bg-amber-200 rounded-lg transition-colors no-underline">
            <?= Icon::make()->name('arrow-left')->class('w-3.5 h-3.5') ?>
            Selesaikan pendaftaran
          </a>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($hasProof && !$verified): ?>
    <?= (new Dialog())->id('confirm-change-proof')->title('Apakah anda yakin?')->width('max-w-md')->content(
      '<div class="flex items-start gap-3">'
        . '<p class="text-sm text-gray-600">Bukti transfer yang sudah diupload akan diganti dengan file baru yang kamu pilih.</p>'
        . '</div>'
        . '<div class="flex justify-end gap-2 mt-5">'
        . '<button type="button" id="confirm-change-no" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>'
        . '<button type="button" id="confirm-change-yes" class="px-4 py-2 text-sm font-semibold text-white bg-brand hover:bg-red-800 rounded-xl transition-colors">Ya, Ganti</button>'
        . '</div>'
    ) ?>
  <?php endif; ?>

  <?php include BASE_PATH . '/src/Components/footer.php'; ?>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    function formatFileSize(bytes) {
      if (!bytes) return '';
      if (bytes < 1024) return bytes + ' B';
      if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
      return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    var pendingInput = null;
    var confirmed = false;

    var origClose = window.closeDialog;
    window.closeDialog = function(id) {
      if (id === 'confirm-change-proof' && !confirmed && pendingInput) {
        pendingInput.value = '';
        pendingInput = null;
      }
      origClose(id);
    };

    function processFile(input) {
      var file = input.files && input.files[0];
      if (!file) return;

      var att = input.closest('[data-slot="attachment"]');
      if (!att) return;

      var maxSize = input.dataset.maxSize;
      if (maxSize && file.size > parseInt(maxSize)) {
        input.value = '';
        var errEl = document.getElementById(input.dataset.error);
        if (errEl) {
          errEl.textContent = 'File terlalu besar. Maksimal ' + formatFileSize(parseInt(maxSize));
          errEl.classList.remove('hidden');
        }
        return;
      }

      var errId = input.dataset.error;
      if (errId) {
        var errEl = document.getElementById(errId);
        if (errEl) errEl.classList.add('hidden');
      }
      input.closest('.dropzone').classList.remove('border-red-500');

      var idle = att.querySelector('[data-slot="attachment-idle"]');
      var preview = att.querySelector('[data-slot="attachment-preview"]');
      var clearBtn = att.querySelector('[data-clear-attachment]');
      var img = preview.querySelector('img');

      if (idle) idle.classList.add('hidden');
      if (preview) preview.classList.remove('hidden');
      if (clearBtn) clearBtn.classList.remove('hidden');
      if (clearBtn) clearBtn.classList.add('flex');

      var fileInfo = att.parentElement.querySelector('[data-slot="attachment-fileinfo"]');
      if (fileInfo) {
        fileInfo.querySelector('[data-slot="file-name"]').textContent = file.name;
        fileInfo.querySelector('[data-slot="file-size"]').textContent =
          file.name.split('.').pop().toUpperCase() + ' \u2022 ' + formatFileSize(file.size);
        fileInfo.classList.remove('hidden');
      }

      var submitBtn = document.getElementById('payment-submit-btn');
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
        submitBtn.classList.add('bg-brand', 'hover:bg-red-800', 'cursor-pointer');
      }

      var reader = new FileReader();
      reader.onload = function(ev) {
        if (img) img.src = ev.target.result;
      };
      reader.readAsDataURL(file);
    }

    document.addEventListener('change', function(e) {
      if (!e.target.matches('[data-preview]')) return;
      if (!e.target.files || !e.target.files[0]) return;
      var att = e.target.closest('[data-slot="attachment"]');
      if (!att) return;

      var idle = att.querySelector('[data-slot="attachment-idle"]');
      if (!confirmed && idle && idle.classList.contains('hidden')) {
        pendingInput = e.target;
        openDialog('confirm-change-proof');
        return;
      }
      confirmed = false;
      processFile(e.target);
    });

    var yesBtn = document.getElementById('confirm-change-yes');
    if (yesBtn) yesBtn.addEventListener('click', function() {
      confirmed = true;
      closeDialog('confirm-change-proof');
      if (pendingInput) processFile(pendingInput);
      pendingInput = null;
      confirmed = false;
    });
    var noBtn = document.getElementById('confirm-change-no');
    if (noBtn) noBtn.addEventListener('click', function() {
      closeDialog('confirm-change-proof');
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

      var idle = att.querySelector('[data-slot="attachment-idle"]');
      var preview = att.querySelector('[data-slot="attachment-preview"]');
      var img = preview.querySelector('img');

      if (idle) idle.classList.remove('hidden');
      if (preview) preview.classList.add('hidden');
      if (img) img.src = '';

      clearBtn.classList.remove('flex');
      clearBtn.classList.add('hidden');

      var fileInfo = att.parentElement.querySelector('[data-slot="attachment-fileinfo"]');
      if (fileInfo) fileInfo.classList.add('hidden');

      var submitBtn = document.getElementById('payment-submit-btn');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
        submitBtn.classList.remove('bg-brand', 'hover:bg-red-800', 'cursor-pointer');
      }
    });

    document.querySelectorAll('.dropzone').forEach(function(zone) {
      zone.addEventListener('dragover', function(e) {
        e.preventDefault();
        zone.classList.add('border-brand', 'bg-red-50');
      });
      zone.addEventListener('dragleave', function() {
        zone.classList.remove('border-brand', 'bg-red-50');
      });
      zone.addEventListener('drop', function(e) {
        e.preventDefault();
        zone.classList.remove('border-brand', 'bg-red-50');
        var input = zone.querySelector('input[type="file"]');
        if (input && e.dataTransfer.files.length) {
          input.files = e.dataTransfer.files;
          input.dispatchEvent(new Event('change', {
            bubbles: true
          }));
        }
      });
    });

    var payForm = document.getElementById('payment-form');
    if (payForm) {
      payForm.addEventListener('submit', function(e) {
        var input = payForm.querySelector('input[type="file"][name="proofImage"]');
        if (!input || !input.files || !input.files.length) {
          e.preventDefault();
          var errEl = document.getElementById('err-payment-proof');
          if (errEl) {
            errEl.textContent = 'Bukti transfer wajib diupload';
            errEl.classList.remove('hidden');
          }
          var zone = payForm.querySelector('.dropzone');
          if (zone) zone.classList.add('border-red-400');
          return false;
        }
      });
    }
  });
</script>