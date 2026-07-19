<?php
use App\Components\Attachment;
use App\Components\Icon;

/** @var array|null $team */
/** @var string $csrf_token */
/** @var array $uploads */
$members = [];
if ($team) {
  $members[] = ['num' => 1, 'name' => $team['leaderName'] ?? 'Anggota 1'];
  $two = $team['division'] === 'LF' || $team['division'] === 'PLC';
  $three = $team['division'] === 'FFR' || $team['division'] === 'LKTI';
  if ($two || $three) $members[] = ['num' => 2, 'name' => $team['firstMemberName'] ?? 'Anggota 2'];
  if ($three) $members[] = ['num' => 3, 'name' => $team['secondMemberName'] ?? 'Anggota 3'];
}
$UPLOAD_URL = '/uploads/teams/';
?>
<?php if (!$team): ?>
  <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-200 rounded-xl">
    <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-gray-400 shrink-0') ?>
    <p class="text-sm text-gray-500">Daftarkan tim terlebih dahulu.</p>
  </div>
<?php else: ?>
  <form action="/dashboard/team/update" method="POST" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

    <div class="space-y-8">
      <?php foreach ($members as $i => $m):
        $p = $m['num'];
        $isOptional = $p > 1;
        $existingIg = $uploads[$p]['ig_follow'] ?? null;
        $existingTwibbon = $uploads[$p]['twibbon'] ?? null;
      ?>
        <div class="member-group" data-member="<?= $p ?>" <?= $isOptional ? 'data-optional="true"' : '' ?>>
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center text-xs font-bold text-brand"><?= $p ?></div>
            <div>
              <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($m['name']) ?></p>
              <?php if ($isOptional): ?><p class="text-xs text-gray-400">Opsional</p><?php endif; ?>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Bukti Follow Instagram <span class="text-red-500">*</span></label>
              <?php
              $igAttrs = ['accept' => 'image/*', 'data-error' => 'err-sosmed-' . $p . '-ig', 'class' => 'member-input'];
              $igAttrs['required'] = !$isOptional && !$existingIg;
              if ($existingIg):
              ?>
                <?= Attachment::make()
                    ->state('done')
                    ->mediaVariant('image')
                    ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($existingIg) . '" class="w-full h-full object-cover">')
                    ->title(basename($existingIg))
                    ->description('Sudah diupload')
                    ->withPreview()
                    ->fileInput('igFollow_' . $p, $igAttrs)
                    ->render() ?>
              <?php else: ?>
                <?= Attachment::make()
                    ->state('idle')
                    ->media(Icon::make()->name('image')->class('size-5 text-gray-400'))
                    ->title('Screenshot Follow')
                    ->description('Bukti follow Instagram @lombax')
                    ->withPreview()
                    ->fileInput('igFollow_' . $p, $igAttrs)
                    ->render() ?>
              <?php endif; ?>
              <p id="err-sosmed-<?= $p ?>-ig" class="text-xs text-red-500 mt-1 hidden">Bukti follow wajib diupload</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload Twibbon <span class="text-red-500">*</span></label>
              <?php
              $twibbonAttrs = ['accept' => 'image/*', 'data-error' => 'err-sosmed-' . $p . '-twibbon', 'class' => 'member-input'];
              $twibbonAttrs['required'] = !$isOptional && !$existingTwibbon;
              if ($existingTwibbon):
              ?>
                <?= Attachment::make()
                    ->state('done')
                    ->mediaVariant('image')
                    ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($existingTwibbon) . '" class="w-full h-full object-cover">')
                    ->title(basename($existingTwibbon))
                    ->description('Sudah diupload')
                    ->withPreview()
                    ->fileInput('twibbon_' . $p, $twibbonAttrs)
                    ->render() ?>
              <?php else: ?>
                <?= Attachment::make()
                    ->state('idle')
                    ->media(Icon::make()->name('image')->class('size-5 text-gray-400'))
                    ->title('Upload Twibbon')
                    ->description('Foto profil dengan twibbon')
                    ->withPreview()
                    ->fileInput('twibbon_' . $p, $twibbonAttrs)
                    ->render() ?>
              <?php endif; ?>
              <p id="err-sosmed-<?= $p ?>-twibbon" class="text-xs text-red-500 mt-1 hidden">Twibbon wajib diupload</p>
            </div>
          </div>
        </div>
        <?php if ($i < count($members) - 1): ?><hr class="border-gray-100"><?php endif; ?>
      <?php endforeach; ?>
    </div>
  </form>

  <script>
  document.querySelectorAll('[data-optional]').forEach(group => {
    const inputs = group.querySelectorAll('.member-input');
    function check() {
      const hasValue = Array.from(inputs).some(i => i.files.length > 0);
      inputs.forEach(i => {
        if (hasValue) i.setAttribute('required', '');
        else i.removeAttribute('required');
      });
    }
    inputs.forEach(i => i.addEventListener('change', check));
    check();
  });
  </script>
<?php endif; ?>
