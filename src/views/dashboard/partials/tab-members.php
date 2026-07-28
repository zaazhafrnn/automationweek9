<?php

use App\Components\Attachment;
use App\Components\Icon;

/** @var array|null $team */
/** @var string $csrf_token */
/** @var array $uploads */

$members = [
  ['num' => 1, 'label' => 'Anggota 1', 'role' => 'Ketua Tim', 'nameKey' => 'leaderName', 'phoneKey' => 'leaderPhoneNumber'],
];
if ($team) {
  $two = $team['division'] === 'LF' || $team['division'] === 'PLC' || $team['division'] === 'PROG';
  $three = $team['division'] === 'FFR' || $team['division'] === 'LKTI';
  if ($two || $three) $members[] = ['num' => 2, 'label' => 'Anggota 2', 'role' => '', 'nameKey' => 'firstMemberName', 'phoneKey' => 'firstMemberPhoneNumber'];
  if ($three) $members[] = ['num' => 3, 'label' => 'Anggota 3', 'role' => '', 'nameKey' => 'secondMemberName', 'phoneKey' => 'secondMemberPhoneNumber'];
}
$UPLOAD_URL = '/uploads/teams/';
?>
<?php if (!$team): ?>
  <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-200 rounded-xl">
    <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-gray-400 shrink-0') ?>
    <p class="text-sm text-gray-500">Daftarkan tim terlebih dahulu di tab sebelumnya.</p>
  </div>
<?php else: ?>
  <form action="/application/team/update" method="POST" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <input type="hidden" name="division" value="<?= htmlspecialchars($team['division']) ?>">
    <input type="hidden" name="next_tab" value="social-media">
    <input type="hidden" name="current_tab" value="members">

    <div class="space-y-8">
      <?php foreach ($members as $i => $m):
        $name = $team[$m['nameKey']] ?? '';
        $phone = $team[$m['phoneKey']] ?? '';
        $isOptional = $m['num'] > 1;
        $p = $m['num'];
        $existingCard = $uploads[$p]['student_card'] ?? null;
        $originalCard = $uploads[$p]['original_student_card'] ?? null;
        $hasData = $name !== '' || $phone !== '' || $existingCard;
        $disabled = $isOptional && !$hasData;
        $cardRequired = $hasData && !$existingCard;
        $cardAttrs = ['accept' => 'image/*', 'data-error' => 'err-anggota-' . $p . '-card', 'class' => 'member-file'];
        if ($cardRequired) $cardAttrs['required'] = true;
        $cardIcon = Icon::make()->name('credit-card')->class('size-5 text-black');
      ?>
        <div class="member-group relative" data-member="<?= $p ?>" data-optional="true" <?= $disabled ? '' : 'data-activated="true"' ?>>
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center text-xs font-bold text-brand"><?= $p ?></div>
            <div>
              <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($m['label']) ?></p>
              <?php if ($m['role']): ?><p class="text-xs text-gray-400"><?= htmlspecialchars($m['role']) ?></p><?php endif; ?>
            </div>
          </div>
          <div class="member-fields <?= $disabled ? 'opacity-30 pointer-events-none' : '' ?>">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="<?= $m['nameKey'] ?>" value="<?= htmlspecialchars($name) ?>" placeholder="Masukkan nama lengkap"
                  data-error="err-anggota-<?= $p ?>-name"
                  class="member-input w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
                  oninput="this.classList.remove('border-red-500'); document.getElementById(this.dataset.error)?.classList.add('hidden')">
                <p id="err-anggota-<?= $p ?>-name" class="text-xs text-red-500 mt-1 hidden">Nama lengkap wajib diisi</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon / WA</label>
                <input type="text" name="<?= $m['phoneKey'] ?>" value="<?= htmlspecialchars($phone) ?>" placeholder="Masukkan nomor telepon" inputmode="numeric" pattern="[0-9]*"
                  data-error="err-anggota-<?= $p ?>-phone"
                  class="member-input w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
                  oninput="this.value=this.value.replace(/[^0-9]/g,''); this.classList.remove('border-red-500'); document.getElementById(this.dataset.error)?.classList.add('hidden')">
                <p id="err-anggota-<?= $p ?>-phone" class="text-xs text-red-500 mt-1 hidden">No. telepon wajib diisi</p>
              </div>
            </div>
            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Kartu Pelajar</label>
              <?php if ($existingCard): ?>
                <?= Attachment::make()
                  ->mediaVariant('image')
                  ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($existingCard) . '" class="w-full h-full object-cover">')
                  ->title($originalCard ?: basename($existingCard))
                  ->description('Scan atau foto kartu pelajar')
                  ->clearable()
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
                  ->clearable()
                  ->withPreview()
                  ->originalMedia($cardIcon)
                  ->fileInput('studentCard_' . $p, $cardAttrs)
                  ->render() ?>
              <?php endif; ?>
              <p id="err-anggota-<?= $p ?>-card" class="text-xs text-red-500 mt-1 hidden">Kartu pelajar wajib diupload</p>
            </div>
            <?php if ($isOptional && !$hasData): ?>
              <button type="button" class="cancel-member text-xs text-red-400 hover:text-red-600 mt-3 hidden">Batalkan</button>
            <?php endif; ?>
          </div>
          <?php if ($disabled): ?>
            <div class="member-overlay absolute inset-0 z-10 flex items-center justify-center rounded-xl cursor-pointer"
              onclick="var g=this.closest('[data-optional]');g.dataset.activated='true';this.classList.add('hidden');g.querySelector('.member-fields').classList.remove('opacity-30','pointer-events-none');g.querySelectorAll('.member-input').forEach(function(i){if(!i.value.trim())i.setAttribute('required','');});(g.querySelector('.cancel-member')||{}).classList?.remove('hidden')">
              <button type="button" class="inline-flex items-center gap-2 px-6 py-3.5 text-sm font-semibold text-brand bg-brand/5 border-2 border-dashed border-brand/30 rounded-xl hover:bg-brand/10 hover:border-brand/50 transition-all">
                <?= Icon::make()->name('users')->class('w-5 h-5') ?>
                + Tambah Member
              </button>
            </div>
          <?php endif; ?>
        </div>
        <?php if ($i < count($members) - 1): ?>
          <hr class="border-gray-100"><?php endif; ?>
      <?php endforeach; ?>
    </div>
  </form>

  <script>
    document.querySelectorAll('[data-optional]').forEach(group => {
      const inputs = group.querySelectorAll('.member-input');

      function check() {
        const activated = group.dataset.activated === 'true';
        const hasValue = Array.from(inputs).some(i => i.value.trim() !== '');
        inputs.forEach(i => {
          if (hasValue || activated) i.setAttribute('required', '');
          else i.removeAttribute('required');
        });
      }
      inputs.forEach(i => i.addEventListener('change', check));
      inputs.forEach(i => i.addEventListener('input', check));
      check();
    });
    document.querySelectorAll('.cancel-member').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var g = this.closest('[data-optional]');
        g.dataset.activated = 'false';
        g.querySelectorAll('.member-input').forEach(function(i) {
          i.value = '';
          i.removeAttribute('required');
          i.classList.remove('border-red-500');
        });
        g.querySelectorAll('[data-slot="attachment"]').forEach(function(a) {
          a.dataset.state = 'idle';
          a.querySelector('input[type="file"]').value = '';
        });
        g.querySelector('.member-fields').classList.add('opacity-30', 'pointer-events-none');
        g.querySelector('.member-overlay').classList.remove('hidden');
        this.classList.add('hidden');
      });
    });
  </script>
<?php endif; ?>