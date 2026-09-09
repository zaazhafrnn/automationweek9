<?php

use App\Components\Attachment;
use App\Components\Icon;

/** @var array|null $team */
/** @var string $csrf_token */
/** @var array $uploads */

$members = [];
if ($team) {
  $members[] = ['num' => 1, 'name' => $team['leaderName'] ?? 'Anggota 1', 'active' => true];
  $two = $team['division'] === 'LF' || $team['division'] === 'PLC';
  $three = $team['division'] === 'FFR' || $team['division'] === 'LKTI' || $team['division'] === 'PROG';
  if ($two || $three) $members[] = ['num' => 2, 'name' => $team['firstMemberName'] ?? 'Anggota 2', 'active' => !empty($team['firstMemberName'])];
  if ($three) $members[] = ['num' => 3, 'name' => $team['secondMemberName'] ?? 'Anggota 3', 'active' => !empty($team['secondMemberName'])];
}
$UPLOAD_URL = '/uploads/teams/';
$twibbonLinks = [
  'PROG' => 'https://twb.nz/awixalogaritmaprogram',
  'LKTI' => 'https://twb.nz/awixlkti',
  'PLC'  => 'https://twb.nz/awixplc',
  'LF'   => 'https://twb.nz/awixlfm',
  'FFR'  => 'https://twb.nz/awixffr',
];
$twibbonLink = $twibbonLinks[$team['division']] ?? '#';
$TWIBBON_CAPTION = <<<'CAP'
🚀 I'M READY FOR AUTOMATION WEEK 9!🚀

“Success is not given, it is earned through hard work, creativity, and determination."✨

Halo, Automation Enthusiasts! 👋

Saya, [Nama Lengkap] dari [Asal Sekolah], siap menjadi bagian dari Automation Week 9!🤖⚡

Melalui kegiatan ini, saya ingin mengembangkan kemampuan, menambah pengalaman, memperluas relasi, serta mengasah kreativitas dan semangat kompetitif dalam menghadapi berbagai tantangan di dunia teknologi dan otomasi.

🏆 Kategori Lomba Automation Week 9:
🔥 Fire Fighting Roboboat
⚙️ Programmable Logic Controller
📚 Lomba Karya Tulis Ilmiah
💻 Algoritma Program
🤖 Line Follower Microcontroller

💬 Motivasi saya mengikuti Automation Week 9:
(Tuliskan motivasi di sini.)

Saya percaya bahwa setiap tantangan adalah kesempatan untuk belajar, berkembang, dan menjadi versi terbaik dari diri sendiri. Mari bersama-sama berkompetisi dengan penuh semangat, menjunjung sportivitas, dan menciptakan inovasi yang luar biasa! 🔥🤖

Let's Compete, Innovate, and Create! 🚀

AUTOMATION WEEK 9 — Unleash Your Potential! ⚡

