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
  $two = $team['division'] === 'LF' || $team['division'] === 'PLC';
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
  <form action="/dashboard/team/update" method="POST" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <input type="hidden" name="division" value="<?= htmlspecialchars($team['division']) ?>">

    <div class="space-y-8">
      <?php foreach ($members as $i => $m):
        $name = $team[$m['nameKey']] ?? '';
        $phone = $team[$m['phoneKey']] ?? '';
        $isOptional = $m['num'] > 1;
        $p = $m['num'];
        $existingCard = $uploads[$p]['student_card'] ?? null;
        $cardRequired = !$isOptional && !$existingCard;
        $cardAttrs = ['accept' => 'image/*', 'data-error' => 'err-anggota-' . $p . '-card', 'class' => 'member-input'];
        $cardAttrs['required'] = $cardRequired;
      ?>
        <div class="member-group" data-member="<?= $p ?>" <?= $isOptional ? 'data-optional="true"' : '' ?>>
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center text-xs font-bold text-brand"><?= $p ?></div>
            <div>
              <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($m['label']) ?></p>
              <?php if ($m['role']): ?><p class="text-xs text-gray-400"><?= htmlspecialchars($m['role']) ?></p><?php endif; ?>
              <?php if ($isOptional): ?><p class="text-xs text-gray-400">Opsional — isi jika ada anggota</p><?php endif; ?>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <?= $isOptional ? '' : '<span class="text-red-500">*</span>' ?></label>
              <input type="text" name="<?= $m['nameKey'] ?>" value="<?= htmlspecialchars($name) ?>" <?= $isOptional ? '' : 'required' ?>
                data-error="err-anggota-<?= $p ?>-name"
                class="member-input w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
                oninput="this.classList.remove('border-red-500'); document.getElementById(this.dataset.error)?.classList.add('hidden')">
              <p id="err-anggota-<?= $p ?>-name" class="text-xs text-red-500 mt-1 hidden">Nama lengkap wajib diisi</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon / WA <?= $isOptional ? '' : '<span class="text-red-500">*</span>' ?></label>
              <input type="text" name="<?= $m['phoneKey'] ?>" value="<?= htmlspecialchars($phone) ?>" <?= $isOptional ? '' : 'required' ?>
                data-error="err-anggota-<?= $p ?>-phone"
                class="member-input w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
                oninput="this.classList.remove('border-red-500'); document.getElementById(this.dataset.error)?.classList.add('hidden')">
              <p id="err-anggota-<?= $p ?>-phone" class="text-xs text-red-500 mt-1 hidden">No. telepon wajib diisi</p>
            </div>
          </div>
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Kartu Pelajar / Mahasiswa <?= $isOptional ? '' : '<span class="text-red-500">*</span>' ?></label>
            <?php if ($existingCard): ?>
              <?= Attachment::make()
                  ->state('done')
                  ->mediaVariant('image')
                  ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($existingCard) . '" class="w-full h-full object-cover">')
                  ->title(basename($existingCard))
                  ->description('Sudah diupload')
                  ->withPreview()
                  ->fileInput('studentCard_' . $p, $cardAttrs)
                  ->render() ?>
            <?php else: ?>
              <?= Attachment::make()
                  ->state('idle')
                  ->media(Icon::make()->name('credit-card')->class('size-5 text-gray-400'))
                  ->title('Upload Kartu Pelajar')
                  ->description('Scan atau foto kartu pelajar/mahasiswa')
                  ->withPreview()
                  ->fileInput('studentCard_' . $p, $cardAttrs)
                  ->render() ?>
            <?php endif; ?>
            <p id="err-anggota-<?= $p ?>-card" class="text-xs text-red-500 mt-1 hidden">Kartu pelajar wajib diupload</p>
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
      const hasValue = Array.from(inputs).some(i => i.type === 'file' ? i.files.length > 0 : i.value.trim() !== '');
      inputs.forEach(i => {
        if (hasValue) i.setAttribute('required', '');
        else i.removeAttribute('required');
      });
    }
    inputs.forEach(i => i.addEventListener('change', check));
    inputs.forEach(i => i.addEventListener('input', check));
    check();
  });
  </script>
<?php endif; ?>
