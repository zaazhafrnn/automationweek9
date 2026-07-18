<?php
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
    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
              <?php if ($existingIg): ?>
                <div class="mb-2">
                  <img src="<?= $UPLOAD_URL . htmlspecialchars($existingIg) ?>" class="max-h-24 rounded-lg border border-gray-200 object-contain bg-gray-50">
                  <p class="text-xs text-green-600 mt-1">Sudah diupload</p>
                </div>
              <?php endif; ?>
              <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-brand/50 hover:bg-brand/5 transition-colors p-4 group cursor-pointer">
                <input type="file" accept="image/*" name="igFollow_<?= $p ?>" <?= $isOptional ? '' : (!$existingIg ? 'required' : '') ?>
                  data-error="err-sosmed-<?= $p ?>-ig"
                  class="member-input absolute inset-0 opacity-0 cursor-pointer"
                  onchange="this.classList.remove('border-red-500'); document.getElementById(this.dataset.error)?.classList.add('hidden')">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand/10 transition-colors">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-700 group-hover:text-brand transition-colors"><?= $existingIg ? 'Ganti Screenshot' : 'Screenshot Follow' ?></p>
                    <p class="text-xs text-gray-400">Bukti sudah follow Instagram @lombax</p>
                  </div>
                </div>
              </div>
              <p id="err-sosmed-<?= $p ?>-ig" class="text-xs text-red-500 mt-1 hidden">Bukti follow wajib diupload</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload Twibbon <span class="text-red-500">*</span></label>
              <?php if ($existingTwibbon): ?>
                <div class="mb-2">
                  <img src="<?= $UPLOAD_URL . htmlspecialchars($existingTwibbon) ?>" class="max-h-24 rounded-lg border border-gray-200 object-contain bg-gray-50">
                  <p class="text-xs text-green-600 mt-1">Sudah diupload</p>
                </div>
              <?php endif; ?>
              <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-brand/50 hover:bg-brand/5 transition-colors p-4 group cursor-pointer">
                <input type="file" accept="image/*" name="twibbon_<?= $p ?>" <?= $isOptional ? '' : (!$existingTwibbon ? 'required' : '') ?>
                  data-error="err-sosmed-<?= $p ?>-twibbon"
                  class="member-input absolute inset-0 opacity-0 cursor-pointer"
                  onchange="this.classList.remove('border-red-500'); document.getElementById(this.dataset.error)?.classList.add('hidden')">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand/10 transition-colors">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-700 group-hover:text-brand transition-colors"><?= $existingTwibbon ? 'Ganti Twibbon' : 'Upload Twibbon' ?></p>
                    <p class="text-xs text-gray-400">Foto profil dengan twibbon</p>
                  </div>
                </div>
              </div>
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