#AutomationWeek
#AutomationWeek9
#PPNSSUCCES
#HIMATO
#SadhnaMahesvara
#AbreTuCorazon
#SatuPanjiOtomasi
CAP;
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

    <div class="space-y-6">
      <?php foreach ($members as $i => $m):
        $p = $m['num'];
        $isOptional = $p > 1;
        $disabled = !$m['active'];
        $existingIg = $uploads[$p]['ig_follow'] ?? null;
        $existingTwibbon = $uploads[$p]['twibbon'] ?? null;
        $originalIg = $uploads[$p]['original_ig_follow'] ?? null;
        $originalTwibbon = $uploads[$p]['original_twibbon'] ?? null;
        $igIcon = Icon::make()->name('instagram')->class('size-5 text-black');
        $twibbonIcon = Icon::make()->name('user-round')->class('size-5 text-black');
      ?>
        <div class="member-group relative bg-white rounded-xl border border-gray-200 p-4 sm:p-5" data-member="<?= $p ?>">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center text-xs font-bold text-brand"><?= $p ?></div>
            <div>
              <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($m['name']) ?></p>
              <p class="text-xs text-gray-400">Anggota <?= $p ?><?= $p === 1 ? ' (Ketua Tim)' : '' ?></p>
            </div>
          </div>
          <div class="member-fields <?= $disabled ? 'opacity-30 pointer-events-none' : '' ?>">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-1.5">Bukti Follow Instagram<span class="text-red-500">*</span> <a href="https://instagram.com/automationweek" target="_blank" data-tooltip="Buka Instagram Automation Week" class="text-sm text-brand font-semibold hover:underline ml-1">@automationweek</a></label>
                <?php
                $igRequired = !$disabled && !$existingIg;
                $igAttrs = ['accept' => 'image/*,.pdf,application/pdf', 'data-error' => 'err-sosmed-' . $p . '-ig', 'max-size' => 10 * 1024 * 1024];
                if ($igRequired) $igAttrs['required'] = true;
                if ($existingIg):
                ?>
                  <?= Attachment::make()

                    ->mediaVariant('image')
                    ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($existingIg) . '" class="w-full h-full object-cover">')
                    ->title($originalIg ?: basename($existingIg))
                    ->description('Bukti follow Instagram @automationweek')
                    ->clearable()
                    ->withPreview()
                    ->fileUrl($UPLOAD_URL . htmlspecialchars($existingIg))
                    ->originalMedia($igIcon)
                    ->originalSrc($UPLOAD_URL . htmlspecialchars($existingIg))
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
                <p id="err-sosmed-<?= $p ?>-ig" class="text-xs text-red-500 mt-1 hidden">Bukti follow wajib diupload</p>
              </div>
              <div>
                <div class="flex flex-wrap items-center gap-x-1 text-sm mb-1.5">
                  <label class="text-sm font-medium">Unggah Twibbon<span class="text-red-500">*</span></label>
                  <a href="<?= htmlspecialchars($twibbonLink) ?>" target="_blank" data-tooltip="Unduh template twibbon" class="text-brand font-semibold hover:underline">Unduh Twibbon</a>
                  <span class="">serta dengan caption</span>
                  <button type="button" data-copy-caption data-tooltip="Salin caption untuk postingan" class="text-brand font-semibold hover:underline cursor-pointer">Salin Caption</button>
                </div>
                <?php
                $twibbonRequired = !$disabled && !$existingTwibbon;
                $twibbonAttrs = ['accept' => 'image/*,.pdf,application/pdf', 'data-error' => 'err-sosmed-' . $p . '-twibbon', 'max-size' => 10 * 1024 * 1024];
                if ($twibbonRequired) $twibbonAttrs['required'] = true;
                if ($existingTwibbon):
                ?>
                  <?= Attachment::make()

                    ->mediaVariant('image')
                    ->media('<img src="' . $UPLOAD_URL . htmlspecialchars($existingTwibbon) . '" class="w-full h-full object-cover">')
                    ->title($originalTwibbon ?: basename($existingTwibbon))
                    ->description('Foto profil dengan twibbon')
                    ->clearable()
                    ->withPreview()
                    ->fileUrl($UPLOAD_URL . htmlspecialchars($existingTwibbon))
                    ->originalMedia($twibbonIcon)
                    ->originalSrc($UPLOAD_URL . htmlspecialchars($existingTwibbon))
                    ->idleTitle('Unggah Twibbon')
                    ->fileInput('twibbon_' . $p, $twibbonAttrs)
                    ->render() ?>
                <?php else: ?>
                  <?= Attachment::make()

                    ->media($twibbonIcon)
                    ->title('Unggah Twibbon')
                    ->description('Foto profil dengan twibbon')
                    ->clearable()
                    ->withPreview()
                    ->originalMedia($twibbonIcon)
                    ->fileInput('twibbon_' . $p, $twibbonAttrs)
                    ->render() ?>
                <?php endif; ?>
                <p id="err-sosmed-<?= $p ?>-twibbon" class="text-xs text-red-500 mt-1 hidden">Twibbon wajib diupload</p>
              </div>
            </div>
          </div>
          <?php if ($disabled): ?>
            <div class="member-overlay absolute inset-0 z-10 flex items-center justify-center rounded-xl cursor-default">
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </form>
<?php endif; ?>
<script>
  const TWIBBON_CAPTION = <?= json_encode(rtrim($TWIBBON_CAPTION), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-copy-caption]');
    if (!btn) return;
    const done = () => {
      btn.dataset.original = btn.dataset.original || btn.innerHTML;
      btn.innerHTML = 'Tersalin!';
      setTimeout(() => {
        btn.innerHTML = btn.dataset.original;
      }, 1500);
    };
    const fallback = () => {
      const ta = document.createElement('textarea');
      ta.value = TWIBBON_CAPTION;
      ta.style.position = 'fixed';
      ta.style.opacity = '0';
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      ta.remove();
      done();
    };
    if (navigator.clipboard) navigator.clipboard.writeText(TWIBBON_CAPTION).then(done).catch(fallback);
    else fallback();
  });
</script>