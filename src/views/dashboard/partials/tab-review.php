<?php

use App\Components\Attachment;
use App\Components\Dialog;
use App\Components\Icon;

/** @var array|null $team */
/** @var string $csrf_token */
/** @var array $uploads */

$DIVISION_LABELS = ['LF' => 'Line Follower', 'PLC' => 'Programmable Logic Controller', 'FFR' => 'Fire Fighting Robot', 'LKTI' => 'Lomba Karya Tulis Ilmiah', 'PROG' => 'Algoritma Program'];
$DIVISION_ICONS = ['LF' => '/image/lf_icon.png', 'PLC' => '/image/plc_icon.png', 'FFR' => '/image/ffr_icon.png', 'LKTI' => '/image/lkti_icon.png', 'PROG' => '/image/program_icon.png'];
$UPLOAD_URL = '/uploads/teams/';

$sectionBtn = function (): string {
  return '<button type="submit" formaction="/application/team/update" disabled'
    . ' class="review-section-btn relative inline-flex items-center justify-center min-w-[5rem] px-4 py-2 text-xs font-semibold rounded-lg transition-all border text-gray-400 bg-white border-gray-200">'
    . '<span class="review-btn-label transition-all duration-300">Simpan</span>'
    . Icon::make()->name('check')->class('review-btn-check text-white bg-brand h-full w-full size-1 absolute inset-0 m-auto p-1.5 opacity-0 transition-all rounded-lg duration-100')->render()
    . '</button>';
};
?>
<?php if (!$team): ?>
  <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-200 rounded-xl">
    <?= Icon::make()->name('alert-circle')->class('w-5 h-5 text-gray-400 shrink-0') ?>
    <p class="text-sm text-gray-500">Daftarkan tim terlebih dahulu di tab sebelumnya.</p>
  </div>
<?php else:
  $members = [
    ['num' => 1, 'label' => 'Anggota 1', 'role' => 'Ketua Tim', 'nameKey' => 'leaderName', 'phoneKey' => 'leaderPhoneNumber', 'genderKey' => 'leaderGender'],
  ];
  if (!empty($team['firstMemberName'])) $members[] = ['num' => 2, 'label' => 'Anggota 2', 'role' => '', 'nameKey' => 'firstMemberName', 'phoneKey' => 'firstMemberPhoneNumber', 'genderKey' => 'firstMemberGender'];
  if (!empty($team['secondMemberName'])) $members[] = ['num' => 3, 'label' => 'Anggota 3', 'role' => '', 'nameKey' => 'secondMemberName', 'phoneKey' => 'secondMemberPhoneNumber', 'genderKey' => 'secondMemberGender'];
