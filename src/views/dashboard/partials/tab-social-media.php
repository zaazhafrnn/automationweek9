<?php

use App\Components\Attachment;
use App\Components\Icon;

/** @var array|null $team */
/** @var string $csrf_token */
/** @var array $uploads */

$members = [];
if ($team) {
  $members[] = ['num' => 1, 'name' => $team['leaderName'] ?? 'Anggota 1', 'active' => true];
  $two = $team['division'] === 'LF' || $team['division'] === 'PLC' || $team['division'] === 'PROG';
  $three = $team['division'] === 'FFR' || $team['division'] === 'LKTI';
  if ($two || $three) $members[] = ['num' => 2, 'name' => $team['firstMemberName'] ?? 'Anggota 2', 'active' => !empty($team['firstMemberName'])];
  if ($three) $members[] = ['num' => 3, 'name' => $team['secondMemberName'] ?? 'Anggota 3', 'active' => !empty($team['secondMemberName'])];
}
$UPLOAD_URL = '/uploads/teams/';
?>
<?php if (!$team): ?>
  <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-200 rounded-xl">
    <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-gray-400 shrink-0') ?>
    <p class="text-sm text-gray-500">Daftarkan tim terlebih dahulu.</p>
  </div>
<?php else: ?>
  <form action="/application/team/update" method="POST" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <input type="hidden" name="next_tab" value="review">
    <input type="hidden" name="current_tab" value="social-media">

    <div class="space-y-8">
      <?php foreach ($members as $i => $m):
        $p = $m['num'];
        $isOptional = $p > 1;
        $disabled = !$m['active'];
        $existingIg = $uploads[$p]['ig_follow'] ?? null;
        $existingTwibbon = $uploads[$p]['twibbon'] ?? null;
        $originalIg = $uploads[$p]['original_ig_follow'] ?? null;
        $originalTwibbon = $uploads[$p]['original_twibbon'] ?? null;
      ?>
        <div class="member-group relative" data-member="<?= $p ?>">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center text-xs font-bold text-brand"><?= $p ?></div>
            <div>
              <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($m['name']) ?></p>
              <?php if ($isOptional && !$disabled): ?><p class="text-xs text-gray-400">Anggota aktif</p><?php endif; ?>
            </div>
          </div>
          <div class="member-fields <?= $disabled ? 'opacity-30 pointer-events-none' : '' ?>">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bukti Follow Instagram</label>
                <?php
                $igRequired = !$disabled && !$existingIg;
                $igAttrs = ['accept' => 'image/*', 'data-error' => 'err-sosmed-' . $p . '-ig'];
                if ($igRequired) $igAttrs['required'] = true;
                if ($existingIg):
                ?>
                  <?= Attachment::make()
                    ->state('done')
                    ->mediaVariant('image')
                    ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($existingIg) . '" class="w-full h-full object-cover">')
                    ->title($originalIg ?: basename($existingIg))
                    ->description('Sudah diupload')
                    ->clearable()
                    ->withPreview()
                    ->fileInput('igFollow_' . $p, $igAttrs)
                    ->render() ?>
                <?php else: ?>
                  <?= Attachment::make()
                    ->state('idle')
                    ->media(Icon::make()->name('image')->class('size-5 text-gray-400'))
                    ->title('Screenshot Follow')
                    ->description('Bukti follow Instagram @lombax')
                    ->clearable()
                    ->withPreview()
                    ->fileInput('igFollow_' . $p, $igAttrs)
                    ->render() ?>
                <?php endif; ?>
                <p id="err-sosmed-<?= $p ?>-ig" class="text-xs text-red-500 mt-1 hidden">Bukti follow wajib diupload</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload Twibbon</label>
                <?php
                $twibbonRequired = !$disabled && !$existingTwibbon;
                $twibbonAttrs = ['accept' => 'image/*', 'data-error' => 'err-sosmed-' . $p . '-twibbon'];
                if ($twibbonRequired) $twibbonAttrs['required'] = true;
                if ($existingTwibbon):
                ?>
                  <?= Attachment::make()
                    ->state('done')
                    ->mediaVariant('image')
                    ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($existingTwibbon) . '" class="w-full h-full object-cover">')
                    ->title($originalTwibbon ?: basename($existingTwibbon))
                    ->description('Sudah diupload')
                    ->clearable()
                    ->withPreview()
                    ->fileInput('twibbon_' . $p, $twibbonAttrs)
                    ->render() ?>
                <?php else: ?>
                  <?= Attachment::make()
                    ->state('idle')
                    ->media(Icon::make()->name('image')->class('size-5 text-gray-400'))
                    ->title('Upload Twibbon')
                    ->description('Foto profil dengan twibbon')
                    ->clearable()
                    ->withPreview()
                    ->fileInput('twibbon_' . $p, $twibbonAttrs)
                    ->render() ?>
                <?php endif; ?>
                <p id="err-sosmed-<?= $p ?>-twibbon" class="text-xs text-red-500 mt-1 hidden">Twibbon wajib diupload</p>
              </div>
            </div>
          </div>
          <?php if ($disabled): ?>
            <div class="member-overlay absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/80 cursor-default">
              <div class="text-center">
                <?= Icon::make()->name('lock')->class('w-5 h-5 text-gray-300 mx-auto mb-1') ?>
                <p class="text-xs text-gray-400">Data anggota belum diisi</p>
                <p class="text-xs text-gray-400">Isi di tab Data Anggota terlebih dahulu</p>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <?php if ($i < count($members) - 1): ?>
          <hr class="border-gray-100"><?php endif; ?>
      <?php endforeach; ?>
    </div>
  </form>
<?php endif; ?>