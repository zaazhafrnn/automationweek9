<?php

use App\Components\Dialog;
use App\Components\Icon;
use App\Components\Toast;

/** @var string $csrf_token */
/** @var string $user_name */
/** @var array|null $team */
/** @var array|null $payment */
/** @var bool $is_reviewed */
/** @var string $type */
/** @var array|null $submission */
/** @var string|null $abstract_status */
/** @var string|null $success */
/** @var string|null $error */

$isAbstract = $type === 'abstract';
$title = $isAbstract ? 'Abstrak' : 'Full Paper';
$desc = $isAbstract
  ? 'Unggah file abstrak untuk tahap seleksi LKTI.'
  : 'Unggah naskah full paper kamu setelah abstrak disetujui.';

$status = $submission['status'] ?? null;
$hasFile = !empty($submission['value']);
$storedSize = '';
if ($hasFile) {
  $filePath = BASE_PATH . '/public/uploads/submissions/' . $submission['value'];
  if (is_file($filePath)) {
    $bytes = filesize($filePath);
    $storedSize = $bytes < 1024 * 1024
      ? round($bytes / 1024, 1) . ' KB'
      : round($bytes / (1024 * 1024), 2) . ' MB';
  }
}
$abstractApproved = $abstract_status === 'approved';
$paymentVerified = ($payment['status'] ?? '') === 'verified';

if ($isAbstract) {
  $locked = !$team || !$is_reviewed;
} else {
  $locked = !$team || !$is_reviewed || !$abstractApproved || !$paymentVerified;
}

