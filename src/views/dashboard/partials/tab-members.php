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

    <div class="space-y-6">
      <?php foreach ($members as $i => $m):
        $name = $team[$m['nameKey']] ?? '';
        $phone = $team[$m['phoneKey']] ?? '';
        $genderKey = $m['nameKey'] === 'leaderName' ? 'leaderGender' : ($m['nameKey'] === 'firstMemberName' ? 'firstMemberGender' : 'secondMemberGender');
        $gender = $team[$genderKey] ?? '';
        $isOptional = $m['num'] > 1;
        $p = $m['num'];
        $existingCard = $uploads[$p]['student_card'] ?? null;
        $originalCard = $uploads[$p]['original_student_card'] ?? null;
        $hasData = $name !== '' || $phone !== '' || $existingCard;
        $disabled = $isOptional && !$hasData;
        $cardRequired = $hasData && !$existingCard;
        $cardAttrs = ['accept' => 'image/*,.pdf,application/pdf', 'data-error' => 'err-anggota-' . $p . '-card', 'class' => 'member-file', 'max-size' => 10 * 1024 * 1024];
        if ($cardRequired) $cardAttrs['required'] = true;
        $cardIcon = Icon::make()->name('credit-card')->class('size-5 text-black');
      ?>
        <div class="member-group relative bg-white rounded-xl border border-gray-200 p-4 sm:p-5" data-member="<?= $p ?>" data-optional="true" <?= $disabled ? '' : 'data-activated="true"' ?>>
          <div class="flex items-center gap-3 mb-4 justify-between">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center text-xs font-bold text-brand"><?= $p ?></div>
              <div>
                <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($m['label']) ?></p>
                <?php if ($m['role']): ?><p class="text-xs text-gray-400"><?= htmlspecialchars($m['role']) ?></p><?php elseif ($isOptional): ?><p class="text-xs text-gray-400">(opsional)</p><?php endif; ?>
              </div>
            </div>
            <?php if ($isOptional): ?>
              <button type="button" class="cancel-member <?= $hasData ? '' : 'hidden' ?> hover:bg-red-100 p-3 border border-grey-100 hover:border-red-500 rounded-xl text-gray-400 hover:text-red-500 transition-colors" aria-label="Hapus anggota">
                <?= Icon::make()->name('trash-2')->class('w-5 h-5 text-red-500') ?>
              </button>
            <?php endif; ?>
          </div>
          <div class="member-fields <?= $disabled ? 'opacity-30 pointer-events-none' : '' ?>">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium  mb-1.5">Nama Lengkap<span class="text-red-500">*</span></label>
                <input type="text" name="<?= $m['nameKey'] ?>" value="<?= htmlspecialchars($name) ?>" placeholder="Masukkan nama lengkap"
                  data-error="err-anggota-<?= $p ?>-name"
                  class="member-input w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
                  oninput="this.classList.remove('border-red-500'); document.getElementById(this.dataset.error)?.classList.add('hidden')">
                <p id="err-anggota-<?= $p ?>-name" class="text-xs text-red-500 mt-1 hidden">Nama lengkap wajib diisi</p>
              </div>
              <div>
                <label class="block text-sm font-medium  mb-1.5">No. Telepon / WA<span class="text-red-500">*</span></label>
                <input type="text" name="<?= $m['phoneKey'] ?>" value="<?= htmlspecialchars($phone) ?>" placeholder="Masukkan nomor telepon" inputmode="numeric" pattern="08[0-9]{6,}"
                  data-error="err-anggota-<?= $p ?>-phone"
                  data-format-error="err-anggota-<?= $p ?>-phone-format"
                  class="member-input w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
                  oninput="this.value=this.value.replace(/[^0-9]/g,''); this.classList.remove('border-red-500'); document.getElementById(this.dataset.error)?.classList.add('hidden'); document.getElementById(this.dataset.formatError)?.classList.add('hidden')">
                <p id="err-anggota-<?= $p ?>-phone" class="text-xs text-red-500 mt-1 hidden">No. telepon wajib diisi</p>
                <p id="err-anggota-<?= $p ?>-phone-format" class="text-xs text-red-500 mt-1 hidden">Nomer harus berawalan 08xx & minimal 8 digit</p>
              </div>
              <div>
                <label class="block text-sm font-medium  mb-1.5">Jenis Kelamin</label>
                <div class="flex gap-3">
                  <label class="flex items-center gap-2 px-4 py-1 cursor-pointer transition-all">
                    <input type="radio" name="<?= $genderKey ?>" value="Laki-laki" class="member-radio accent-brand" <?= !$isOptional ? 'required' : '' ?> data-error="err-<?= $genderKey ?>" <?= $gender === 'Laki-laki' ? 'checked' : '' ?>>
                    <span class="text-sm ">Laki-laki</span>
                  </label>
                  <label class="flex items-center gap-2 px-4 py-1 cursor-pointer transition-all">
                    <input type="radio" name="<?= $genderKey ?>" value="Perempuan" class="member-radio accent-brand" <?= !$isOptional ? 'required' : '' ?> data-error="err-<?= $genderKey ?>" <?= $gender === 'Perempuan' ? 'checked' : '' ?>>
                    <span class="text-sm ">Perempuan</span>
                  </label>
                </div>
                <p id="err-<?= $genderKey ?>" class="text-xs text-red-500 mt-1 hidden">Jenis kelamin wajib dipilih</p>
              </div>
              <div>
                <label class="block text-sm font-medium  mb-1.5">Kartu Pelajar<span class="text-red-500">*</span></label>
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
                  <script>
                    document.addEventListener('change', function(e) {
                      if (e.target.matches('.member-radio')) {
                        var errEl = document.getElementById(e.target.dataset.error);
                        if (errEl) errEl.classList.add('hidden');
                      }
                    });
                  </script>
                <?php endif; ?>

                <p id="err-anggota-<?= $p ?>-card" class="text-xs text-red-500 mt-1 hidden">Kartu pelajar wajib diupload</p>
              </div>
            </div>
          </div>
          <div class="member-overlay absolute inset-0 z-10 flex items-center justify-center rounded-xl cursor-pointer <?= $disabled ? '' : 'hidden' ?>"
            onclick="var g=this.closest('[data-optional]');g.dataset.activated='true';this.classList.add('hidden');g.querySelector('.member-fields').classList.remove('opacity-30','pointer-events-none');g.querySelectorAll('.member-input').forEach(function(i){if(!i.value.trim())i.setAttribute('required','');});g.querySelectorAll('.member-file').forEach(function(f){if(!f.files.length)f.setAttribute('required','');});g.querySelectorAll('.member-radio').forEach(function(r){r.setAttribute('required','');});(g.querySelector('.cancel-member')||{}).classList?.remove('hidden')">
            <button type="button" class="flex flex-col items-center gap-3 px-10 py-8 text-sm font-semibold text-brand bg-brand/5 border-2 border-dashed border-brand/30 rounded-xl hover:bg-brand/10 hover:border-brand/50 transition-all">
              <?= Icon::make()->name('user-round-plus')->class('w-8 h-8') ?>
              Tambah Member
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </form>

  <script>
    document.querySelectorAll('[data-optional]').forEach(group => {
      const inputs = group.querySelectorAll('.member-input');

      function check() {
        const activated = group.dataset.activated === 'true';
        const hasValue = Array.from(inputs).some(i => i.value.trim() !== '');
        const required = hasValue || activated;
        inputs.forEach(i => {
          if (required) i.setAttribute('required', '');
          else i.removeAttribute('required');
        });
        group.querySelectorAll('.member-radio').forEach(r => {
          if (required) r.setAttribute('required', '');
          else {
            r.removeAttribute('required');
            document.getElementById(r.dataset.error)?.classList.add('hidden');
          }
        });
      }
      inputs.forEach(i => i.addEventListener('change', check));
      inputs.forEach(i => i.addEventListener('input', check));
      group.querySelectorAll('.member-radio').forEach(r => r.addEventListener('change', check));
      check();
    });
    document.querySelectorAll('.cancel-member').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var g = this.closest('[data-optional]');
        var num = parseInt(g.dataset.member);
        var next = g.parentElement.querySelector('.member-group[data-member="' + (num + 1) + '"]');
        var hasData = next && (next.dataset.activated === 'true' || Array.from(next.querySelectorAll('.member-input')).some(function(i) {
          return i.value.trim();
        }));
        if (hasData) {
          shiftDown(num, num + 1);
        } else {
          clearMember(g);
        }
      });
    });

    function clearMember(g, keepData) {
      g.dataset.activated = 'false';
      g.querySelectorAll('.member-input').forEach(function(i) {
        i.value = '';
        i.removeAttribute('required');
        i.classList.remove('border-red-500');
        document.getElementById(i.dataset.error)?.classList.add('hidden');
        document.getElementById(i.dataset.formatError)?.classList.add('hidden');
      });
      g.querySelectorAll('.member-radio').forEach(function(r) {
        r.checked = false;
        r.removeAttribute('required');
        document.getElementById(r.dataset.error)?.classList.add('hidden');
        if (r.name) window.state[r.name] = null;
      });
      g.querySelectorAll('.member-fields [data-slot="attachment"]').forEach(function(att) {
        if (!keepData) {
          att.querySelector('[data-clear-attachment]')?.click();
        } else {
          att.dataset.state = 'idle';
          var title = att.querySelector('[data-slot="attachment-title"]');
          if (title) title.textContent = att.dataset.idleTitle || att.dataset.originalTitle || '';
          var desc = att.querySelector('[data-slot="attachment-description"]');
          if (desc) desc.textContent = att.dataset.idleDescription || '';
          var media = att.querySelector('[data-slot="attachment-media"]');
          if (media && att.dataset.originalMedia) media.innerHTML = att.dataset.originalMedia;
        }
        var inp = att.querySelector('input[type="file"]');
        if (inp) inp.removeAttribute('required');
        var errEl = inp && inp.dataset.error ? document.getElementById(inp.dataset.error) : null;
        if (errEl) errEl.classList.add('hidden');
      });
      var mNum = g.dataset.member;
      var form = g.closest('form');
      if (form && !keepData) {
        ['igFollow_' + mNum, 'twibbon_' + mNum].forEach(function(name) {
          var existing = form.querySelector('input[name="delete_' + name + '"]');
          if (!existing) {
            var del = document.createElement('input');
            del.type = 'hidden';
            del.name = 'delete_' + name;
            del.value = '1';
            form.appendChild(del);
          }
        });
      }
      g.querySelector('.member-fields').classList.add('opacity-30', 'pointer-events-none');
      g.querySelector('.member-overlay').classList.remove('hidden');
      g.querySelector('.cancel-member').classList.add('hidden');
    }

    function shiftDown(toNum, fromNum) {
      var prefix = {
        2: 'firstMember',
        3: 'secondMember'
      };
      var to = document.querySelector('.member-group[data-member="' + toNum + '"]');
      var from = document.querySelector('.member-group[data-member="' + fromNum + '"]');
      if (!to || !from) return;

      ['Name', 'PhoneNumber'].forEach(function(field) {
        var fromInput = from.querySelector('.member-input[name="' + prefix[fromNum] + field + '"]');
        var toInput = to.querySelector('.member-input[name="' + prefix[toNum] + field + '"]');
        if (fromInput && toInput) toInput.value = fromInput.value;
      });

      var fromChecked = from.querySelector('.member-radio[name="' + prefix[fromNum] + 'Gender"]:checked');
      if (fromChecked) {
        var toRadio = to.querySelector('.member-radio[name="' + prefix[toNum] + 'Gender"][value="' + fromChecked.value + '"]');
        if (toRadio) {
          toRadio.checked = true;
          if (window.state) window.state[toRadio.name] = toRadio.value;
        }
      }

      var fromAtt = from.querySelector('[data-slot="attachment"]');
      var toAtt = to.querySelector('[data-slot="attachment"]');
      if (fromAtt && toAtt) {
        var newName = fromAtt.dataset.inputName.replace(fromNum, toNum);
        toAtt.dataset.state = fromAtt.dataset.state;
        toAtt.dataset.originalSrc = fromAtt.dataset.originalSrc || '';
        toAtt.dataset.originalTitle = fromAtt.dataset.originalTitle || '';
        toAtt.dataset.originalMedia = fromAtt.dataset.originalMedia || '';
        toAtt.dataset.inputName = newName;

        var toFile = toAtt.querySelector('input[type="file"]');
        var fromFile = fromAtt.querySelector('input[type="file"]');
        if (toFile && fromFile) {
          toFile.name = newName;
          toFile.dataset.error = 'err-anggota-' + toNum + '-card';
          toFile.value = '';
          if (toAtt.dataset.originalSrc) toFile.removeAttribute('required');
        }

        var toTitle = toAtt.querySelector('[data-slot="attachment-title"]');
        var fromTitle = fromAtt.querySelector('[data-slot="attachment-title"]');
        if (toTitle && fromTitle) toTitle.textContent = fromTitle.textContent;

        var toDesc = toAtt.querySelector('[data-slot="attachment-description"]');
        var fromDesc = fromAtt.querySelector('[data-slot="attachment-description"]');
        if (toDesc && fromDesc) toDesc.textContent = fromDesc.textContent;

        var toMedia = toAtt.querySelector('[data-slot="attachment-media"]');
        var fromMedia = fromAtt.querySelector('[data-slot="attachment-media"]');
        if (toMedia && fromMedia) toMedia.innerHTML = fromMedia.innerHTML;
      }

      to.querySelectorAll('.member-input').forEach(function(i) {
        i.dispatchEvent(new Event('input', {
          bubbles: true
        }));
      });

      var form = from.closest('form');
      if (form) {
        var move = document.createElement('input');
        move.type = 'hidden';
        move.name = 'move_member_' + fromNum + '_to_' + toNum;
        move.value = '1';
        form.appendChild(move);
      }

      clearMember(from, true);
    }
  </script>
<?php endif; ?>