<?php

use App\Components\Icon;

/** @var string $csrf_token */
/** @var string $user_name */
/** @var array|null $team */
/** @var array|null $payment */
/** @var bool $is_reviewed */
/** @var array $uploads */

$division = $team['division'] ?? null;
$divisionUpper = strtoupper((string) $division);
$divisionNames = ['PROG' => 'Program'];
$divisionDisplay = $divisionNames[$divisionUpper] ?? $divisionUpper;
$upload1 = $uploads[1] ?? [];

$steps = [
  1 => ['label' => 'Registrasi', 'title' => 'Registrasi Tim', 'done' => (bool) $team],
  2 => ['label' => 'Data Anggota', 'title' => 'Data Anggota', 'done' => !empty($team['leaderName'])],
  3 => ['label' => 'Media Sosial', 'title' => 'Media Sosial', 'done' => !empty($upload1['ig_follow']) && !empty($upload1['twibbon'])],
  4 => ['label' => 'Review', 'title' => 'Review & Submit', 'done' => $is_reviewed],
  5 => ['label' => 'Pembayaran', 'title' => 'Pembayaran', 'done' => !empty($payment) && ($payment['status'] ?? '') === 'verified'],
];
$currentStep = null;
foreach ($steps as $n => $s) {
  if (!$s['done']) {
    $currentStep = $n;
    break;
  }
}
$nextLabel = $currentStep ? $steps[$currentStep]['title'] : null;
$applicationDone = $steps[1]['done'] && $steps[2]['done'] && $steps[3]['done'] && $steps[4]['done'];

