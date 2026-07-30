<?php

use App\Components\Attachment;
use App\Components\Icon;

/** @var array|null $team */
/** @var string $csrf_token */
/** @var array $uploads */

$DIVISION_LABELS = ['LF' => 'Line Follower', 'PLC' => 'Programmable Logic Controller', 'FFR' => 'Fire Fighting Robot', 'LKTI' => 'Lomba Karya Tulis Ilmiah', 'PROG' => 'Program'];
$UPLOAD_URL = '/uploads/teams/';
$origDivision = $team['division'] ?? '';
$hasM2 = !empty($team['firstMemberName']);
$hasM3 = !empty($team['secondMemberName']);
?>
<div class="space-y-6">
  <form action="/application/team/update" method="POST" class="review-form bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Registrasi Tim</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium  mb-1.5">Nama Tim <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="<?= htmlspecialchars($team['name'] ?? '') ?>" required placeholder="Masukkan nama tim" class="review-field w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
      </div>
      <div>
        <label class="block text-sm font-medium  mb-1.5">Asal Sekolah <span class="text-red-500">*</span></label>
        <input type="text" name="teamSchool" value="<?= htmlspecialchars($team['teamSchool'] ?? '') ?>" required placeholder="Masukkan nama sekolah" class="review-field w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
      </div>
    </div>
    <div class="mt-4">
      <label class="block text-sm font-medium  mb-2">Divisi</label>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2" id="reviewDivision">
        <?php foreach ($DIVISION_LABELS as $k => $v):
          $isOrig = $origDivision === $k;
        ?>
          <label class="review-division block rounded-lg border-2 p-3 cursor-pointer has-[:checked]:border-brand has-[:checked]:bg-brand/5 transition-all <?= $isOrig ? 'border-brand bg-brand/5' : 'border-gray-200' ?>"
            data-orig="<?= $isOrig ? '1' : '0' ?>">
            <input type="radio" name="division" value="<?= $k ?>" class="hidden" <?= $isOrig ? 'checked' : '' ?>>
            <p class="text-xs font-medium text-gray-900"><?= htmlspecialchars($v) ?></p>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="mt-4 flex justify-end">
      <button type="submit" class="review-btn text-xs font-medium text-gray-400 border border-gray-200 px-3 py-1.5 rounded-lg transition-colors">Simpan</button>
    </div>
  </form>

  <form action="/application/team/update" method="POST" enctype="multipart/form-data" class="review-form bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <input type="hidden" name="division" value="<?= htmlspecialchars($team['division']) ?>">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Data Anggota</h3>
    <?php
    $reviewMembers = [
      ['num' => 1, 'label' => 'Ketua Tim', 'nameKey' => 'leaderName', 'phoneKey' => 'leaderPhoneNumber'],
    ];
    if ($hasM2) $reviewMembers[] = ['num' => 2, 'label' => 'Anggota 2', 'nameKey' => 'firstMemberName', 'phoneKey' => 'firstMemberPhoneNumber'];
    if ($hasM3) $reviewMembers[] = ['num' => 3, 'label' => 'Anggota 3', 'nameKey' => 'secondMemberName', 'phoneKey' => 'secondMemberPhoneNumber'];
    ?>
    <div class="space-y-4">
      <?php foreach ($reviewMembers as $m):
        $p = $m['num'];
        $existingCard = $uploads[$p]['student_card'] ?? null;
        $originalCard = $uploads[$p]['original_student_card'] ?? null;
        $cardIcon = Icon::make()->name('credit-card')->class('size-5 text-black');
      ?>
        <div>
          <p class="text-xs font-semibold text-gray-800 mb-2"><?= htmlspecialchars($m['label']) ?></p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-xs text-gray-500 mb-1 block">Nama Lengkap<span class="text-red-500">*</span></label>
              <input type="text" name="<?= $m['nameKey'] ?>" value="<?= htmlspecialchars($team[$m['nameKey']] ?? '') ?>" placeholder="Nama Lengkap" required class="review-field w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
            </div>
            <div>
              <label class="text-xs text-gray-500 mb-1 block">No. Telepon / WA<span class="text-red-500">*</span></label>
              <input type="text" name="<?= $m['phoneKey'] ?>" value="<?= htmlspecialchars($team[$m['phoneKey']] ?? '') ?>" placeholder="No. Telepon" required pattern="08[0-9]{6,}" class="review-field w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
            </div>
          </div>
          <div class="mt-2">
            <label class="text-xs text-gray-500 mb-1 block">Kartu Pelajar</label>
            <?php
            $cardAttrs = ['accept' => 'image/*,.pdf,application/pdf', 'max-size' => 10 * 1024 * 1024];
            if ($existingCard):
            ?>
              <?= Attachment::make()

                ->mediaVariant('image')
                ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($existingCard) . '" class="w-full h-full object-cover">')
                ->title($originalCard ?: basename($existingCard))
                ->description('Scan atau foto kartu pelajar')
                ->withPreview()
                ->fileUrl($UPLOAD_URL . htmlspecialchars($existingCard))
                ->originalMedia($cardIcon)
                ->originalSrc($UPLOAD_URL . htmlspecialchars($existingCard))
                ->idleTitle('Upload Kartu Pelajar')
                ->fileInput('studentCard_' . $p, $cardAttrs)
                ->render() ?>
            <?php else: ?>
              <?= Attachment::make()

                ->media($cardIcon)
                ->title('Upload Kartu Pelajar')
                ->description('Scan atau foto kartu pelajar')
                ->withPreview()
                ->originalMedia($cardIcon)
                ->fileInput('studentCard_' . $p, $cardAttrs)
                ->render() ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="mt-4 flex justify-end">
      <button type="submit" class="review-btn text-xs font-medium text-gray-400 border border-gray-200 px-3 py-1.5 rounded-lg transition-colors">Simpan</button>
    </div>
  </form>

  <form action="/application/team/update" method="POST" enctype="multipart/form-data" class="review-form bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
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
        $originalIg = $uploads[$p]['original_ig_follow'] ?? null;
        $originalTwibbon = $uploads[$p]['original_twibbon'] ?? null;
        $igIcon = Icon::make()->name('image')->class('size-5 text-black');
        $twibbonIcon = Icon::make()->name('image')->class('size-5 text-black');
      ?>
        <div>
          <p class="text-xs font-semibold text-gray-800 mb-2"><?= htmlspecialchars($m['name']) ?></p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-xs text-gray-500 mb-1 block">Bukti Follow IG</label>
              <?php
              $igAttrs = ['accept' => 'image/*,.pdf,application/pdf', 'max-size' => 10 * 1024 * 1024];
              if ($ig):
              ?>
                <?= Attachment::make()

                  ->mediaVariant('image')
                  ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($ig) . '" class="w-full h-full object-cover">')
                  ->title($originalIg ?: basename($ig))
                  ->description('Bukti follow Instagram @automationweek')
                  ->withPreview()
                  ->fileUrl($UPLOAD_URL . htmlspecialchars($ig))
                  ->originalMedia($igIcon)
                  ->originalSrc($UPLOAD_URL . htmlspecialchars($ig))
                  ->idleTitle('Screenshot Follow')
                  ->fileInput('igFollow_' . $p, $igAttrs)
                  ->render() ?>
              <?php else: ?>
                <?= Attachment::make()

                  ->media($igIcon)
                  ->title('Screenshot Follow')
                  ->description('Bukti follow Instagram @automationweek')
                  ->withPreview()
                  ->originalMedia($igIcon)
                  ->fileInput('igFollow_' . $p, $igAttrs)
                  ->render() ?>
              <?php endif; ?>
            </div>
            <div>
              <label class="text-xs text-gray-500 mb-1 block">Twibbon</label>
              <?php
              $twibbonAttrs = ['accept' => 'image/*,.pdf,application/pdf', 'max-size' => 10 * 1024 * 1024];
              if ($twibbon):
              ?>
                <?= Attachment::make()

                  ->mediaVariant('image')
                  ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($twibbon) . '" class="w-full h-full object-cover">')
                  ->title($originalTwibbon ?: basename($twibbon))
                  ->description('Foto profil dengan twibbon')
                  ->withPreview()
                  ->fileUrl($UPLOAD_URL . htmlspecialchars($twibbon))
                  ->originalMedia($twibbonIcon)
                  ->originalSrc($UPLOAD_URL . htmlspecialchars($twibbon))
                  ->idleTitle('Upload Twibbon')
                  ->fileInput('twibbon_' . $p, $twibbonAttrs)
                  ->render() ?>
              <?php else: ?>
                <?= Attachment::make()

                  ->media($twibbonIcon)
                  ->title('Upload Twibbon')
                  ->description('Foto profil dengan twibbon')
                  ->withPreview()
                  ->originalMedia($twibbonIcon)
                  ->fileInput('twibbon_' . $p, $twibbonAttrs)
                  ->render() ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="mt-4 flex justify-end">
      <button type="submit" class="review-btn text-xs font-medium text-gray-400 border border-gray-200 px-3 py-1.5 rounded-lg transition-colors">Simpan</button>
    </div>
  </form>
</div>

<script>
  document.getElementById('reviewDivision')?.addEventListener('change', function(e) {
    if (e.target.name !== 'division') return;
    this.querySelectorAll('.review-division').forEach(function(l) {
      var rb = l.querySelector('input[name="division"]');
      var changed = rb && rb.checked && l.dataset.orig !== '1';
      l.classList.toggle('ring-2', changed);
      l.classList.toggle('ring-brand', changed);
      l.classList.toggle('ring-offset-1', changed);
    });
  });
</script>