?>
  <form id="reviewForm" action="/application/team/submit" method="POST" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <input type="hidden" name="next_tab" value="review">
    <input type="hidden" name="current_tab" value="review">

    <div id="reviewFormError" class="hidden flex items-start gap-3 p-4 rounded-xl border border-red-200 bg-red-50 mb-6">
      <p class="text-sm text-red-700"></p>
    </div>

    <div class="space-y-6">
      <section data-section="team" class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
        <label class="block text-sm font-semibold mb-3">Pilih Divisi Lomba<span class="text-red-500">*</span></label>
        <div class="flex flex-wrap justify-center gap-3">
          <?php foreach ($DIVISION_LABELS as $k => $v):
            $isDiv = $team['division'] === $k;
          ?>
            <label class="division-card relative block w-[calc(50%-0.375rem)] rounded-xl border-2 border-gray-200 p-4 cursor-pointer hover:border-brand/50 hover:bg-brand/5 transition-all has-[:checked]:border-brand has-[:checked]:bg-brand/5 has-[:checked]:ring-2 has-[:checked]:ring-brand/20 text-center">
              <input type="radio" name="division" value="<?= $k ?>" class="hidden" <?= $isDiv ? 'checked' : '' ?>>
              <div class="w-18 h-18 md:w-28 md:h-28 rounded-xl flex items-center justify-center mb-2 mx-auto"><img src="<?= $DIVISION_ICONS[$k] ?? '' ?>" alt="<?= htmlspecialchars($v) ?>" class="w-28 h-28 object-contain"></div>
              <p class="text-sm font-medium "><?= htmlspecialchars($v) ?></p>
            </label>
          <?php endforeach; ?>
        </div>
        <div class="mt-4">
          <label class="block text-sm font-semibold mb-3">Informasi Tim</label>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1.5">Nama Tim<span class="text-red-500">*</span></label>
              <input type="text" name="name" required value="<?= htmlspecialchars($team['name'] ?? '') ?>" data-error="err-review-name" placeholder="Masukkan nama tim"
                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
                oninput="this.classList.remove('border-red-500'); document.getElementById('err-review-name')?.classList.add('hidden')">
              <p id="err-review-name" class="text-xs text-red-500 mt-1 hidden">Nama tim wajib diisi</p>
            </div>
            <div>
              <label class="block text-sm font-medium a mb-1.5">Asal Sekolah<span class="text-red-500">*</span></label>
              <input type="text" name="teamSchool" required value="<?= htmlspecialchars($team['teamSchool'] ?? '') ?>" data-error="err-review-school" placeholder="Masukkan nama sekolah"
                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
                oninput="this.classList.remove('border-red-500'); document.getElementById('err-review-school')?.classList.add('hidden')">
              <p id="err-review-school" class="text-xs text-red-500 mt-1 hidden">Asal sekolah wajib diisi</p>
            </div>
          </div>
        </div>
        <div class="mt-5 flex justify-end"><?= $sectionBtn() ?></div>
      </section>

      <section data-section="members" class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold a mb-4">Data Anggota</h3>
        <div class="space-y-4">
          <?php foreach ($members as $m):
            $p = $m['num'];
            $name = $team[$m['nameKey']] ?? '';
            $phone = $team[$m['phoneKey']] ?? '';
            $gender = $team[$m['genderKey']] ?? '';
            $existingCard = $uploads[$p]['student_card'] ?? null;
            $originalCard = $uploads[$p]['original_student_card'] ?? null;
            $cardRequired = !$existingCard;
            $cardAttrs = ['accept' => 'image/*,.pdf,application/pdf', 'data-error' => 'err-review-' . $p . '-card', 'max-size' => 10 * 1024 * 1024];
            if ($cardRequired) $cardAttrs['required'] = true;
            $cardIcon = Icon::make()->name('credit-card')->class('size-5 text-black');
          ?>
            <div class="member-group bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
              <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center text-xs font-bold text-brand"><?= $p ?></div>
                <div>
                  <p class="text-sm font-semibold"><?= htmlspecialchars($m['label']) ?></p>
                  <?php if ($m['role']): ?><p class="text-xs text-gray-400"><?= htmlspecialchars($m['role']) ?></p><?php endif; ?>
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-1.5">Nama Lengkap<span class="text-red-500">*</span></label>
                  <input type="text" name="<?= $m['nameKey'] ?>" value="<?= htmlspecialchars($name) ?>" placeholder="Masukkan nama lengkap" required
                    data-error="err-review-<?= $p ?>-name"
                    class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
                    oninput="this.classList.remove('border-red-500'); document.getElementById(this.dataset.error)?.classList.add('hidden')">
                  <p id="err-review-<?= $p ?>-name" class="text-xs text-red-500 mt-1 hidden">Nama lengkap wajib diisi</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1.5">No. Telepon / WA<span class="text-red-500">*</span></label>
                  <input type="text" name="<?= $m['phoneKey'] ?>" value="<?= htmlspecialchars($phone) ?>" placeholder="Masukkan nomor telepon" required inputmode="numeric" pattern="08[0-9]{6,}"
                    data-error="err-review-<?= $p ?>-phone"
                    data-format-error="err-review-<?= $p ?>-phone-format"
                    class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
                    oninput="this.value=this.value.replace(/[^0-9]/g,''); this.classList.remove('border-red-500'); document.getElementById(this.dataset.error)?.classList.add('hidden'); document.getElementById(this.dataset.formatError)?.classList.add('hidden')">
                  <p id="err-review-<?= $p ?>-phone" class="text-xs text-red-500 mt-1 hidden">No. telepon wajib diisi</p>
                  <p id="err-review-<?= $p ?>-phone-format" class="text-xs text-red-500 mt-1 hidden">Nomer harus berawalan 08xx & minimal 8 digit</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1.5">Jenis Kelamin</label>
                  <div class="flex gap-3">
                    <label class="flex items-center gap-2 px-4 py-1 cursor-pointer transition-all">
                      <input type="radio" name="<?= $m['genderKey'] ?>" value="Laki-laki" class="member-radio accent-brand cursor-pointer" <?= $p === 1 ? 'required' : '' ?> data-error="err-review-<?= $p ?>-gender" <?= $gender === 'Laki-laki' ? 'checked' : '' ?>>
                      <span class="text-sm ">Laki-laki</span>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-1 cursor-pointer transition-all">
                      <input type="radio" name="<?= $m['genderKey'] ?>" value="Perempuan" class="member-radio accent-brand cursor-pointer" <?= $p === 1 ? 'required' : '' ?> data-error="err-review-<?= $p ?>-gender" <?= $gender === 'Perempuan' ? 'checked' : '' ?>>
                      <span class="text-sm ">Perempuan</span>
                    </label>
                  </div>
                  <p id="err-review-<?= $p ?>-gender" class="text-xs text-red-500 mt-1 hidden">Jenis kelamin wajib dipilih</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1.5">Kartu Pelajar<span class="text-red-500">*</span></label>
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
                  <p id="err-review-<?= $p ?>-card" class="text-xs text-red-500 mt-1 hidden">Kartu pelajar wajib diupload</p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="mt-5 flex justify-end"><?= $sectionBtn() ?></div>
      </section>

      <section data-section="social" class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold mb-4">Media Sosial</h3>
        <div class="space-y-4">
          <?php foreach ($members as $m):
            $p = $m['num'];
            $ig = $uploads[$p]['ig_follow'] ?? null;
            $twibbon = $uploads[$p]['twibbon'] ?? null;
            $originalIg = $uploads[$p]['original_ig_follow'] ?? null;
            $originalTwibbon = $uploads[$p]['original_twibbon'] ?? null;
            $igIcon = Icon::make()->name('instagram')->class('size-5 text-black');
            $twibbonIcon = Icon::make()->name('user-round')->class('size-5 text-black');
            $igAttrs = ['accept' => 'image/*,.pdf,application/pdf', 'data-error' => 'err-review-' . $p . '-ig', 'max-size' => 10 * 1024 * 1024];
            if (!$ig) $igAttrs['required'] = true;
            $twibbonAttrs = ['accept' => 'image/*,.pdf,application/pdf', 'data-error' => 'err-review-' . $p . '-twibbon', 'max-size' => 10 * 1024 * 1024];
            if (!$twibbon) $twibbonAttrs['required'] = true;
          ?>
            <div class="member-group bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
              <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center text-xs font-bold text-brand"><?= $p ?></div>
                <div>
                  <p class="text-sm font-semibold"><?= htmlspecialchars($team[$m['nameKey']] ?? $m['label']) ?></p>
                  <p class="text-xs text-gray-400"><?= htmlspecialchars($m['role'] ? ($m['label'] . ' (' . $m['role'] . ')') : $m['label']) ?></p>
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-1.5">Bukti Follow Instagram<span class="text-red-500">*</span></label>
                  <?php if ($ig): ?>
                    <?= Attachment::make()
                      ->mediaVariant('image')
                      ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($ig) . '" class="w-full h-full object-cover">')
                      ->title($originalIg ?: basename($ig))
                      ->description('Bukti follow Instagram @automationweek')
                      ->clearable()
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
                      ->clearable()
                      ->withPreview()
                      ->originalMedia($igIcon)
                      ->fileInput('igFollow_' . $p, $igAttrs)
                      ->render() ?>
                  <?php endif; ?>
                  <p id="err-review-<?= $p ?>-ig" class="text-xs text-red-500 mt-1 hidden">Bukti follow wajib diupload</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1.5">Upload Twibbon<span class="text-red-500">*</span></label>
                  <?php if ($twibbon): ?>
                    <?= Attachment::make()
                      ->mediaVariant('image')
                      ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($twibbon) . '" class="w-full h-full object-cover">')
                      ->title($originalTwibbon ?: basename($twibbon))
                      ->description('Foto profil dengan twibbon')
                      ->clearable()
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
                      ->clearable()
                      ->withPreview()
                      ->originalMedia($twibbonIcon)
                      ->fileInput('twibbon_' . $p, $twibbonAttrs)
                      ->render() ?>
                  <?php endif; ?>
                  <p id="err-review-<?= $p ?>-twibbon" class="text-xs text-red-500 mt-1 hidden">Twibbon wajib diupload</p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="mt-5 flex justify-end"><?= $sectionBtn() ?></div>
      </section>
    </div>
  </form>

  <?= Dialog::make()->id('submit-confirm-dialog')->title('Konfirmasi Submit')->width('max-w-md')->content('
    <p class="text-sm text-gray-600 mb-6">Apakah kamu yakin data sudah benar dan ingin submit pendaftaran?</p>
    <div class="flex justify-end gap-3">
      <button onclick="closeDialog(\'submit-confirm-dialog\')" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">Batal</button>
      <button onclick="confirmSubmit()" class="px-4 py-2 text-sm font-semibold text-white bg-brand rounded-lg hover:bg-red-800 transition-colors">Ya, Submit</button>
    </div>
  ') ?>

  <script>
    (function() {
      var form = document.getElementById('reviewForm');
      if (!form) return;

      var onCls = ['text-white', 'bg-brand', 'border-transparent', 'hover:bg-red-800', 'cursor-pointer'];
      var offCls = ['text-gray-400', 'bg-white', 'border-gray-200'];
      var sections = [];

      form.querySelectorAll('.review-section-btn').forEach(function(btn) {
        var section = btn.closest('section[data-section]');
        if (!section) return;

        var originals = {};

        function captureOriginals() {
          originals = {};
          section.querySelectorAll('input[name]').forEach(function(i) {
            if (i.type === 'radio') {
              if (i.checked) originals[i.name] = i.value;
            } else if (i.type === 'file') {
              originals[i.name] = i.files[0] || null;
            } else {
              originals[i.name] = i.value;
            }
          });
        }

        captureOriginals();

        function sectionChanged() {
          var changed = false;
          section.querySelectorAll('input[name]').forEach(function(i) {
            if (changed) return;
            if (i.type === 'radio') {
              if (i.checked && i.value !== (originals[i.name] || '')) changed = true;
            } else if (i.type === 'file') {
              if ((i.files[0] || null) !== (originals[i.name] || null)) changed = true;
            } else if ((i.value || '') !== (originals[i.name] || '')) {
              changed = true;
            }
          });
          section.querySelectorAll('[data-slot="attachment"]').forEach(function(att) {
            if (changed) return;
            if (att.dataset.originalSrc && att.dataset.state === 'idle') changed = true;
          });
          return changed;
        }

        function update() {
          var on = sectionChanged();
          btn.disabled = !on;
          onCls.forEach(function(c) {
            btn.classList.toggle(c, on);
          });
          offCls.forEach(function(c) {
            btn.classList.toggle(c, !on);
          });
        }

        section.addEventListener('input', update);
        section.addEventListener('change', update);
        section.addEventListener('click', function(e) {
          if (e.target.closest('[data-clear-attachment]')) setTimeout(update, 0);
        });
        sections.push({
          section: section,
          update: update,
          capture: captureOriginals
        });
        update();
      });

      function sectionSaved(section) {
        sections.forEach(function(s) {
          if (section && s.section !== section) return;
          s.capture();
          s.update();
        });
      }

      function flashSaved(btn) {
        var label = btn.querySelector('.review-btn-label');
        var check = btn.querySelector('.review-btn-check');
        btn.disabled = true;
        if (label) label.classList.add('opacity-0');
        if (check) check.classList.remove('opacity-0', 'scale-50');
        setTimeout(function() {
          if (label) label.classList.remove('opacity-0');
          if (check) check.classList.add('opacity-0', 'scale-50');
        }, 1500);
      }

      function showError(msg) {
        var box = document.getElementById('reviewFormError');
        if (!box) return;
        var p = box.querySelector('p');
        if (p) p.innerHTML = msg;
        box.classList.remove('hidden');
      }

      window.confirmSubmit = function() {
        closeDialog('submit-confirm-dialog');
        form.requestSubmit();
      };

      form.addEventListener('submit', function(e) {
        var t = e.submitter || document.activeElement;
        var section = t && t.closest ? t.closest('section[data-section]') : null;
        if (section && window.validateScope && !window.validateScope(section)) e.preventDefault();
      });

      form.addEventListener('submit', function(e) {
        if (e.defaultPrevented) return;
        var t = e.submitter || document.activeElement;
        var section = t && t.closest ? t.closest('section[data-section]') : null;
        var btn = t && t.matches ? t.closest('button[type="submit"]') : null;
        var isSubmit = (btn && btn.formAction ? btn.formAction : form.action).indexOf('/submit') !== -1;

        e.preventDefault();
        if (btn) btn.disabled = true;
        document.getElementById('reviewFormError')?.classList.add('hidden');

        fetch((btn && btn.formAction) || form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(function(r) {
            return r.json().catch(function() {
              return null;
            });
          })
          .then(function(data) {
            if (data && data.ok) {
              if (isSubmit) {
                window.location.href = '/home';
                return;
              }
              if (btn) btn.disabled = false;
              if (btn) flashSaved(btn);
              sectionSaved(section);
              if (window.__syncSavedState) window.__syncSavedState(false);
            } else {
              if (btn) btn.disabled = false;
              showError((data && data.error) || 'Terjadi kesalahan. Silakan coba lagi.');
            }
          })
          .catch(function() {
            if (btn) btn.disabled = false;
            showError('Terjadi kesalahan jaringan. Silakan coba lagi.');
          });
      });

      var panel = form.closest('.tab-panel');
      if (panel && 'MutationObserver' in window) {
        new MutationObserver(function() {
          if (!panel.classList.contains('hidden')) sections.forEach(function(s) {
            s.update();
          });
        }).observe(panel, {
          attributes: true,
          attributeFilter: ['class']
        });
      }
    })();
  </script>
<?php endif; ?>