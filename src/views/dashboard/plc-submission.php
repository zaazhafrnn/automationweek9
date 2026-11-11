<?php

use App\Components\Icon;
use App\Components\Toast;

/** @var string $csrf_token */
/** @var string $user_name */
/** @var array $team */
/** @var array $rounds */
/** @var bool $payment_verified */
/** @var bool $round3_eligible */
/** @var string|null $success */
/** @var string|null $error */

?>
<div class="min-h-screen bg-gray-50">
  <?php $current = 'submission-plc';
  include BASE_PATH . '/src/Components/page-loading.php'; ?>
  <?php include BASE_PATH . '/src/Components/nav-tabs.php'; ?>

  <div class="px-4 sm:px-6 lg:px-8 py-4 mx-auto">
    <?php if ($success || $error): ?>
      <?= Toast::make()->variant($error ? 'error' : 'success')->message($error ?: $success)->render() ?>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
      <div class="border-b border-gray-200 px-5 py-3.5">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="font-display text-xl font-bold tracking-tight text-gray-900 mt-0.5">Pengumpulan Karya PLC</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kirimkan link program / karya untuk setiap round yang telah dibuka.</p>
          </div>
        </div>
      </div>
      <div class="p-5 sm:p-6">
        <?php if (!$payment_verified): ?>
          <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
            <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-amber-500 shrink-0 mt-0.5') ?>
            <div>
              <p class="text-sm font-semibold text-amber-700">Menunggu Verifikasi</p>
              <p class="text-xs text-amber-600 mt-0.5">Pengumpulan dapat dibuka setelah pembayaran tim kamu diverifikasi admin.</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="space-y-4">
      <?php foreach ($rounds as $number => $round):
        $roundNumber = (int) $number;
        $row = $round['row'];
        $isFinal = $roundNumber === 3;
        $eligible = $round3_eligible && ($roundNumber !== 3);
        $open = !empty($round['open']);
        $lockedByPayment = !$payment_verified;
        $disabled = $lockedByPayment || !$open || ($isFinal && !$eligible);
        $submitted = !empty($row['value']);
      ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden <?= $disabled ? 'opacity-70' : '' ?>">
          <div class="border-b border-gray-200 px-5 py-3.5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <span class="w-9 h-9 rounded-xl bg-brand/10 text-brand font-bold flex items-center justify-center text-sm font-display">R<?= $roundNumber ?></span>
              <div>
                <h3 class="font-bold text-gray-900"><?= htmlspecialchars($round['label']) ?></h3>
                <p class="text-xs text-gray-500 mt-0.5"><?= $roundNumber === 1 || $roundNumber === 2 ? 'Kirimkan link penyelesaian.' : 'Kirimkan link presentasi / karya final.' ?></p>
              </div>
            </div>
            <?php if ($submitted): ?>
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-100 text-green-800">
                <?= Icon::make()->name('check')->class('w-3.5 h-3.5') ?>
                Tersimpan
              </span>
            <?php elseif ($open && !$isFinal && !$lockedByPayment): ?>
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-800">
                <?= Icon::make()->name('lock')->class('w-3.5 h-3.5') ?>
                Terbuka
              </span>
            <?php else: ?>
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600">
                <?= Icon::make()->name('clock')->class('w-3.5 h-3.5') ?>
                <?= $submitted ? 'Tutup' : 'Tidak Terbuka' ?>
              </span>
            <?php endif; ?>
          </div>

          <div class="p-5 sm:p-6">
            <?php if ($isFinal && !$eligible): ?>
              <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-amber-500 shrink-0 mt-0.5') ?>
                <p class="text-sm text-amber-800">Round 3 hanya untuk 20 tim terbaik hasil Round 1 &amp; Round 2.</p>
              </div>
            <?php elseif (!$open): ?>
              <div class="flex items-start gap-3 p-4 bg-gray-50 border border-gray-200 rounded-2xl">
                <?= Icon::make()->name('clock')->class('w-5 h-5 text-gray-500 shrink-0 mt-0.5') ?>
                <p class="text-sm text-gray-600">Pengumpulan round ini sedang ditutup. Tim kamu belum bisa mengirimkan link.</p>
              </div>
            <?php else: ?>
              <?php if ($submitted && !empty($row['value'])): ?>
                <p class="text-xs text-gray-500 mb-3">Link tersimpan. Kamu bisa memperbarui selama round masih terbuka.</p>
              <?php endif; ?>
              <form action="/submission/plc" method="POST" class="<?= $disabled ? 'pointer-events-none opacity-60 select-none' : '' ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="round" value="<?= $roundNumber ?>">
                <label class="block text-sm font-semibold mb-2">
                  Link <?= $roundNumber === 1 || $roundNumber === 2 ? 'Penyelesaian' : 'Karya' ?>
                  <?php if ($open) : ?> <span class="text-red-500">*</span><?php endif; ?>
                </label>
                <div class="flex flex-col sm:flex-row gap-3">
                  <input type="url" name="link" required value="<?= htmlspecialchars($row['value'] ?? '') ?>"
                    placeholder="https://drive.google.com/..."
                    class="flex-1 h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand/20 focus-visible:border-brand "
                    <?= $disabled ? 'disabled' : '' ?>>
                  <button type="submit" <?= $disabled ? 'disabled' : '' ?> class="h-11 px-4 rounded-xl bg-brand text-white text-sm font-semibold hover:bg-red-800 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    Simpan Link
                  </button>
                </div>
                <?php if ($submitted && !empty($row['value'])): ?>
                  <p class="text-xs text-gray-500 mt-3 break-all">
                    Link tersimpan: <a href="<?= htmlspecialchars($row['value'], ENT_QUOTES) ?>" target="_blank" rel="noopener" class="text-brand font-semibold hover:underline"><?= htmlspecialchars($row['value']) ?></a>
                  </p>
                <?php endif; ?>
              </form>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php include BASE_PATH . '/src/Components/footer.php'; ?>
</div>
