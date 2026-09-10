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
/** @var array|null $originality */
/** @var array|null $approval */
/** @var string|null $abstract_status */
/** @var string|null $abstract_category */
/** @var string|null $success */
/** @var string|null $error */

$isAbstract = $type === 'abstract';
$title = $isAbstract ? 'Abstrak' : 'Full Paper';
$desc = $isAbstract
  ? 'Unggah file abstrak untuk tahap seleksi LKTI.'
  : 'Unggah naskah full paper kamu setelah abstrak disetujui.';

$status = $submission['status'] ?? null;
$hasFile = !empty($submission['value']);
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

$approved = $status === 'approved';
$abstractFormat = 'ABSTRAK_AW9_Nama Lengkap Ketua_Nama Sekolah_Judul Karya Tulis';
$originalityFormat = 'SURAT ORISINALITAS_AW9_Nama Lengkap Ketua_Nama Sekolah';
$approvalFormat = 'LEMBAR PENGESAHAN_AW9_Nama Lengkap Ketua_Nama Sekolah';
$nameFormat = ($isAbstract ? $abstractFormat : 'FULLPAPER_AW9_Nama Lengkap Ketua_Nama Sekolah_Judul Karya Tulis');
$formatTemplateLink = 'https://drive.google.com/drive/folders/1ZqorKKptIvz1DxWnEhuq3WUZZEsV1igr?usp=sharing';

if (!function_exists('submission_slot')) {
  function submission_slot(string $inputName, string $label, string $format, array|false $row, string $errId, ?string $templateLink = null): string
  {
    $hasFile = !empty($row['value']);
    $storedSize = '';
    if ($hasFile) {
      $filePath = BASE_PATH . '/public/uploads/submissions/' . $row['value'];
      if (is_file($filePath)) {
        $bytes = filesize($filePath);
        $storedSize = $bytes < 1024 * 1024
          ? round($bytes / 1024, 1) . ' KB'
          : round($bytes / (1024 * 1024), 2) . ' MB';
      }
    }
    ob_start(); ?>
    <div>
      <label class="block text-sm font-semibold mb-1">File <?= $label ?><span class="text-red-500">*</span><?php if ($templateLink): ?> <a href="<?= htmlspecialchars($templateLink) ?>" target="_blank" data-tooltip="Unduh template <?= htmlspecialchars(strtolower($label)) ?>" class="text-brand font-semibold hover:underline">Unduh Template</a><?php endif; ?></label>
      <p class="text-xs text-gray-500 mb-2">Unggah file dengan format nama <strong class="font-semibold text-gray-700"><?= $format ?></strong></p>
      <div data-slot="attachment" class="w-full" data-has-file="<?= $hasFile ? '1' : '0' ?>">
        <label class="dropzone relative block w-full aspect-[16/9] rounded-2xl border-2 border-dashed border-gray-300 bg-transparent hover:border-brand transition-colors cursor-pointer overflow-hidden">

          <div data-slot="attachment-idle" class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none p-6 text-center">
            <?= Icon::make()->name('file-text')->class('size-14 mb-3') ?>
            <p class="text-base font-semibold text-gray-800" data-slot="idle-title"><?= $hasFile ? htmlspecialchars($row['value']) : 'Seret & lepas file ke sini' ?></p>
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

          <input type="file" name="<?= $inputName ?>" accept=".pdf,application/pdf" required
            data-error="<?= $errId ?>" data-max-size="<?= 100 * 1024 * 1024 ?>"
            class="absolute inset-0 opacity-0 cursor-pointer z-10">
        </label>
      </div>
      <p id="<?= $errId ?>" class="text-xs text-red-500 mt-1.5 hidden">File wajib diupload</p>
    </div>
<?php return ob_get_clean();
  }
}

