<?php

use App\Components\Icon;
use App\Utils\Session;

/** @var string $csrf_token */
/** @var string $user_name */
/** @var array|null $team */
/** @var array|null $payment */
/** @var bool $is_reviewed */
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
  4 => $is_reviewed,
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
  <?php $current = 'application';
  include __DIR__ . '/components/nav-tabs.php'; ?>

  <?php
  $flashError = Session::flash('team_update_error') ?? Session::flash('team_register_error');
  $flashSuccess = Session::flash('team_update_success');
  if ($flashError || $flashSuccess):
    $variant = $flashError ? 'error' : 'success';
    $msg = $flashError ?: $flashSuccess;
  ?>
    <?= \App\Components\Toast::make()->variant($variant)->message($msg)->render() ?>
  <?php endif; ?>

  <div>
    <div>
      <div class="border-b border-gray-200">
        <div class="px-4 sm:px-6">
          <div role="tablist" class="overflow-x-auto gap-1 p-1 hidden md:flex" id="tabList">
            <?php foreach ($tabs as $num => $tab): ?>
              <?php $isActive = $activeTab === $num; ?>
              <a href="/application/<?= $tab['slug'] ?>" role="tab" data-tab-num="<?= $num ?>" aria-selected="<?= $isActive ? 'true' : 'false' ?>"
                class="relative flex-1 justify-center inline-flex items-center gap-2 px-2 sm:px-4 py-3 sm:py-4 text-xs sm:text-sm font-bold whitespace-nowrap rounded-xl transition-colors no-underline
                  <?= $isActive ? 'bg-brand text-white hover:bg-red-800' : 'text-gray-500 hover:bg-black/5' ?>">
                <span class="tab-num w-4 h-4 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px] font-bold shrink-0 <?= $tabDone[$num] ? 'hidden' : '' ?>"><?= $num ?></span>
                <span class="tab-check shrink-0 <?= $tabDone[$num] ? '' : 'hidden' ?>">
                  <?= Icon::make()->name('check')
                    ->class('w-4 h-4 tab-check-icon')
                  ?>
                </span>
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
    </div>
    <div class="flex items-center justify-end gap-2 pb-4 pr-4 md:pr-6" id="tabNav">
      <a href="/application/<?= $activeTab > 1 ? $tabs[$activeTab - 1]['slug'] : '' ?>" role="button" data-goto="<?= $activeTab - 1 ?>"
        class="inline-flex items-center gap-2 px-4 py-2 text-xs md:text-sm font-medium text-black bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-800 transition-all no-underline"
        id="prevTab" <?= $activeTab <= 1 ? 'style="display:none"' : '' ?>>
        Kembali
      </a>
      <button type="button" id="nextTabSave" class="inline-flex items-center gap-2 px-4 py-2 text-xs md:text-sm font-semibold text-white bg-brand rounded-lg hover:bg-brand/90 transition-all cursor-pointer disabled:opacity-30 disabled:pointer-events-none" <?= $activeTab >= 4 ? 'style="display:none"' : '' ?>>
        Simpan & Lanjut
      </button>
      <button type="button" id="nextTabSubmit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-brand rounded-lg hover:bg-red-800 transition-all disabled:opacity-30 disabled:pointer-events-none cursor-pointer" <?= $activeTab >= 4 ? '' : 'style="display:none"' ?>>
        Submit
      </button>
    </div>
  </div>

  <?php include __DIR__ . '/partials/footer.php'; ?>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    let current = <?= $activeTab ?>;
    const total = 4;
    const slugs = <?= json_encode(array_values(array_map(fn($t) => $t['slug'], $tabs))) ?>;
    const slugToNum = {};
    slugs.forEach(function(s, i) {
      slugToNum[s] = i + 1;
    });

    const _state = <?= json_encode([
                      'division' => $team['division'] ?? null,
                      'name' => $team['name'] ?? null,
                      'teamSchool' => $team['teamSchool'] ?? null,
                      'leaderName' => $team['leaderName'] ?? null,
                      'leaderPhoneNumber' => $team['leaderPhoneNumber'] ?? null,
                      'leaderGender' => $team['leaderGender'] ?? null,
                      'firstMemberName' => $team['firstMemberName'] ?? null,
                      'firstMemberPhoneNumber' => $team['firstMemberPhoneNumber'] ?? null,
                      'firstMemberGender' => $team['firstMemberGender'] ?? null,
                      'secondMemberName' => $team['secondMemberName'] ?? null,
                      'secondMemberPhoneNumber' => $team['secondMemberPhoneNumber'] ?? null,
                      'secondMemberGender' => $team['secondMemberGender'] ?? null,
                      'studentCard_1' => $uploads[1]['student_card'] ?? null,
                      'studentCard_2' => $uploads[2]['student_card'] ?? null,
                      'studentCard_3' => $uploads[3]['student_card'] ?? null,
                      'igFollow_1' => $uploads[1]['ig_follow'] ?? null,
                      'igFollow_2' => $uploads[2]['ig_follow'] ?? null,
                      'igFollow_3' => $uploads[3]['ig_follow'] ?? null,
                      'twibbon_1' => $uploads[1]['twibbon'] ?? null,
                      'twibbon_2' => $uploads[2]['twibbon'] ?? null,
                      'twibbon_3' => $uploads[3]['twibbon'] ?? null,
                      '__submitted' => $is_reviewed,
                    ]) ?>;
    window.state = _state;
    const state = _state;
    const savedState = JSON.parse(JSON.stringify(state));
    const savedAttachments = {};
    document.querySelectorAll('[data-slot="attachment"]').forEach(function(a) {
      savedAttachments[a.dataset.inputName] = a.cloneNode(true);
    });

    window.__syncSavedState = function(submitted) {
      if (submitted) state.__submitted = true;
      for (var k in state) savedState[k] = state[k];
      updateTabs();
    };

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
      if (v == null || v === false) return false;
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
      if (n === 4) return tabDone(1) && tabDone(2) && tabDone(3) && filled('__submitted');
      return false;
    }

    function savedTabDone(n) {
      if (n === 1) return filledSaved('division') && filledSaved('name') && filledSaved('teamSchool');
      if (n === 2) return filledSaved('leaderName');
      if (n === 3) return filledSaved('igFollow_1') && filledSaved('twibbon_1');
      if (n === 4) return savedTabDone(1) && savedTabDone(2) && savedTabDone(3) && filledSaved('__submitted');
      return false;
    }

    function filledSaved(k) {
      var v = savedState[k];
      if (v == null || v === false) return false;
      return String(v).trim() !== '';
    }

    function isTabLocked(num) {
      if (num <= 1) return false;
      for (var i = 1; i < num; i++) {
        if (!savedTabDone(i)) return true;
      }
      return false;
    }

    function updateTabs() {
      for (var n = 1; n <= total; n++) {
        var link = document.querySelector('#tabList a[data-tab-num="' + n + '"]');
        if (!link) continue;
        var locked = isTabLocked(n);
        var done = savedTabDone(n);
        link.classList.toggle('pointer-events-none', locked);
        link.classList.toggle('opacity-40', locked);
        link.setAttribute('aria-disabled', locked ? 'true' : 'false');
        var numEl = link.querySelector('.tab-num');
        var checkEl = link.querySelector('.tab-check');
        if (numEl) numEl.classList.toggle('hidden', done);
        if (checkEl) checkEl.classList.toggle('hidden', !done);
      }
    }

    function resetTab(tabNum) {
      var panel = document.querySelector('.tab-panel[data-tab="' + tabNum + '"]');
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
      panel.querySelectorAll('[id^="err-"]').forEach(function(el) {
        el.classList.add('hidden');
      });
      panel.querySelectorAll('.border-red-500').forEach(function(el) {
        el.classList.remove('border-red-500');
      });
      panel.querySelectorAll('input[name^="delete_"], input[name^="move_member_"]').forEach(function(el) {
        el.remove();
      });
      panel.querySelectorAll('[data-slot="attachment"]').forEach(function(att) {
        var snap = savedAttachments[att.dataset.inputName];
        if (snap) att.replaceWith(snap.cloneNode(true));
      });
      panel.querySelectorAll('.member-group[data-optional]').forEach(function(g) {
        var mNum = parseInt(g.dataset.member);
        var prefix = mNum === 2 ? 'firstMember' : 'secondMember';
        var cardSaved = filledSaved('studentCard_' + mNum);
        var hasSaved = filledSaved(prefix + 'Name') || filledSaved(prefix + 'PhoneNumber') || cardSaved;
        g.dataset.activated = hasSaved ? 'true' : 'false';
        g.querySelector('.member-fields').classList.toggle('opacity-30', !hasSaved);
        g.querySelector('.member-fields').classList.toggle('pointer-events-none', !hasSaved);
        g.querySelector('.member-overlay').classList.toggle('hidden', hasSaved);
        var cancelBtn = g.querySelector('.cancel-member');
        if (cancelBtn) cancelBtn.classList.toggle('hidden', !hasSaved);
        g.querySelectorAll('.member-input').forEach(function(i) {
          if (hasSaved) i.setAttribute('required', '');
          else i.removeAttribute('required');
        });
        g.querySelectorAll('.member-file').forEach(function(f) {
          if (hasSaved && !cardSaved) f.setAttribute('required', '');
          else f.removeAttribute('required');
        });
        g.querySelectorAll('.member-radio').forEach(function(r) {
          if (hasSaved) r.setAttribute('required', '');
          else r.removeAttribute('required');
        });
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
      history.pushState({
        tab: num
      }, '', '/application/' + slugs[num - 1]);
      var activeLink = document.querySelector('#tabList a[data-tab-num="' + num + '"]');
      if (activeLink) activeLink.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest',
        inline: 'center'
      });
      current = num;
    }

    function validateScope(scope) {
      const fields = scope.querySelectorAll('[required]');
      let valid = true;
      const radioGroups = new Set();
      fields.forEach(f => {
        if (f.type === 'radio') {
          if (radioGroups.has(f.name)) return;
          radioGroups.add(f.name);
          const group = scope.querySelectorAll(`input[type="radio"][name="${f.name}"]`);
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
            errEl.classList.remove('hidden');
          }
          valid = false;
        } else {
          f.classList.remove('border-red-500');
          var att = f.closest('[data-slot="attachment"]');
          if (att) att.dataset.state = 'done';
          if (errEl) errEl.classList.add('hidden');
          const fmtErrId = f.dataset.formatError;
          if (fmtErrId && f.pattern) {
            var fmtErrEl = document.getElementById(fmtErrId);
            if (fmtErrEl && !new RegExp('^' + f.pattern + '$').test(f.value)) {
              f.classList.add('border-red-500');
              fmtErrEl.classList.remove('hidden');
              valid = false;
            } else if (fmtErrEl) {
              fmtErrEl.classList.add('hidden');
            }
          }
        }
      });
      return valid;
    }
    window.validateScope = validateScope;

    function validateTab(num) {
      const panel = document.querySelector(`.tab-panel[data-tab="${num}"]`);
      if (!panel) return true;
      return validateScope(panel);
    }

    document.getElementById('tabList').addEventListener('click', function(e) {
      var link = e.target.closest('a[data-tab-num]');
      if (link && link.getAttribute('aria-disabled') !== 'true') {
        e.preventDefault();
        var target = parseInt(link.dataset.tabNum);
        if (target !== current) {
          resetTab(current);
          resetTab(target);
        }
        goTo(target);
      }
    });

    document.getElementById('tabNav').addEventListener('click', function(e) {
      var prev = e.target.closest('[data-goto]');
      if (prev) {
        e.preventDefault();
        var target = parseInt(prev.dataset.goto);
        if (target !== current) {
          resetTab(current);
          resetTab(target);
        }
        goTo(target);
        return;
      }
      var next = e.target.closest('#nextTabSave, #nextTabSubmit');
      if (next) {
        if (!validateTab(current)) return;
        var panel = document.querySelector('.tab-panel[data-tab="' + current + '"]');
        if (next.id === 'nextTabSubmit' && current === total) {
          openDialog('submit-confirm-dialog');
          return;
        }
        if (!hasChanges()) {
          if (current < total) goTo(current + 1);
          return;
        }
        var form = panel && panel.querySelector('form');
        if (form) {
          var btn = form.querySelector('button[type="submit"]');
          if (btn) {
            btn.click();
            return;
          }
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

    function formatFileSize(bytes) {
      if (!bytes) return '';
      if (bytes < 1024) return bytes + ' B';
      if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
      return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    document.addEventListener('change', function(e) {
      if (!e.target.matches('[data-preview]')) return;
      var file = e.target.files && e.target.files[0];
      if (!file) return;
      var att = e.target.closest('[data-slot="attachment"]');
      if (!att) return;
      var maxSize = e.target.dataset.maxSize;
      if (maxSize && file.size > parseInt(maxSize)) {
        e.target.value = '';
        if (e.target.name) state[e.target.name] = null;
        att.dataset.state = 'error';
        var errEl = document.getElementById(e.target.dataset.error);
        if (errEl) {
          errEl.textContent = 'File terlalu besar. Maksimal ' + formatFileSize(parseInt(maxSize));
          errEl.classList.remove('hidden');
        }
        return;
      }
      att.dataset.state = 'done';
      var title = att.querySelector('[data-slot="attachment-title"]');
      if (title) title.textContent = file.name;
      var desc = att.querySelector('[data-slot="attachment-description"]');
      if (desc) desc.textContent = formatFileSize(file.size);

      var errId = e.target.dataset.error;
      if (errId) {
        var errEl = document.getElementById(errId);
        if (errEl) errEl.classList.add('hidden');
      }
      e.target.classList.remove('border-red-500');
      att.classList.remove('border-red-500');

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
      e.preventDefault();
      e.stopPropagation();
      var att = clearBtn.closest('[data-slot="attachment"]');
      if (!att) return;
      var input = att.querySelector('input[type="file"]');
      var inputName = att.dataset.inputName || (input ? input.name : '');
      if (input) {
        input.value = '';
        if (inputName && state.hasOwnProperty(inputName)) state[inputName] = null;
      }
      var origSrc = att.dataset.originalSrc;
      var origTitle = att.dataset.originalTitle;
      var origMedia = att.dataset.originalMedia;
      var title = att.querySelector('[data-slot="attachment-title"]');
      var desc = att.querySelector('[data-slot="attachment-description"]');
      var media = att.querySelector('[data-slot="attachment-media"]');
      if (origSrc && inputName) {
        var form = att.closest('form');
        if (form) {
          var del = document.createElement('input');
          del.type = 'hidden';
          del.name = 'delete_' + inputName;
          del.value = '1';
          form.appendChild(del);
        }
        if (input) input.setAttribute('required', '');
      }
      att.dataset.state = 'idle';
      if (title) title.textContent = att.dataset.idleTitle || origTitle || '';
      if (desc) desc.textContent = att.dataset.idleDescription || '';
      if (media && origMedia) media.innerHTML = origMedia;
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
          el.textContent = rb.value === 'FFR' || rb.value === 'LKTI' ? 'max 3 siswa' : 'max 2 siswa';
        });
      }
      radios.forEach(r => r.addEventListener('change', () => upd(r.value)));
      const checked = divCards.querySelector('input[name="division"]:checked');
      if (checked) upd(checked.value);
      hints.forEach(el => {
        const p = el.closest('.division-card');
        const rb = p?.querySelector('input[type="radio"]');
        if (!rb) return;
        el.textContent = rb.value === 'LF' || rb.value === 'PLC' ? 'max 2 siswa' : 'max 3 siswa';
      });
    }
  });
</script>