<?php

use App\Components\Icon;

/** @var string $csrf_token */
/** @var string $user_name */
/** @var array|null $team */
/** @var array|null $payment */
/** @var array|null $submission */
/** @var array $uploads */
/** @var int $activeTab */

$main_class = 'flex-grow w-full';

$DIVISION_LABELS = ['LF' => 'Line Follower', 'PLC' => 'Programmable Logic Controller', 'FFR' => 'Fire Fighting Robot', 'LKTI' => 'Lomba Karya Tulis Ilmiah', 'PROG' => 'Program'];

$tabs = [
  1 => ['label' => 'Registrasi Tim', 'slug' => 'team-register'],
  2 => ['label' => 'Data Anggota', 'slug' => 'members'],
  3 => ['label' => 'Media Sosial', 'slug' => 'social-media'],
  4 => ['label' => 'Review & Submit', 'slug' => 'review'],
];

$upload1 = $uploads[1] ?? [];
$tabDone = [
  1 => (bool) $team,
  2 => !empty($team['leaderName']),
  3 => !empty($upload1['ig_follow']) && !empty($upload1['twibbon']),
  4 => (bool) $team && !empty($team['leaderName']) && !empty($upload1['ig_follow']) && !empty($upload1['twibbon']),
];

if (!isset($activeTab)) {
  $activeTab = 1;
  foreach ($tabDone as $n => $done) {
    if (!$done) {
      $activeTab = $n;
      break;
    }
    $activeTab = $n;
  }
}
?>
<div class="min-h-screen bg-gray-50">
  <div class="bg-brand border-b border-gray-200 text-white">
    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-14">
      <div>
        <h1 class="text-lg font-bold">Hi, <?= htmlspecialchars(explode(' ', $user_name ?? '')[0]) ?>!</h1>
        <p class="text-xs -mt-0.5">Kelola pendaftaran tim kamu.</p>
      </div>
      <?php $current = 'application';
      include __DIR__ . '/partials/nav-tabs.php'; ?>
      <form action="/logout" method="POST" class="m-0">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-black bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
          <?= Icon::make()->name('log-out')->class('w-3.5 h-3.5') ?>
          Logout
        </button>
      </form>
    </div>
  </div>

  <div class="px-4 sm:px-6 lg:px-8 py-4">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
      <div class="border-b border-gray-200">
        <div class="px-4 sm:px-6">
          <div role="tablist" class="flex overflow-x-auto gap-1 p-1" id="tabList">
            <?php foreach ($tabs as $num => $tab): ?>
              <?php
              $isActive = $activeTab === $num;
              $locked = ($num === 2 && !$team) || ($num === 3 && (!$team || empty($team['leaderName']))) || ($num === 4 && !$tabDone[4]);
              ?>
              <?php if ($locked): ?>
                <span role="tab" aria-disabled="true"
                  class="relative flex-1 justify-center inline-flex items-center gap-2 px-2 sm:px-4 py-3 sm:py-4 text-xs sm:text-sm font-bold whitespace-nowrap rounded-xl transition-colors text-gray-400 opacity-40 pointer-events-none cursor-not-allowed">
                  <?php if ($tabDone[$num]): ?>
                    <?= Icon::make()->name('check')->class('w-4 h-4 text-green-500 shrink-0') ?>
                  <?php else: ?>
                    <span class="w-4 h-4 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px] font-bold shrink-0"><?= $num ?></span>
                  <?php endif; ?>
                  <span class="hidden sm:inline"><?= htmlspecialchars($tab['label']) ?></span>
                </span>
              <?php else: ?>
                <a href="/application/<?= $tab['slug'] ?>" role="tab" aria-selected="<?= $isActive ? 'true' : 'false' ?>"
                  class="relative flex-1 justify-center inline-flex items-center gap-2 px-2 sm:px-4 py-3 sm:py-4 text-xs sm:text-sm font-bold whitespace-nowrap rounded-xl transition-colors no-underline
                    <?= $isActive ? 'bg-brand text-white hover:bg-red-800' : 'text-gray-500 hover:bg-black/5' ?>">
                  <?php if ($tabDone[$num]): ?>
                    <?= Icon::make()->name('check')->class('w-4 h-4 text-green-500 shrink-0') ?>
                  <?php else: ?>
                    <span class="w-4 h-4 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px] font-bold shrink-0"><?= $num ?></span>
                  <?php endif; ?>
                  <span class="hidden sm:inline"><?= htmlspecialchars($tab['label']) ?></span>
                </a>
              <?php endif; ?>
            <?php endforeach; ?>

          </div>
        </div>
      </div>

      <div class="border-t border-gray-100">
        <?php foreach ($tabs as $num => $tab): ?>
          <div role="tabpanel"
            data-tab="<?= $num ?>"
            class="tab-panel p-4 sm:p-6 <?= $activeTab === $num ? '' : 'hidden' ?>">
            <?php include __DIR__ . "/partials/tab-" . match ($num) {
              1 => 'team-register',
              2 => 'members',
              3 => 'social-media',
              4 => 'review'
            } . ".php"; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="flex items-center justify-end gap-3 px-4 sm:px-6 py-4 border-t border-gray-100" id="tabNav">
        <span class="text-xs text-gray-400 mr-auto" id="tabIndicator">Tab <?= $activeTab ?> dari 4</span>
        <a href="/application/<?= $activeTab > 1 ? $tabs[$activeTab - 1]['slug'] : '' ?>"
          class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-800 transition-all no-underline <?= $activeTab <= 1 ? 'pointer-events-none opacity-30' : '' ?>"
          id="prevTab">
          <?= Icon::make()->name('chevron-left')->class('w-4 h-4') ?>
          Kembali
        </a>
        <?php if ($activeTab < 4): ?>
          <button type="button" id="nextTab" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-brand rounded-xl hover:bg-brand/90 transition-all disabled:opacity-30 disabled:pointer-events-none">
            Simpan & Lanjut
            <?= Icon::make()->name('chevron-right')->class('w-4 h-4') ?>
          </button>
        <?php else: ?>
          <button type="button" id="nextTab" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-xl hover:bg-green-700 transition-all disabled:opacity-30 disabled:pointer-events-none">
            Submit
            <?= Icon::make()->name('check')->class('w-4 h-4') ?>
          </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const nextBtn = document.getElementById('nextTab');
    const current = <?= $activeTab ?>;
    const total = 4;
    const slugs = <?= json_encode(array_values(array_map(fn($t) => $t['slug'], $tabs))) ?>;

    function goTo(num) {
      if (num >= 1 && num <= total) window.location.href = '/application/' + slugs[num - 1];
    }

    function validateTab(num) {
      const panel = document.querySelector(`.tab-panel[data-tab="${num}"]`);
      if (!panel) return true;
      const fields = panel.querySelectorAll('[required]');
      let valid = true;
      const radioGroups = new Set();
      fields.forEach(f => {
        if (f.type === 'radio') {
          if (radioGroups.has(f.name)) return;
          radioGroups.add(f.name);
          const group = panel.querySelectorAll(`input[type="radio"][name="${f.name}"]`);
          const checked = Array.from(group).some(r => r.checked);
          const errEl = document.getElementById(f.dataset.error);
          group.forEach(r => r.closest('.division-card')?.classList.toggle('border-red-500', !checked));
          if (!checked) {
            if (errEl) errEl.classList.remove('hidden');
            valid = false;
          } else {
            if (errEl) errEl.classList.add('hidden');
          }
          return;
        }
        const errId = f.dataset.error;
        const errEl = errId ? document.getElementById(errId) : null;
        const empty = f.type === 'file' ? f.files.length === 0 : !f.value.trim();
        if (empty) {
          f.classList.add('border-red-500');
          var att = f.closest('[data-slot="attachment"]');
          if (att) att.dataset.state = 'error';
          if (errEl) {
            errEl.textContent = errEl.dataset.msg || 'Field wajib diisi';
            errEl.classList.remove('hidden');
          }
          valid = false;
        } else {
          f.classList.remove('border-red-500');
          var att = f.closest('[data-slot="attachment"]');
          if (att) att.dataset.state = 'done';
          if (errEl) errEl.classList.add('hidden');
        }
      });
      return valid;
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function() {
        if (!validateTab(current)) return;
        const panel = document.querySelector(`.tab-panel[data-tab="${current}"]`);
        const form = panel?.querySelector('form');
        if (form) {
          const btn = form.querySelector('button[type="submit"]');
          if (btn) {
            btn.click();
            return;
          }
          form.submit();
          return;
        }
        if (current < total) goTo(current + 1);
      });
    }

    document.addEventListener('change', function(e) {
      if (!e.target.matches('[data-preview]')) return;
      var file = e.target.files && e.target.files[0];
      if (!file) return;
      var att = e.target.closest('[data-slot="attachment"]');
      if (!att) return;
      var media = att.querySelector('[data-slot="attachment-media"]');
      if (!media) return;
      var reader = new FileReader();
      reader.onload = function(ev) {
        media.innerHTML = '<img src="' + ev.target.result + '" class="w-full h-full object-cover">';
      };
      reader.readAsDataURL(file);
    });

    document.addEventListener('click', function(e) {
      var clearBtn = e.target.closest('[data-clear-attachment]');
      if (!clearBtn) return;
      var att = clearBtn.closest('[data-slot="attachment"]');
      if (!att) return;
      var input = att.querySelector('input[type="file"]');
      if (input) input.value = '';
      att.dataset.state = 'idle';
      var media = att.querySelector('[data-slot="attachment-media"]');
      if (media) media.innerHTML = '<?= addslashes(Icon::make()->name('image')->class('size-5 text-gray-400')) ?>';
    });

    const divCards = document.getElementById('divisionCards');
    const hint = document.getElementById('division_hint');
    if (divCards) {
      const radios = divCards.querySelectorAll('input[name="division"]');
      const hints = divCards.querySelectorAll('[data-division-hint]');

      function upd(val) {
        const two = val === 'LF' || val === 'PLC' || val === 'PROG';
        const three = val === 'FFR' || val === 'LKTI';
        if (hint) {
          hint.textContent = two ? '* Maksimal 2 anggota (1 Ketua + 1 Anggota)' : (three ? '* Maksimal 3 anggota (1 Ketua + 2 Anggota)' : '');
          hint.classList.toggle('hidden', !val);
        }
        hints.forEach(el => {
          const p = el.closest('.division-card');
          const rb = p?.querySelector('input[type="radio"]');
          if (!rb) return;
          el.textContent = rb.value === 'FFR' || rb.value === 'LKTI' ? 'max 3 org' : 'max 2 org';
        });
      }
      radios.forEach(r => r.addEventListener('change', () => upd(r.value)));
      const checked = divCards.querySelector('input[name="division"]:checked');
      if (checked) upd(checked.value);
      hints.forEach(el => {
        const p = el.closest('.division-card');
        const rb = p?.querySelector('input[type="radio"]');
        if (!rb) return;
        el.textContent = rb.value === 'LF' || rb.value === 'PLC' ? 'max 2 org' : 'max 3 org';
      });
    }
  });
</script>