$anyFile = $hasFile || !empty($originality['value']) || !empty($approval['value']);
?>
<div class="min-h-screen bg-gray-50">
  <?php $current = $isAbstract ? 'submission-abstract' : 'submission-full-paper';
  include BASE_PATH . '/src/Components/page-loading.php'; ?>
  <?php include BASE_PATH . '/src/Components/nav-tabs.php'; ?>

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
            <form action="/submission/<?= $isAbstract ? 'abstract' : 'full-paper' ?>" method="POST" enctype="multipart/form-data" novalidate id="submission-form" data-abstract="<?= $isAbstract ? '1' : '0' ?>">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 <?= $approved ? 'pointer-events-none opacity-60 select-none' : '' ?>">
                <fieldset>
                  <legend class="text-sm font-semibold text-gray-900">Kategori Karya <span class="text-red-500">*</span></legend>
                  <p class="text-xs text-gray-500 mt-0.5"><?= $isAbstract ? 'Pilih salah satu kategori Karya Tulis Ilmiah yang ingin anda ikuti.' : 'Kategori mengikuti pilihan pada abstrak kamu yang sudah disetujui.' ?></p>
                  <div class="grid grid-cols-1 gap-3 mt-3">
                    <?php $chosenCategory = $isAbstract ? ($submission['category'] ?? null) : ($abstract_category ?? null);
                    foreach (
                      [
                        'gagasan' => ['Gagasan', 'Menyelesaikan sebuah permasalahan dengan memberikan sebuah ide atau inovasi.', 'lightbulb'],
                        'prototype' => ['Prototype', 'Menyelesaikan sebuah permasalahan dengan menciptakan atau mengembangkan suatu alat.', 'wrench'],
                      ] as $value => [$label, $catDesc, $icon]
                    ): ?>
                      <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 <?= $isAbstract ? 'border-gray-200 hover:border-gray-300 bg-transparent cursor-pointer transition-colors' : 'border-gray-200 bg-gray-50 cursor-not-allowed opacity-75' ?> has-[:checked]:border-brand has-[:checked]:bg-red-50/60">
                        <?= Icon::make()->name($icon)->class('w-6 h-6 shrink-0 mt-0.5 text-brand') ?>
                        <span class="flex flex-col">
                          <span class="flex items-center gap-2">
                            <input type="radio" name="category" value="<?= $value ?>" required
                              <?= $chosenCategory === $value ? 'checked' : '' ?>
                              <?= $isAbstract ? '' : 'disabled' ?>
                              class="size-4 accent-[var(--color-brand)] shrink-0 <?= $isAbstract ? 'cursor-pointer' : 'cursor-not-allowed' ?>">
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
                  <?= submission_slot('doc_file', $title, $nameFormat, $submission, 'err-submission-file') ?>
                </div>

                <?php if ($isAbstract): ?>
                  <div>
                    <?= submission_slot('doc_originality', 'Lembar Pernyataan Orisinalitas Karya', $originalityFormat, $originality, 'err-submission-originality', $formatTemplateLink) ?>
                  </div>
                  <div>
                    <?= submission_slot('doc_approval', 'Lembar Pengesahan Karya', $approvalFormat, $approval, 'err-submission-approval', $formatTemplateLink) ?>
                  </div>
                <?php endif; ?>
              </div>

              <div class="mt-6 flex justify-end">
                <button type="submit" disabled id="submission-submit-btn" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-gray-300 rounded-xl transition-colors <?= $approved ? 'hidden' : '' ?>">
                  Simpan
                  <?= Icon::make()->name('upload')->class('w-4 h-4') ?>
                </button>
              </div>
            </form>
          </div>
        </div>

        <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-2xl mt-4">
          <?= Icon::make()->name('megaphone')->class('w-5 h-5 text-blue-500 shrink-0 mt-0.5') ?>
          <div class="min-w-0">
            <p class="text-sm font-semibold text-blue-700">Harap perhatikan!</p>
            <p class="text-xs text-blue-600 mt-0.5">Ikuti format penamaan file yang tertera di masing-masing kolom agar mempermudah proses review.</p>
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
          <?= Icon::make()->name('check')->class('w-5 h-5 text-green-500 shrink-0 mt-0.5') ?>
          <div>
            <p class="text-sm font-semibold text-green-700">Selamat! Anda lolos tahap seleksi <?= $title ?>!</p>
            <p class="text-xs text-green-600 mt-0.5"><?= $isAbstract ? 'Kamu dapat melanjutkan ke pembayaran' : 'Terima kasih, full paper kamu sudah diterima.' ?></p>
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

  <?php if ($anyFile): ?>
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

    var pendingSlot = null;
    var confirmed = false;

    var origClose = window.closeDialog;
    window.closeDialog = function(id) {
      if (id === 'confirm-change-doc' && !confirmed && pendingSlot) {
        pendingSlot.input.value = '';
        pendingSlot = null;
        refresh();
      }
      origClose(id);
    };

    var form = document.getElementById('submission-form');
    if (!form) return;

    var isAbstract = form.dataset.abstract === '1';
    var slots = [].map.call(form.querySelectorAll('[data-slot="attachment"]'), function(att) {
      var input = att.querySelector('input[type="file"]');
      return {
        input: input,
        title: att.querySelector('[data-slot="idle-title"]'),
        desc: att.querySelector('[data-slot="idle-desc"]'),
        clear: att.querySelector('[data-clear-attachment]'),
        zone: att.querySelector('.dropzone'),
        err: document.getElementById(input.dataset.error),
        hasFile: att.dataset.hasFile === '1'
      };
    });
    var submitBtn = document.getElementById('submission-submit-btn');
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

    function stagedAny() {
      return slots.some(function(s) {
        return s.input.files && s.input.files.length > 0;
      });
    }

    function allFilled() {
      return categoryChosen() && slots.every(function(s) {
        return s.hasFile || (s.input.files && s.input.files.length > 0);
      });
    }

    function refresh() {
      if (isAbstract) {
        setEnabled(allFilled());
      } else {
        setEnabled(categoryChosen() && (stagedAny() || categoryChanged()));
      }
    }

    function setEnabled(on) {
      submitBtn.disabled = !on;
      submitBtn.classList.toggle('bg-gray-300', !on);
      submitBtn.classList.toggle('cursor-not-allowed', !on);
      submitBtn.classList.toggle('bg-brand', on);
      submitBtn.classList.toggle('hover:bg-red-800', on);
      submitBtn.classList.toggle('cursor-pointer', on);
    }

    function showErr(slot, msg) {
      slot.err.textContent = msg;
      slot.err.classList.remove('hidden');
    }

    function showFile(slot, file) {
      slot.title.textContent = file.name;
      slot.desc.textContent = 'PDF \u2022 ' + formatFileSize(file.size);
      slot.clear.classList.remove('hidden');
      slot.clear.classList.add('flex');
      slot.hasFile = true;
    }

    function processFile(slot) {
      var file = slot.input.files && slot.input.files[0];
      if (!file) return;

      var maxSize = parseInt(slot.input.dataset.maxSize);
      if (file.size > maxSize) {
        slot.input.value = '';
        showErr(slot, 'File terlalu besar. Maksimal ' + formatFileSize(maxSize));
        refresh();
        return;
      }
      if (!/\.pdf$/i.test(file.name)) {
        slot.input.value = '';
        showErr(slot, 'Hanya file PDF yang diizinkan.');
        refresh();
        return;
      }

      slot.err.classList.add('hidden');
      showFile(slot, file);
      refresh();
    }

    slots.forEach(function(slot) {
      slot.input.addEventListener('change', function(e) {
        if (!e.target.files || !e.target.files[0]) return;
        if (!confirmed && slot.hasFile) {
          pendingSlot = slot;
          openDialog('confirm-change-doc');
          return;
        }
        confirmed = false;
        processFile(slot);
      });

      slot.clear.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        slot.input.value = '';
        slot.title.textContent = 'Seret & lepas file ke sini';
        slot.desc.textContent = 'atau klik untuk memilih file (PDF, maks 100MB)';
        slot.clear.classList.add('hidden');
        slot.clear.classList.remove('flex');
        slot.hasFile = false;

        refresh();
      });

      slot.zone.addEventListener('dragover', function(e) {
        e.preventDefault();
        slot.zone.classList.add('border-brand', 'bg-red-50');
      });
      slot.zone.addEventListener('dragleave', function() {
        slot.zone.classList.remove('border-brand', 'bg-red-50');
      });
      slot.zone.addEventListener('drop', function(e) {
        e.preventDefault();
        slot.zone.classList.remove('border-brand', 'bg-red-50');
        if (e.dataTransfer.files.length) {
          slot.input.files = e.dataTransfer.files;
          slot.input.dispatchEvent(new Event('change'));
        }
      });
    });

    radios.forEach(function(radio) {
      radio.addEventListener('change', function() {
        errCatEl.classList.add('hidden');
        refresh();
      });
    });

    var yesBtn = document.getElementById('confirm-doc-yes');
    if (yesBtn) yesBtn.addEventListener('click', function() {
      confirmed = true;
      closeDialog('confirm-change-doc');
      if (pendingSlot) processFile(pendingSlot);
      pendingSlot = null;
      confirmed = false;
    });
    var noBtn = document.getElementById('confirm-doc-no');
    if (noBtn) noBtn.addEventListener('click', function() {
      closeDialog('confirm-change-doc');
    });

    form.addEventListener('submit', function(e) {
      if (!categoryChosen()) {
        e.preventDefault();
        errCatEl.classList.remove('hidden');
        return;
      }

      if (!allFilled()) {
        e.preventDefault();
        slots.forEach(function(s) {
          if (!(s.hasFile || (s.input.files && s.input.files.length > 0))) {
            showErr(s, 'File wajib diupload');
          }
        });
        return;
      }

      if (categoryChanged() && !catConfirmed) {
        e.preventDefault();
        openDialog('confirm-change-category');
        return;
      }
    });

    var catYesBtn = document.getElementById('confirm-category-yes');
    if (catYesBtn) catYesBtn.addEventListener('click', function() {
      catConfirmed = true;
      closeDialog('confirm-change-category');
      __showLoading();
      form.submit();
    });
    var catNoBtn = document.getElementById('confirm-category-no');
    if (catNoBtn) catNoBtn.addEventListener('click', function() {
      closeDialog('confirm-change-category');
    });

    refresh();
  });
</script>