$lockReason = null;
if ($locked) {
  if (!$team || !$is_reviewed) {
    $lockReason = 'reg';
  } elseif (!$abstractApproved) {
    $lockReason = 'abstract';
  } else {
    $lockReason = 'payment';
  }
}
?>
<div class="min-h-screen bg-gray-50">
  <?php $current = $isAbstract ? 'submission-abstract' : 'submission-full-paper';
  include BASE_PATH . '/src/Components/nav-tabs.php'; ?>

  <div class="px-4 sm:px-6 lg:px-8 py-4 mx-auto">
    <?php if ($success || $error): ?>
      <?= Toast::make()->variant($error ? 'error' : 'success')->message($error ?: $success)->render() ?>
    <?php endif; ?>

    <?php if ($locked): ?>
      <div class="relative">
        <div class="pointer-events-none select-none opacity-50">
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="border-b border-gray-200 px-5 py-3.5">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="font-display text-xl font-bold tracking-tight text-gray-900 mt-0.5"><?= $title ?></h2>
                <p class="text-sm text-gray-500 mt-0.5"><?= $desc ?></p>
              </div>
            </div>
          </div>

          <div class="p-5 sm:p-6">
            <form action="/submission/<?= $isAbstract ? 'abstract' : 'full-paper' ?>" method="POST" enctype="multipart/form-data" novalidate id="submission-form">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <fieldset>
                  <legend class="text-sm font-semibold text-gray-900">Kategori Karya <span class="text-red-500">*</span></legend>
                  <p class="text-xs text-gray-500 mt-0.5">Pilih salah satu kategori Karya Tulis Ilmiah yang ingin anda ikuti.</p>
                  <div class="grid grid-cols-1 gap-3 mt-3">
                    <?php $chosenCategory = $submission['category'] ?? null;
                    foreach (
                      [
                        'gagasan' => ['Gagasan', 'Menyelesaikan sebuah permasalahan dengan memberikan sebuah ide atau inovasi.', 'lightbulb'],
                        'prototype' => ['Prototype', 'Menyelesaikan sebuah permasalahan dengan menciptakan atau mengembangkan suatu alat.', 'wrench'],
                      ] as $value => [$label, $catDesc, $icon]
                    ): ?>
                      <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-gray-300 bg-transparent cursor-pointer transition-colors has-[:checked]:border-brand has-[:checked]:bg-red-50/60">
                        <?= Icon::make()->name($icon)->class('w-6 h-6 shrink-0 mt-0.5 text-brand') ?>
                        <span class="flex flex-col">
                          <span class="flex items-center gap-2">
                            <input type="radio" name="category" value="<?= $value ?>" required
                              <?= $chosenCategory === $value ? 'checked' : '' ?>
                              class="size-4 accent-[var(--color-brand)] shrink-0">
                            <span class="text-sm font-semibold text-gray-900"><?= $label ?></span>
                          </span>
                          <span class="text-sm mt-1 leading-relaxed"><?= $catDesc ?></span>
                        </span>
                      </label>
                    <?php endforeach; ?>
                  </div>
                  <p id="err-submission-category" class="text-xs text-red-500 mt-1.5 hidden">Pilih kategori karya terlebih dahulu.</p>
                </fieldset>

                <div>
                  <label class="block text-sm font-semibold mb-1">File <?= $title ?><span class="text-red-500">*</span></label>
                  <div data-slot="attachment" class="w-full" data-has-file="<?= $hasFile ? '1' : '0' ?>">
                    <label class="dropzone relative block w-full aspect-[16/9] rounded-2xl border-2 border-dashed border-gray-300 bg-transparent hover:border-brand transition-colors cursor-pointer overflow-hidden">

                      <div data-slot="attachment-idle" class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none p-6 text-center">
                        <?= Icon::make()->name('file-text')->class('size-14 mb-3') ?>
                        <p class="text-base font-semibold text-gray-800" data-slot="idle-title"><?= $hasFile ? htmlspecialchars($submission['value']) : 'Seret & lepas file ke sini' ?></p>
                        <p class="text-xs text-gray-500 mt-1" data-slot="idle-desc">
                          <?= $hasFile
                            ? ($storedSize ? ('PDF &#8226; ' . $storedSize) : 'PDF')
                            : 'atau klik untuk memilih file (PDF &#8226; maks 100MB)' ?>
                        </p>

                      </div>

                      <button type="button" data-clear-attachment
                        class="absolute top-4 right-4 z-20 <?= $hasFile ? 'flex' : 'hidden' ?> items-center justify-center w-10 h-10 bg-white rounded-full shadow-md border border-gray-200 hover:bg-red-50 hover:border-red-300 transition-colors">
                        <?= Icon::make()->name('trash-2')->class('w-5 h-5 text-red-500') ?>
                      </button>

                      <input type="file" name="doc_file" accept=".pdf,application/pdf" required
                        data-error="err-submission-file" data-max-size="<?= 100 * 1024 * 1024 ?>"
                        class="absolute inset-0 opacity-0 cursor-pointer z-10">
                    </label>
                  </div>

                  <p id="err-submission-file" class="text-xs text-red-500 mt-1.5 hidden">File wajib diupload</p>
                  <button type="submit" disabled id="submission-submit-btn" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-gray-300 rounded-xl transition-colors">
                    Simpan & upload <?= $title ?>
                    <?= Icon::make()->name('upload')->class('w-4 h-4') ?>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-2xl mt-4">
          <?= Icon::make()->name('megaphone')->class('w-5 h-5 text-blue-500 shrink-0 mt-0.5') ?>
          <div class="min-w-0">
            <p class="text-sm font-semibold text-blue-700">Format Penamaan File</p>
            <p class="text-xs text-blue-600 mt-0.5">Unggah file dengan format penamaan berikut agar mempermudah proses review:</p>
            <p class="text-sm font-semibold text-blue-700 mt-1 break-all">ABSTRAK_AW9_Nama Lengkap Ketua_Nama Sekolah_Judul Karya Tulis</p>
          </div>
        </div>

        <?php if ($locked): ?>
        </div>

        <div class="absolute inset-0 flex items-center justify-center z-10">
          <div class="w-32 h-32 rounded-full bg-white/90 shadow-xl flex items-center justify-center border border-gray-200">
            <?= Icon::make()->name('lock')->class('w-14 h-14 text-gray-400') ?>
          </div>
        </div>
      </div>

      <?php if ($lockReason === 'reg'): ?>
        <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl mt-4">
          <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-amber-500 shrink-0 mt-0.5') ?>
          <div>
            <p class="text-sm font-semibold text-amber-700">Mohon perhatian!</p>
            <p class="text-xs text-amber-600 mt-0.5"><?= $title ?> belum dapat diakses. Mohon <span class="font-semibold">selesaikan</span> seluruh tahap <span class="font-semibold">pendaftaran tim</span> terlebih dahulu.</p>
            <a href="/application" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 text-xs font-semibold text-amber-700 bg-amber-100 hover:bg-amber-200 rounded-lg transition-colors no-underline">
              <?= Icon::make()->name('arrow-left')->class('w-3.5 h-3.5') ?>
              Selesaikan pendaftaran
            </a>
          </div>
        </div>
      <?php elseif ($lockReason === 'abstract'): ?>
        <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl mt-4">
          <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-amber-500 shrink-0 mt-0.5') ?>
          <div>
            <p class="text-sm font-semibold text-amber-700">Abstrak belum disetujui</p>
            <p class="text-xs text-amber-600 mt-0.5">Full paper dapat diupload setelah abstrak kamu <span class="font-semibold">disetujui admin</span>. Pantau status pada halaman Abstrak.</p>
            <a href="/submission/abstract" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 text-xs font-semibold text-amber-700 bg-amber-100 hover:bg-amber-200 rounded-lg transition-colors no-underline">
              <?= Icon::make()->name('file-text')->class('w-3.5 h-3.5') ?>
              Lihat Status Abstrak
            </a>
          </div>
        </div>
      <?php else: ?>
        <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl mt-4">
          <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-amber-500 shrink-0 mt-0.5') ?>
          <div>
            <p class="text-sm font-semibold text-amber-700">Pembayaran belum selesai</p>
            <p class="text-xs text-amber-600 mt-0.5">Selesaikan <span class="font-semibold">pembayaran</span> terlebih dahulu untuk mengupload full paper.</p>
            <a href="/payments" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 text-xs font-semibold text-amber-700 bg-amber-100 hover:bg-amber-200 rounded-lg transition-colors no-underline">
              <?= Icon::make()->name('credit-card')->class('w-3.5 h-3.5') ?>
              Lanjut ke Pembayaran
            </a>
          </div>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <?php if ($status === 'submitted'): ?>
        <div class="flex items-start gap-3 p-4 bg-yellow-50 border border-yellow-200 rounded-2xl mt-4">
          <?= Icon::make()->name('clock')->class('w-5 h-5 text-yellow-500 shrink-0 mt-0.5') ?>
          <div>
            <p class="text-sm font-semibold text-yellow-700">Menunggu Review</p>
            <p class="text-xs text-yellow-600 mt-0.5"><?= $title ?> kamu sedang ditinjau oleh admin<?= $isAbstract ? '. Kamu dapat melanjutkan ke pembayaran setelah disetujui.' : '.' ?></p>
          </div>
        </div>
      <?php elseif ($status === 'approved'): ?>
        <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-2xl mt-4">
          <?= Icon::make()->name('check-circle')->class('w-5 h-5 text-green-500 shrink-0 mt-0.5') ?>
          <div>
            <p class="text-sm font-semibold text-green-700"><?= $title ?> Disetujui</p>
            <p class="text-xs text-green-600 mt-0.5"><?= $isAbstract ? 'Kamu dapat melanjutkan ke pembayaran.' : 'Terima kasih, full paper kamu sudah diterima.' ?></p>
            <?php if ($isAbstract && !$paymentVerified): ?>
              <a href="/payments" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 text-xs font-semibold text-green-700 bg-green-100 hover:bg-green-200 rounded-lg transition-colors no-underline">
                Lanjut ke Pembayaran
                <?= Icon::make()->name('chevron-right')->class('w-3.5 h-3.5') ?>
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php elseif ($status === 'rejected'): ?>
        <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl mt-4">
          <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-red-500 shrink-0 mt-0.5') ?>
          <div>
            <p class="text-sm font-semibold text-red-700"><?= $title ?> Ditolak</p>
            <p class="text-xs text-red-600 mt-0.5">Silakan perbaiki dan upload ulang file <?= htmlspecialchars(strtolower($title)) ?> kamu.</p>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <?php if ($hasFile): ?>
    <?= (new Dialog())->id('confirm-change-doc')->title('Apakah anda yakin?')->width('max-w-md')->content(
      '<div class="flex items-start gap-3">'
        . '<p class="text-sm text-gray-600">File yang sudah diupload akan diganti dengan file baru yang kamu pilih.</p>'
        . '</div>'
        . '<div class="flex justify-end gap-2 mt-5">'
        . '<button type="button" id="confirm-doc-no" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>'
        . '<button type="button" id="confirm-doc-yes" class="px-4 py-2 text-sm font-semibold text-white bg-brand hover:bg-red-800 rounded-xl transition-colors">Ya, Ganti</button>'
        . '</div>'
    ) ?>

    <?= (new Dialog())->id('confirm-change-category')->title('Apakah anda yakin?')->width('max-w-md')->content(
      '<div class="flex items-start gap-3">'
        . '<p class="text-sm text-gray-600">Apakah anda ingin mengubah <span class="font-semibold">kategori</span> dengan <span class="font-semibold">file</span> yang sudah ada. Perubahan ini sangat berpengaruh pada pengumpulan karya kamu</p>'
        . '</div>'
        . '<div class="flex justify-end gap-2 mt-5">'
        . '<button type="button" id="confirm-category-no" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>'
        . '<button type="button" id="confirm-category-yes" class="px-4 py-2 text-sm font-semibold text-white bg-brand hover:bg-red-800 rounded-xl transition-colors">Ya, Simpan</button>'
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
      if (id === 'confirm-change-doc' && !confirmed && pendingInput) {
        pendingInput.value = '';
        pendingInput = null;
        refresh();
      }
      origClose(id);
    };

    var form = document.getElementById('submission-form');
    if (!form) return;

    var att = form.querySelector('[data-slot="attachment"]');
    var hasShown = att.dataset.hasFile === '1';
    var idleTitle = att.querySelector('[data-slot="idle-title"]');
    var idleDesc = att.querySelector('[data-slot="idle-desc"]');
    var clearBtn = att.querySelector('[data-clear-attachment]');
    var input = form.querySelector('input[type="file"]');
    var submitBtn = document.getElementById('submission-submit-btn');
    var errEl = document.getElementById('err-submission-file');
    var errCatEl = document.getElementById('err-submission-category');
    var radios = form.querySelectorAll('input[name="category"]');
    var originalCategory = (form.querySelector('input[name="category"]:checked') || {}).value || null;
    var catConfirmed = false;

    function categoryChosen() {
      return !!form.querySelector('input[name="category"]:checked');
    }

    function categoryChanged() {
      var checked = form.querySelector('input[name="category"]:checked');
      return originalCategory !== null && checked && checked.value !== originalCategory;
    }

    function refresh() {
      setEnabled(categoryChosen() && (input.files.length > 0 || categoryChanged()));
    }

    function setEnabled(on) {
      submitBtn.disabled = !on;
      submitBtn.classList.toggle('bg-gray-300', !on);
      submitBtn.classList.toggle('cursor-not-allowed', !on);
      submitBtn.classList.toggle('bg-brand', on);
      submitBtn.classList.toggle('hover:bg-red-800', on);
      submitBtn.classList.toggle('cursor-pointer', on);
    }

    function showFile(file) {
      idleTitle.textContent = file.name;
      idleDesc.textContent = 'PDF \u2022 ' + formatFileSize(file.size);
      clearBtn.classList.remove('hidden');
      clearBtn.classList.add('flex');
      hasShown = true;
    }

    function processFile(target) {
      var file = target.files && target.files[0];
      if (!file) return;

      var maxSize = parseInt(target.dataset.maxSize);
      if (file.size > maxSize) {
        target.value = '';
        errEl.textContent = 'File terlalu besar. Maksimal ' + formatFileSize(maxSize);
        errEl.classList.remove('hidden');
        refresh();
        return;
      }
      if (!/\.pdf$/i.test(file.name)) {
        target.value = '';
        errEl.textContent = 'Hanya file PDF yang diizinkan.';
        errEl.classList.remove('hidden');
        refresh();
        return;
      }

      errEl.classList.add('hidden');
      showFile(file);
      refresh();
    }

    input.addEventListener('change', function(e) {
      if (!e.target.files || !e.target.files[0]) return;
      if (!confirmed && hasShown) {
        pendingInput = e.target;
        openDialog('confirm-change-doc');
        return;
      }
      confirmed = false;
      processFile(e.target);
    });

    radios.forEach(function(radio) {
      radio.addEventListener('change', function() {
        errCatEl.classList.add('hidden');
        refresh();
      });
    });

    clearBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      input.value = '';
      idleTitle.textContent = 'Seret & lepas file ke sini';
      idleDesc.textContent = 'atau klik untuk memilih file (PDF, maks 100MB)';
      clearBtn.classList.add('hidden');
      clearBtn.classList.remove('flex');
      hasShown = false;

      refresh();
    });

    var yesBtn = document.getElementById('confirm-doc-yes');
    if (yesBtn) yesBtn.addEventListener('click', function() {
      confirmed = true;
      closeDialog('confirm-change-doc');
      if (pendingInput) processFile(pendingInput);
      pendingInput = null;
      confirmed = false;
    });
    var noBtn = document.getElementById('confirm-doc-no');
    if (noBtn) noBtn.addEventListener('click', function() {
      closeDialog('confirm-change-doc');
    });

    var zone = form.querySelector('.dropzone');
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
      if (e.dataTransfer.files.length) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
      }
    });

    form.addEventListener('submit', function(e) {
      var hasFileStaged = input.files && input.files.length > 0;

      if (!categoryChosen()) {
        e.preventDefault();
        errCatEl.classList.remove('hidden');
        return;
      }

      if (hasFileStaged) return;

      if (categoryChanged()) {
        if (!catConfirmed) {
          e.preventDefault();
          openDialog('confirm-change-category');
        }
        return;
      }

      e.preventDefault();
      errEl.textContent = 'Pilih file baru atau ubah kategori terlebih dahulu.';
      errEl.classList.remove('hidden');
    });

    var catYesBtn = document.getElementById('confirm-category-yes');
    if (catYesBtn) catYesBtn.addEventListener('click', function() {
      catConfirmed = true;
      closeDialog('confirm-change-category');
      form.submit();
    });
    var catNoBtn = document.getElementById('confirm-category-no');
    if (catNoBtn) catNoBtn.addEventListener('click', function() {
      closeDialog('confirm-change-category');
    });
  });
</script>