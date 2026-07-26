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
              <?php $isActive = $activeTab === $num; ?>
              <a href="/application/<?= $tab['slug'] ?>" role="tab" data-tab-num="<?= $num ?>" aria-selected="<?= $isActive ? 'true' : 'false' ?>"
                class="relative flex-1 justify-center inline-flex items-center gap-2 px-2 sm:px-4 py-3 sm:py-4 text-xs sm:text-sm font-bold whitespace-nowrap rounded-xl transition-colors no-underline
                  <?= $isActive ? 'bg-brand text-white hover:bg-red-800' : 'text-gray-500 hover:bg-black/5' ?>">
                <span class="tab-num w-4 h-4 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px] font-bold shrink-0 <?= $tabDone[$num] ? 'hidden' : '' ?>"><?= $num ?></span>
                <span class="tab-check shrink-0 <?= $tabDone[$num] ? '' : 'hidden' ?>"><?= Icon::make()->name('check')->class('w-4 h-4 text-green-500') ?></span>
                <span class="hidden sm:inline"><?= htmlspecialchars($tab['label']) ?></span>
              </a>
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
        <a href="/application/<?= $activeTab > 1 ? $tabs[$activeTab - 1]['slug'] : '' ?>" role="button" data-goto="<?= $activeTab - 1 ?>"
          class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-800 transition-all no-underline"
          id="prevTab" <?= $activeTab <= 1 ? 'style="display:none"' : '' ?>>
          <?= Icon::make()->name('chevron-left')->class('w-4 h-4') ?>
          Kembali
        </a>
        <button type="button" id="nextTabSave" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-brand rounded-xl hover:bg-brand/90 transition-all disabled:opacity-30 disabled:pointer-events-none" <?= $activeTab >= 4 ? 'style="display:none"' : '' ?>>
          Simpan & Lanjut
          <?= Icon::make()->name('chevron-right')->class('w-4 h-4') ?>
        </button>
        <button type="button" id="nextTabSubmit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-xl hover:bg-green-700 transition-all disabled:opacity-30 disabled:pointer-events-none" <?= $activeTab >= 4 ? '' : 'style="display:none"' ?>>
          Submit
          <?= Icon::make()->name('check')->class('w-4 h-4') ?>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    let current = <?= $activeTab ?>;
    const total = 4;
    const slugs = <?= json_encode(array_values(array_map(fn($t) => $t['slug'], $tabs))) ?>;
    const slugToNum = {};
    slugs.forEach(function(s, i) { slugToNum[s] = i + 1; });

    const state = <?= json_encode([
      'division' => $team['division'] ?? null,
      'name' => $team['name'] ?? null,
      'teamSchool' => $team['teamSchool'] ?? null,
      'leaderName' => $team['leaderName'] ?? null,
      'leaderPhoneNumber' => $team['leaderPhoneNumber'] ?? null,
      'firstMemberName' => $team['firstMemberName'] ?? null,
      'firstMemberPhoneNumber' => $team['firstMemberPhoneNumber'] ?? null,
      'secondMemberName' => $team['secondMemberName'] ?? null,
      'secondMemberPhoneNumber' => $team['secondMemberPhoneNumber'] ?? null,
      'studentCard_1' => $uploads[1]['student_card'] ?? null,
      'studentCard_2' => $uploads[2]['student_card'] ?? null,
      'studentCard_3' => $uploads[3]['student_card'] ?? null,
      'igFollow_1' => $uploads[1]['ig_follow'] ?? null,
      'igFollow_2' => $uploads[2]['ig_follow'] ?? null,
      'igFollow_3' => $uploads[3]['ig_follow'] ?? null,
      'twibbon_1' => $uploads[1]['twibbon'] ?? null,
      'twibbon_2' => $uploads[2]['twibbon'] ?? null,
      'twibbon_3' => $uploads[3]['twibbon'] ?? null,
    ]) ?>;

    const savedState = JSON.parse(JSON.stringify(state));

    function hasChanges() {
      for (var k in state) {
        if (state[k] instanceof File) return true;
        var cur = state[k] == null ? '' : String(state[k]).trim();
        var orig = savedState[k] == null ? '' : String(savedState[k]).trim();
        if (cur !== orig) return true;
      }
      return false;
    }

    function filled(k) {
      var v = state[k];
      if (v == null) return false;
      return v instanceof File ? true : String(v).trim() !== '';
    }

    function tabDone(n) {
      if (n === 1) return filled('division') && filled('name') && filled('teamSchool');
      if (n === 2) {
        if (!filled('leaderName')) return false;
        var ok = true;
        document.querySelectorAll('.member-group[data-member][data-activated="true"]').forEach(function(g) {
          var m = parseInt(g.dataset.member);
          if (m === 1) return;
          var nameKey = m === 2 ? 'firstMemberName' : 'secondMemberName';
          if (!filled(nameKey)) ok = false;
        });
        return ok;
      }
      if (n === 3) {
        if (!filled('igFollow_1') || !filled('twibbon_1')) return false;
        if (filled('firstMemberName') && (!filled('igFollow_2') || !filled('twibbon_2'))) return false;
        if (filled('secondMemberName') && (!filled('igFollow_3') || !filled('twibbon_3'))) return false;
        return true;
      }
      if (n === 4) return tabDone(1) && tabDone(2) && tabDone(3);
      return false;
    }

    function updateTabs() {
      for (var n = 1; n <= total; n++) {
        var link = document.querySelector('#tabList a[data-tab-num="' + n + '"]');
        if (!link) continue;
        var done = tabDone(n);
        var numEl = link.querySelector('.tab-num');
        var checkEl = link.querySelector('.tab-check');
        if (numEl) numEl.classList.toggle('hidden', done);
        if (checkEl) checkEl.classList.toggle('hidden', !done);
      }
    }

    function resetTab() {
      var panel = document.querySelector('.tab-panel[data-tab="' + current + '"]');
      if (!panel) return;
      panel.querySelectorAll('input, textarea, select').forEach(function(el) {
        if (!el.name || !state.hasOwnProperty(el.name)) return;
        var orig = savedState[el.name];
        if (el.type === 'file') {
          el.value = '';
          state[el.name] = orig;
        } else if (el.type === 'radio') {
          el.checked = el.value === (orig || '');
          state[el.name] = el.checked ? el.value : state[el.name];
        } else {
          el.value = orig || '';
          state[el.name] = orig;
        }
      });
    }

    function goTo(num) {
      if (num < 1 || num > total) return;
      document.querySelectorAll('.tab-panel').forEach(function(p) {
        p.classList.toggle('hidden', parseInt(p.dataset.tab) !== num);
      });
      document.querySelectorAll('#tabList a[role="tab"]').forEach(function(a) {
        var isActive = parseInt(a.dataset.tabNum) === num;
        a.setAttribute('aria-selected', isActive ? 'true' : 'false');
        a.classList.toggle('bg-brand', isActive);
        a.classList.toggle('text-white', isActive);
        a.classList.toggle('hover:bg-red-800', isActive);
        a.classList.toggle('text-gray-500', !isActive);
        a.classList.toggle('hover:bg-black/5', !isActive);
      });
      var indicator = document.getElementById('tabIndicator');
      if (indicator) indicator.textContent = 'Tab ' + num + ' dari 4';
      var prevBtn = document.getElementById('prevTab');
      if (prevBtn) {
        prevBtn.style.display = num <= 1 ? 'none' : '';
        prevBtn.href = num > 1 ? '/application/' + slugs[num - 2] : '#';
        prevBtn.dataset.goto = num > 1 ? num - 1 : '';
      }
      var saveBtn = document.getElementById('nextTabSave');
      var submitBtn = document.getElementById('nextTabSubmit');
      if (saveBtn) saveBtn.style.display = num >= 4 ? 'none' : '';
      if (submitBtn) submitBtn.style.display = num < 4 ? 'none' : '';
      history.pushState({ tab: num }, '', '/application/' + slugs[num - 1]);
      var activeLink = document.querySelector('#tabList a[data-tab-num="' + num + '"]');
      if (activeLink) activeLink.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
      current = num;
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

    document.getElementById('tabList').addEventListener('click', function(e) {
      var link = e.target.closest('a[data-tab-num]');
      if (link) {
        e.preventDefault();
        var target = parseInt(link.dataset.tabNum);
        if (target !== current) resetTab();
        goTo(target);
      }
    });

    document.getElementById('tabNav').addEventListener('click', function(e) {
      var prev = e.target.closest('[data-goto]');
      if (prev) {
        e.preventDefault();
        var target = parseInt(prev.dataset.goto);
        if (target !== current) resetTab();
        goTo(target);
        return;
      }
      var next = e.target.closest('#nextTabSave, #nextTabSubmit');
      if (next) {
        if (!validateTab(current)) return;
        if (!hasChanges()) {
          if (current < total) goTo(current + 1);
          return;
        }
        var panel = document.querySelector('.tab-panel[data-tab="' + current + '"]');
        var form = panel && panel.querySelector('form');
        if (form) {
          var btn = form.querySelector('button[type="submit"]');
          if (btn) { btn.click(); return; }
          form.submit();
          return;
        }
        if (current < total) goTo(current + 1);
      }
    });

    window.addEventListener('popstate', function() {
      var slug = window.location.pathname.split('/').pop();
      if (slugToNum[slug]) goTo(slugToNum[slug]);
    });

    document.addEventListener('input', function(e) {
      if (e.target.name) {
        state[e.target.name] = e.target.value;
        updateTabs();
      }
    });

    document.addEventListener('change', function(e) {
      if (e.target.name) {
        if (e.target.type === 'file') {
          state[e.target.name] = e.target.files[0] || null;
        } else if (e.target.type === 'radio') {
          state[e.target.name] = e.target.value;
        }
        updateTabs();
      }
    });

    updateTabs();

    function autoRedirect() {
      for (var n = 1; n <= total; n++) {
        if (!tabDone(n)) {
          if (current !== n) goTo(n);
          return;
        }
      }
    }

    if (window.location.pathname.replace(/\/+$/, '') === '/application') {
      autoRedirect();
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