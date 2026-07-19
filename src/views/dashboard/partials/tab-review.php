<?php
use App\Components\Attachment;
use App\Components\Icon;

/** @var array|null $team */
/** @var array|null $payment */
/** @var string $csrf_token */
/** @var array $uploads */
$DIVISION_LABELS = ['LF' => 'Line Follower', 'PLC' => 'Programmable Logic Controller', 'FFR' => 'Fire Fighting Robot', 'LKTI' => 'Lomba Karya Tulis Ilmiah'];
$UPLOAD_URL = '/uploads/teams/';
?>
<div class="space-y-6">
  <!-- Team -->
  <form action="/dashboard/team/update" method="POST" class="review-form bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Registrasi Tim</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Tim <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="<?= htmlspecialchars($team['name'] ?? '') ?>" required class="review-field w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Asal Sekolah <span class="text-red-500">*</span></label>
        <input type="text" name="teamSchool" value="<?= htmlspecialchars($team['teamSchool'] ?? '') ?>" required class="review-field w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
      </div>
    </div>
    <div class="mt-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        <?php foreach ($DIVISION_LABELS as $k => $v): ?>
          <label class="block rounded-lg border-2 p-3 cursor-pointer has-[:checked]:border-brand has-[:checked]:bg-brand/5 transition-all <?= ($team['division'] ?? '') === $k ? 'border-brand bg-brand/5' : 'border-gray-200' ?>">
            <input type="radio" name="division" value="<?= $k ?>" class="hidden review-field" <?= ($team['division'] ?? '') === $k ? 'checked' : '' ?>>
            <p class="text-xs font-medium text-gray-900"><?= htmlspecialchars($v) ?></p>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="mt-4 flex justify-end">
      <button type="submit" class="review-btn text-xs font-medium text-gray-400 border border-gray-200 px-3 py-1.5 rounded-lg transition-colors">Simpan</button>
    </div>
  </form>

  <!-- Anggota -->
  <form action="/dashboard/team/update" method="POST" enctype="multipart/form-data" class="review-form bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <input type="hidden" name="division" value="<?= htmlspecialchars($team['division']) ?>">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Data Anggota</h3>
    <?php
    $reviewMembers = [
      ['num' => 1, 'label' => 'Ketua Tim', 'nameKey' => 'leaderName', 'phoneKey' => 'leaderPhoneNumber'],
    ];
    $hasM2 = !empty($team['firstMemberName']);
    $hasM3 = !empty($team['secondMemberName']);
    if ($hasM2) $reviewMembers[] = ['num' => 2, 'label' => 'Anggota 2', 'nameKey' => 'firstMemberName', 'phoneKey' => 'firstMemberPhoneNumber'];
    if ($hasM3) $reviewMembers[] = ['num' => 3, 'label' => 'Anggota 3', 'nameKey' => 'secondMemberName', 'phoneKey' => 'secondMemberPhoneNumber'];
    ?>
    <div class="space-y-4">
      <?php foreach ($reviewMembers as $m): ?>
        <div>
          <p class="text-xs font-semibold text-gray-800 mb-2"><?= htmlspecialchars($m['label']) ?></p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <input type="text" name="<?= $m['nameKey'] ?>" value="<?= htmlspecialchars($team[$m['nameKey']] ?? '') ?>" placeholder="Nama Lengkap" required class="review-field w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
            <input type="text" name="<?= $m['phoneKey'] ?>" value="<?= htmlspecialchars($team[$m['phoneKey']] ?? '') ?>" placeholder="No. Telepon" required class="review-field w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="mt-4 flex justify-end">
      <button type="submit" class="review-btn text-xs font-medium text-gray-400 border border-gray-200 px-3 py-1.5 rounded-lg transition-colors">Simpan</button>
    </div>
  </form>

  <!-- Media Sosial -->
  <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Media Sosial</h3>
    <?php
    $sosmedMembers = [
      ['num' => 1, 'name' => $team['leaderName'] ?? 'Ketua Tim'],
    ];
    if ($hasM2) $sosmedMembers[] = ['num' => 2, 'name' => $team['firstMemberName'] ?? 'Anggota 2'];
    if ($hasM3) $sosmedMembers[] = ['num' => 3, 'name' => $team['secondMemberName'] ?? 'Anggota 3'];
    ?>
    <div class="space-y-4">
      <?php foreach ($sosmedMembers as $m):
        $p = $m['num'];
        $ig = $uploads[$p]['ig_follow'] ?? null;
        $twibbon = $uploads[$p]['twibbon'] ?? null;
      ?>
        <div>
          <p class="text-xs font-semibold text-gray-800 mb-2"><?= htmlspecialchars($m['name']) ?></p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <p class="text-xs text-gray-500 mb-1">Bukti Follow IG</p>
              <?php if ($ig): ?>
                <?= Attachment::make()
                    ->state('done')
                    ->mediaVariant('image')
                    ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($ig) . '" class="w-full h-full object-cover">')
                    ->title(basename($ig))
                    ->description('Sudah diupload')
                    ->render() ?>
              <?php else: ?>
                <div class="flex items-center gap-2 p-3 bg-gray-50 border border-dashed border-gray-200 rounded-xl">
                  <?= Icon::make()->name('image')->class('w-4 h-4 text-gray-300 shrink-0') ?>
                  <p class="text-xs text-gray-400">Belum diupload</p>
                </div>
              <?php endif; ?>
            </div>
            <div>
              <p class="text-xs text-gray-500 mb-1">Twibbon</p>
              <?php if ($twibbon): ?>
                <?= Attachment::make()
                    ->state('done')
                    ->mediaVariant('image')
                    ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($twibbon) . '" class="w-full h-full object-cover">')
                    ->title(basename($twibbon))
                    ->description('Sudah diupload')
                    ->render() ?>
              <?php else: ?>
                <div class="flex items-center gap-2 p-3 bg-gray-50 border border-dashed border-gray-200 rounded-xl">
                  <?= Icon::make()->name('image')->class('w-4 h-4 text-gray-300 shrink-0') ?>
                  <p class="text-xs text-gray-400">Belum diupload</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Pembayaran -->
  <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-3">Bukti Pembayaran</h3>
    <?php if ($payment && !empty($payment['proofImage'])): ?>
      <?= Attachment::make()
          ->state('done')
          ->mediaVariant('image')
          ->media('<img src="/uploads/payments/' . htmlspecialchars($payment['proofImage']) . '" class="w-full h-full object-cover">')
          ->title(basename($payment['proofImage']))
          ->description('Sudah diupload')
          ->render() ?>
    <?php else: ?>
      <div class="flex items-center gap-2 p-3 bg-gray-50 border border-dashed border-gray-200 rounded-xl">
        <?= Icon::make()->name('upload')->class('w-4 h-4 text-gray-300 shrink-0') ?>
        <p class="text-xs text-gray-400">Upload bukti pembayaran di tab Pembayaran</p>
      </div>
    <?php endif; ?>
  </div>
</div>