$DIVISION_INFO = [
  'LKTI' => [
    'title' => 'LKTI (Lomba Karya Tulis Ilmiah)',
    'logo' => '/image/lkti_icon.png',
    'desc' => 'Lomba Karya Tulis Ilmiah merupakan sebuah perlombaan yang bertujuan untuk mengembangkan ide kreatif dan inovatif siswa dalam memecahkan masalah yang ada di lingkungan sekitar.',
    'guide_book' => 'https://drive.google.com/drive/folders/1LlB1h7dXbIcFxFiWV2BUf2RJfVFYDEZb',
    'timeline' => [
      ['title' => 'Pendaftaran & Pengumpulan Abstrak', 'date' => '24 Agustus 2026'],
      ['title' => 'Close Registration', 'date' => '1 Oktober 2026'],
      ['title' => 'Pengumuman Lolos Abstrak', 'date' => '3 Oktober 2026'],
      ['title' => 'Pengumpulan Full Paper', 'date' => '4 – 26 Oktober 2026'],
      ['title' => 'Pengumuman Finalis', 'date' => '2 November 2026'],
      ['title' => 'Technical Meeting', 'date' => '6 November 2026'],
      ['title' => 'Pembukaan & Persiapan Prototype', 'date' => '13 November 2026'],
      ['title' => 'Presentasi', 'date' => '14 November 2026'],
      ['title' => 'Pameran', 'date' => '15 November 2026'],
    ],
    'video' => null
  ],
  'FFR' => [
    'title' => 'FFR (Fire Fighting Roboboat)',
    'logo' => '/image/ffr_icon.png',
    'desc' => 'Fire Fighting Roboboat merupakan perlombaan kapal tanpa awak yang bergerak secara otomatis dan memiliki misi untuk memadamkan api.',
    'guide_book' => 'https://drive.google.com/drive/folders/1Xc2tKgDXoXcN0q_Y-34UnCOw-E6OmqM0',
    'timeline' => [
      ['title' => 'Pendaftaran', 'date' => '26 Agustus 2026'],
      ['title' => 'TM FFR & Test Track', 'date' => '13 November 2026'],
      ['title' => 'Penyisihan', 'date' => '14 November 2026'],
      ['title' => 'Semifinal & Final', 'date' => '15 November 2026'],
    ],
    'video' => 'https://www.youtube.com/embed/ZRfGoB4jJPw'
  ],
  'PLC' => [
    'title' => 'PLC (Programmable Logic Controller)',
    'logo' => '/image/plc_icon.png',
    'desc' => 'Programmable Logic Controller merupakan jenis lomba yang bertujuan untuk mengasah logika dan kemampuan siswa dalam bidang pemrograman PLC.',
    'guide_book' => 'https://drive.google.com/drive/folders/1QPSJh0ktutXskInEGvoYBCw69jRwd0KO',
    'timeline' => [
      ['title' => 'Open Registration', 'date' => '24 Agustus 2026'],
      ['title' => 'Close Registration', 'date' => '30 September 2026'],
      ['title' => 'Plan Contest', 'date' => '11 Oktober 2026'],
      ['title' => 'Pelatihan Sesi 1', 'date' => '17 Oktober 2026'],
      ['title' => 'Pelatihan Sesi 2', 'date' => '18 Oktober 2026'],
      ['title' => 'Technical Meeting', 'date' => '8 November 2026'],
      ['title' => 'Penyisihan 1 & 2', 'date' => '13 November 2026'],
      ['title' => 'Penyisihan 3', 'date' => '14 November 2026'],
      ['title' => 'Final', 'date' => '15 November 2026'],
    ],
    'video' => null
  ],
  'LF' => [
    'title' => 'Line Follower',
    'logo' => '/image/lf_icon.png',
    'desc' => 'Lomba Line Follower Mikrokontroler, Kompetisi robot berbasis mikrokontroler yang ditantang untuk mengikuti lintasan secara otomatis dengan kecepatan dan ketepatan tinggi.',
    'guide_book' => 'https://drive.google.com/drive/folders/19rjeLr4o4ZYeSvLECxF9etIvNlbaSsfq',
    'timeline' => [
      ['title' => 'Open Registration', 'date' => '17 Agustus 2026'],
      ['title' => 'Close Registration', 'date' => '2 November 2026'],
      ['title' => 'Technical Meeting', 'date' => '7 November 2026'],
      ['title' => 'Uji Lintasan / Track', 'date' => '13 November 2026'],
      ['title' => 'Penyisihan', 'date' => '14 November 2026'],
      ['title' => '16 Besar – Final', 'date' => '15 November 2026'],
    ],
    'video' => null
  ],
  'PROG' => [
    'title' => 'Algoritma Program',
    'logo' => '/image/program_icon.png',
    'desc' => 'Kompetisi pemrograman yang menguji kemampuan algoritma dan logika dalam menyelesaikan masalah secara efisien.',
    'guide_book' => '#',
    'timeline' => [
      ['title' => 'Open Registrasi', 'date' => '24 Agustus 2026'],
      ['title' => 'Close Registrasi', 'date' => '1 Oktober 2026'],
      ['title' => 'Technical Meeting', 'date' => '4 Oktober 2026'],
      ['title' => 'Pelatihan', 'date' => '13 November 2026'],
      ['title' => 'Penyisihan 1 & 2', 'date' => '14 Oktober 2026'],
      ['title' => 'Final', 'date' => '15 November 2026'],
    ],
    'video' => null
  ],
];
?>
<div class="min-h-screen bg-gray-50">
  <?php $current = 'home';
  include __DIR__ . '/components/nav-tabs.php'; ?>

  <div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-6 mb-6">
      <div class="flex flex-col md:flex-row md:items-center gap-6 md:gap-8">
        <div class="md:w-64 shrink-0">
          <h2 class="text-base md:text-xl font-bold text-gray-800">Progress Pendaftaran</h2>
          <p class="text-xs md:text-sm text-gray-500 mt-1">
            <?= $nextLabel ? 'Silahkan selesaikan <span class="font-semibold text-brand">' . htmlspecialchars($nextLabel) . '</span>' : 'Semua langkah selesai' ?>
          </p>
        </div>
        <div class="flex-1 min-w-0">
          <div class="overflow-x-auto pb-2 px-4 pt-2 sm:mx-0 sm:px-0">
            <div class="flex items-start sm:justify-between min-w-max sm:min-w-0">
              <?php foreach ($steps as $n => $s):
                $isDone = $s['done'];
                $isCurrent = $currentStep === $n;
                $innerCls = $isDone
                  ? 'bg-green-500 border-green-500 text-white'
                  : ($isCurrent ? 'bg-brand border-brand text-white' : 'bg-gray-100 border-gray-200 text-gray-400');
              ?>
                <?php if ($n > 1): ?>
                  <div class="w-10 sm:flex-[2.5_1_0%] shrink-0 h-0.5 mt-5 rounded-full <?= $steps[$n - 1]['done'] ? 'bg-green-500' : 'bg-gray-200' ?>"></div>
                <?php endif; ?>
                <div class="flex flex-col items-center gap-1.5 w-16 sm:flex-1 sm:w-auto shrink-0 px-0.5 sm:px-1">
                  <span data-active="<?= $isCurrent ? '1' : '' ?>" class="<?= $isCurrent ? 'relative' : '' ?> w-10 h-10 sm:w-11 sm:h-11 flex items-center justify-center shrink-0">
                    <?php if ($isCurrent): ?>
                      <span class="absolute inset-0 rounded-full ring-6 ring-brand/25 animate-pulse"></span>
                    <?php endif; ?>
                    <span class="<?= $innerCls ?> relative w-10 h-10 sm:w-11 sm:h-11 rounded-full border flex items-center justify-center">
                      <?php if ($isDone): ?>
                        <?= Icon::make()->name('check')->class('w-5 h-5') ?>
                      <?php else: ?>
                        <span class="text-sm font-bold"><?= $n ?></span>
                      <?php endif; ?>
                    </span>
                  </span>
                  <span class="text-xs font-semibold text-center leading-tight <?= $isDone || $isCurrent ? 'text-gray-800' : 'text-gray-400' ?>"><?= htmlspecialchars($s['label']) ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="mt-6 mb-4 md:mb-0 flex justify-center">
            <?php if (($payment['status'] ?? '') !== 'verified'): ?>
              <a href="<?= $applicationDone ? '/payments' : '/application' ?>" class="inline-flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white bg-brand rounded-xl hover:bg-red-800 transition-colors no-underline shadow-sm">
                <?= $applicationDone ? 'Lanjut ke Pembayaran' : 'Lanjutkan Pendaftaran' ?>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <?php if (!$team): ?>
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 max-w-lg mx-auto text-center">
        <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
          <?= Icon::make()->name('alert-circle')->class('w-7 h-7 text-brand') ?>
        </div>
        <h2 class="text-lg font-bold text-gray-800 mb-1">Belum Terdaftar Dalam Tim</h2>
        <p class="text-sm text-gray-500 mb-6">Kamu belum membuat atau mendaftarkan tim. Silakan daftarkan tim kamu terlebih dahulu untuk melihat informasi spesifik divisi.</p>
        <a href="/application/team-register" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand rounded-xl hover:bg-red-800 transition-colors no-underline shadow-sm">
          Daftarkan Tim Sekarang
          <?= Icon::make()->name('chevron-right')->class('w-4 h-4') ?>
        </a>
      </div>
    <?php else: ?>
      <?php $info = $DIVISION_INFO[$divisionUpper] ?? null; ?>
      <div class="flex flex-col gap-6 lg:flex-row">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex-1">
          <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
              <span class="text-xs uppercase font-bold tracking-wider text-gray-400">Tim Terdaftar</span>
              <h2 class="text-xl font-bold text-gray-800 mt-1"><?= htmlspecialchars($team['name']) ?></h2>
              <p class="text-sm text-gray-500 mt-1">Sekolah: <span class="font-semibold text-gray-700"><?= htmlspecialchars($team['teamSchool']) ?></span></p>
              <span class="inline-block mt-2 px-2.5 py-1 text-xs font-bold text-brand bg-brand/10 rounded-lg">Divisi <?= htmlspecialchars($divisionDisplay) ?></span>
            </div>
            <?php if ($info && !empty($info['guide_book'])): ?>
              <a href="<?= htmlspecialchars($info['guide_book']) ?>" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs font-bold text-white bg-brand hover:bg-red-800 rounded-xl transition-colors no-underline shrink-0 sm:self-center">
                <?= Icon::make()->name('download')->class('w-4 h-4') ?>
                Download Guide Book
              </a>
            <?php endif; ?>
          </div>
        </div>

        <?php if ($info): ?>
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex-1">
            <div class="flex items-center gap-4 mb-4">
              <img src="<?= htmlspecialchars($info['logo']) ?>" alt="<?= htmlspecialchars($info['title']) ?>" class="w-16 h-16 object-contain">
              <div>
                <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($info['title']) ?></h3>
                <p class="text-xs text-gray-400">Informasi & Penjelasan Lomba</p>
              </div>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed"><?= htmlspecialchars($info['desc']) ?></p>
          </div>
      </div>

      <?php if (!empty($info['video'])): ?>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm text-center mt-6">
          <h3 class="text-md font-bold text-gray-800 mb-4">Video Trial / Demo</h3>
          <div class="aspect-video w-full max-w-2xl mx-auto rounded-xl overflow-hidden shadow">
            <iframe class="w-full h-full" src="<?= htmlspecialchars($info['video']) ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          </div>
        </div>
      <?php endif; ?>

      <div class="bg-white rounded-2xl p-6 mt-6 border border-gray-100 shadow-sm">
        <h3 class="text-md font-bold text-gray-800 mb-4">Timeline Divisi <?= htmlspecialchars($divisionUpper) ?></h3>
        <div class="relative border-l-2 border-brand/20 ml-4 space-y-6">
          <?php
          $parseDate = function (string $d): ?int {
            $m = ['januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4, 'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8, 'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12];
            if (preg_match('/(\d{1,2})\s+(\w+)\s+(\d{4})/', $d, $p)) {
              $mon = $m[strtolower($p[2])] ?? null;
              return $mon ? mktime(0, 0, 0, $mon, (int)$p[1], (int)$p[3]) : null;
            }
            return null;
          };
          $today = strtotime('today');
          ?>
          <?php foreach ($info['timeline'] as $item):
            $ts = $parseDate($item['date']);
            $reached = $ts !== null && $ts <= $today;
          ?>
            <div class="relative pl-6">
              <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full border-4 border-white <?= $reached ? 'bg-green-500 animate-pulse ring-4 ring-green-500/25' : 'bg-brand' ?>"></div>
              <h4 class="text-sm font-bold <?= $reached ? 'text-green-700' : 'text-gray-800' ?>"><?= htmlspecialchars($item['title']) ?></h4>
              <p class="text-xs <?= $reached ? 'text-green-600' : 'text-gray-500' ?> mt-0.5"><?= htmlspecialchars($item['date']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <p class="text-sm text-gray-600">Informasi divisi belum tersedia.</p>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
<script>
  var active = document.querySelector('[data-active="1"]');
  if (active) {
    var scroller = active.closest('.overflow-x-auto');
    if (scroller) {
      scroller.style.scrollBehavior = 'smooth';
      var r = active.getBoundingClientRect();
      var sr = scroller.getBoundingClientRect();
      scroller.scrollLeft += r.left - sr.left - (sr.width / 2) + (r.width / 2);
    }
  }
</script>
</div>