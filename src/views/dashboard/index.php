<?php

/** @var string $csrf_token */
/** @var string $user_name */
/** @var array|null $team */
/** @var array|null $payment */
/** @var array|null $submission */

$steps = [
  1 => ['label' => 'Registrasi Tim', 'icon' => 'users'],
  2 => ['label' => 'Pembayaran', 'icon' => 'credit-card'],
  3 => ['label' => 'Submit Karya', 'icon' => 'upload'],
];

$currentStep = 0;
if (!$team) {
  $currentStep = 1;
} elseif (!$payment || $payment['status'] !== 'verified') {
  $currentStep = 2;
} else {
  $currentStep = 3;
}

function stepStatus(int $num, int $current): string
{
  if ($num < $current) return 'done';
  if ($num === $current) return 'active';
  return 'upcoming';
}

$DIVISION_LABELS = ['LF' => 'Line Follower', 'PLC' => 'Programmable Logic Controller', 'FFR' => 'Fire Fighting Robot', 'LKTI' => 'Lomba Karya Tulis Ilmiah'];
?>
<div class="max-w-5xl mx-auto">
  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
        Hi, <?= htmlspecialchars(explode(' ', $user_name ?? '')[0]) ?>!
      </h1>
      <p class="text-sm text-gray-500 mt-1">Ikuti langkah-langkah berikut untuk menyelesaikan pendaftaran.</p>
    </div>
    <form action="/logout" method="POST" class="m-0">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
      <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        Logout
      </button>
    </form>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6">
    <div class="flex items-start sm:items-center gap-0 sm:gap-2" id="stepProgress">
      <?php foreach ($steps as $num => $s): $ss = stepStatus($num, $currentStep); ?>
        <div class="flex-1 flex flex-col sm:flex-row items-center gap-2 sm:gap-3 <?= $ss === 'upcoming' ? 'opacity-40 pointer-events-none' : 'cursor-pointer' ?>" data-step="<?= $num ?>">
          <div class="flex items-center gap-2 shrink-0">
            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-xs font-bold transition-colors <?= match ($ss) {
                                                                                                                              'done' => 'bg-green-500 text-white',
                                                                                                                              'active' => 'bg-brand text-white',
                                                                                                                              default => 'bg-gray-200 text-gray-400'
                                                                                                                            } ?>">
              <?php if ($ss === 'done'): ?>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
              <?php else: ?>
                <?= $num ?>
              <?php endif; ?>
            </div>
            <span class="text-xs sm:text-sm font-medium transition-colors <?= $ss === 'active' ? 'text-brand' : ($ss === 'done' ? 'text-green-600' : 'text-gray-400') ?> hidden sm:inline">
              <?= htmlspecialchars($s['label']) ?>
            </span>
          </div>
          <?php if ($num < count($steps)): ?>
            <div class="h-0.5 w-full sm:flex-1 bg-gray-200 mt-1 sm:mt-0 <?= $ss === 'done' ? 'bg-green-400' : '' ?>"></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="flex justify-between mt-2 sm:hidden">
      <?php foreach ($steps as $num => $s): $ss = stepStatus($num, $currentStep); ?>
        <span class="text-[10px] font-medium <?= $ss === 'active' ? 'text-brand' : ($ss === 'done' ? 'text-green-600' : 'text-gray-400') ?>">
          <?= htmlspecialchars($s['label']) ?>
        </span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="space-y-6" id="stepContainer">
    <div class="step step-1 <?= $currentStep === 1 ? '' : 'hidden' ?>" data-step="1">
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
          <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold <?= $team ? 'bg-green-500 text-white' : 'bg-brand text-white' ?>">
            <?php if ($team): ?>
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
              </svg>
              <?php else: ?>1<?php endif; ?>
          </div>
          <span class="text-sm font-semibold text-gray-900">Registrasi Tim</span>
          <?php if ($team): ?>
            <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-green-100 text-green-700">Selesai</span>
          <?php endif; ?>
        </div>

        <div class="p-6">
          <?php if (!$team): ?>
            <?php $form_error = \App\Utils\Session::flash('team_register_error'); ?>
            <?php if ($form_error): ?>
              <div class="flex items-center gap-2 p-3 mb-6 bg-red-50 border border-red-200 rounded-xl">
                <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm text-red-800"><?= htmlspecialchars($form_error) ?></span>
              </div>
            <?php endif; ?>
            <form action="/dashboard/team/register" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Tim <span class="text-red-500">*</span></label>
                  <input type="text" name="name" required
                    class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">Asal Sekolah</label>
                  <input type="text" name="teamSchool"
                    class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
                </div>
              </div>

              <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-3">Divisi Lomba <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="divisionCards">
                  <?php $divs = ['LF' => 'Line Follower', 'PLC' => 'Programmable Logic Controller', 'FFR' => 'Fire Fighting Robot', 'LKTI' => 'Lomba Karya Tulis Ilmiah']; ?>
                  <?php foreach ($divs as $k => $v): ?>
                    <label class="division-card relative block rounded-xl border-2 border-gray-200 p-4 cursor-pointer hover:border-brand/50 hover:bg-brand/5 transition-all has-[:checked]:border-brand has-[:checked]:bg-brand/5 has-[:checked]:ring-2 has-[:checked]:ring-brand/20">
                      <input type="radio" name="division" value="<?= $k ?>" class="hidden" required>
                      <div>
                        <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center mb-2 text-xs font-bold text-gray-600"><?= $k ?></div>
                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($v) ?></p>
                        <p class="text-xs text-gray-400 mt-0.5" data-division-hint></p>
                      </div>
                    </label>
                  <?php endforeach; ?>
                </div>
                <p id="division_hint" class="mt-2 text-xs text-gray-500"></p>
              </div>

              <div class="space-y-8">
                <div>
                  <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center text-xs font-bold text-brand">1</div>
                    <div>
                      <p class="text-sm font-semibold text-gray-900">Anggota 1</p>
                      <p class="text-xs text-gray-400">Ketua Tim</p>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                      <input type="text" name="leaderName" required
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon / WA <span class="text-red-500">*</span></label>
                      <input type="text" name="leaderPhoneNumber" required
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
                    </div>
                  </div>
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kartu Pelajar / Mahasiswa <span class="text-red-500">*</span></label>
                    <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-brand/50 hover:bg-brand/5 transition-colors p-4 group cursor-pointer">
                      <input type="file" accept="image/*" required class="absolute inset-0 opacity-0 cursor-pointer">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand/10 transition-colors">
                          <svg class="w-5 h-5 text-gray-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0" />
                          </svg>
                        </div>
                        <div>
                          <p class="text-sm font-medium text-gray-700 group-hover:text-brand transition-colors">Upload Kartu Pelajar</p>
                          <p class="text-xs text-gray-400">Scan atau foto kartu pelajar/mahasiswa</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Bukti Follow Instagram <span class="text-red-500">*</span></label>
                      <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-brand/50 hover:bg-brand/5 transition-colors p-4 group cursor-pointer">
                        <input type="file" accept="image/*" required class="absolute inset-0 opacity-0 cursor-pointer">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand/10 transition-colors">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-700 group-hover:text-brand transition-colors">Screenshot Follow</p>
                            <p class="text-xs text-gray-400">Bukti sudah follow Instagram</p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload Twibbon <span class="text-red-500">*</span></label>
                      <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-brand/50 hover:bg-brand/5 transition-colors p-4 group cursor-pointer">
                        <input type="file" accept="image/*" required class="absolute inset-0 opacity-0 cursor-pointer">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand/10 transition-colors">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-700 group-hover:text-brand transition-colors">Upload Twibbon</p>
                            <p class="text-xs text-gray-400">Foto dengan twibbon</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <hr class="border-gray-100">

                <div id="member2_section">
                  <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">2</div>
                    <div>
                      <p class="text-sm font-semibold text-gray-900">Anggota 2</p>
                      <p class="text-xs text-gray-400">Opsional — tergantung divisi</p>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                      <input type="text" name="firstMemberName"
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon / WA</label>
                      <input type="text" name="firstMemberPhoneNumber"
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
                    </div>
                  </div>
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kartu Pelajar / Mahasiswa</label>
                    <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-brand/50 hover:bg-brand/5 transition-colors p-4 group cursor-pointer">
                      <input type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand/10 transition-colors">
                          <svg class="w-5 h-5 text-gray-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0" />
                          </svg>
                        </div>
                        <div>
                          <p class="text-sm font-medium text-gray-700 group-hover:text-brand transition-colors">Upload Kartu Pelajar</p>
                          <p class="text-xs text-gray-400">Scan atau foto kartu pelajar/mahasiswa</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Bukti Follow Instagram</label>
                      <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-brand/50 hover:bg-brand/5 transition-colors p-4 group cursor-pointer">
                        <input type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand/10 transition-colors">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-700 group-hover:text-brand transition-colors">Screenshot Follow</p>
                            <p class="text-xs text-gray-400">Bukti sudah follow Instagram</p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload Twibbon</label>
                      <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-brand/50 hover:bg-brand/5 transition-colors p-4 group cursor-pointer">
                        <input type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand/10 transition-colors">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-700 group-hover:text-brand transition-colors">Upload Twibbon</p>
                            <p class="text-xs text-gray-400">Foto dengan twibbon</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <hr class="border-gray-100">

                <div id="member3_section" class="hidden">
                  <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">3</div>
                    <div>
                      <p class="text-sm font-semibold text-gray-900">Anggota 3</p>
                      <p class="text-xs text-gray-400">Opsional — tergantung divisi</p>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                      <input type="text" name="secondMemberName" id="secondMemberName"
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon / WA</label>
                      <input type="text" name="secondMemberPhoneNumber"
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
                    </div>
                  </div>
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kartu Pelajar / Mahasiswa</label>
                    <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-brand/50 hover:bg-brand/5 transition-colors p-4 group cursor-pointer">
                      <input type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand/10 transition-colors">
                          <svg class="w-5 h-5 text-gray-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0" />
                          </svg>
                        </div>
                        <div>
                          <p class="text-sm font-medium text-gray-700 group-hover:text-brand transition-colors">Upload Kartu Pelajar</p>
                          <p class="text-xs text-gray-400">Scan atau foto kartu pelajar/mahasiswa</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Bukti Follow Instagram</label>
                      <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-brand/50 hover:bg-brand/5 transition-colors p-4 group cursor-pointer">
                        <input type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand/10 transition-colors">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-700 group-hover:text-brand transition-colors">Screenshot Follow</p>
                            <p class="text-xs text-gray-400">Bukti sudah follow Instagram</p>
                          </div>
                        </div>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload Twibbon</label>
                        <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-brand/50 hover:bg-brand/5 transition-colors p-4 group cursor-pointer">
                          <input type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                          <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand/10 transition-colors">
                              <svg class="w-5 h-5 text-gray-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                              </svg>
                            </div>
                            <div>
                              <p class="text-sm font-medium text-gray-700 group-hover:text-brand transition-colors">Upload Twibbon</p>
                              <p class="text-xs text-gray-400">Foto dengan twibbon</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand text-white text-sm font-semibold rounded-xl hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-brand/30 transition-all active:scale-[0.98] ontouchstart="">
                  Daftar Tim
                  <svg class=" w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </button>
                  </div>
            </form>

          <?php else: ?>
            <?php $update_error = \App\Utils\Session::flash('team_update_error'); ?>
            <?php $update_success = \App\Utils\Session::flash('team_update_success'); ?>
            <?php if ($update_error): ?>
              <div class="flex items-center gap-2 p-3 mb-6 bg-red-50 border border-red-200 rounded-xl">
                <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm text-red-800"><?= htmlspecialchars($update_error) ?></span>
              </div>
            <?php endif; ?>
            <?php if ($update_success): ?>
              <div class="flex items-center gap-2 p-3 mb-6 bg-green-50 border border-green-200 rounded-xl">
                <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm text-green-800"><?= htmlspecialchars($update_success) ?></span>
              </div>
            <?php endif; ?>
            <form action="/dashboard/team/update" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Tim <span class="text-red-500">*</span></label>
                  <div class="relative">
                    <input type="text" name="name" value="<?= htmlspecialchars($team['name']) ?>"
                      class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-500 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
                      readonly data-editable>
                    <button type="button" class="edit-toggle absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-300 hover:text-brand transition-colors" tabindex="-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                      </svg>
                    </button>
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">Asal Sekolah</label>
                  <div class="relative">
                    <input type="text" name="teamSchool" value="<?= htmlspecialchars($team['teamSchool'] ?? '') ?>"
                      class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-500 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
                      readonly data-editable>
                    <button type="button" class="edit-toggle absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-300 hover:text-brand transition-colors" tabindex="-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Divisi Lomba</label>
                <div class="flex flex-wrap gap-3">
                  <div class="flex items-center gap-3 px-5 py-3.5 rounded-xl border-2 border-brand/30 bg-brand/5">
                    <div class="w-10 h-10 rounded-lg bg-brand/10 flex items-center justify-center">
                      <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($DIVISION_LABELS[$team['division']] ?? $team['division']) ?></p>
                      <p class="text-xs text-gray-400"><?= htmlspecialchars($team['division']) ?></p>
                    </div>
                    <svg class="w-5 h-5 text-brand shrink-0" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                    </svg>
                  </div>
                </div>
              </div>

              <?php
              $members = [];
              $members[] = ['num' => 1, 'label' => 'Anggota 1', 'role' => 'Ketua Tim', 'nameKey' => 'leaderName', 'phoneKey' => 'leaderPhoneNumber'];
              $members[] = ['num' => 2, 'label' => 'Anggota 2', 'role' => '', 'nameKey' => 'firstMemberName', 'phoneKey' => 'firstMemberPhoneNumber'];
              $hasM3 = !empty($team['secondMemberName']) || !empty($team['secondMemberPhoneNumber']);
              if ($hasM3) {
                $members[] = ['num' => 3, 'label' => 'Anggota 3', 'role' => '', 'nameKey' => 'secondMemberName', 'phoneKey' => 'secondMemberPhoneNumber'];
              }
              ?>
              <div class="space-y-8">
                <?php foreach ($members as $mi => $m):
                  $name = $team[$m['nameKey']] ?? '';
                  $phone = $team[$m['phoneKey']] ?? '';
                ?>
                  <div>
                    <div class="flex items-center gap-3 mb-4">
                      <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center text-xs font-bold text-brand"><?= $m['num'] ?></div>
                      <div>
                        <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($m['label']) ?></p>
                        <?php if ($m['role']): ?><p class="text-xs text-gray-400"><?= htmlspecialchars($m['role']) ?></p><?php endif; ?>
                      </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <div class="relative">
                          <input type="text" name="<?= $m['nameKey'] ?>" value="<?= htmlspecialchars($name) ?>"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-500 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
                            readonly data-editable>
                          <button type="button" class="edit-toggle absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-300 hover:text-brand transition-colors" tabindex="-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                          </button>
                        </div>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon / WA <span class="text-red-500">*</span></label>
                        <div class="relative">
                          <input type="text" name="<?= $m['phoneKey'] ?>" value="<?= htmlspecialchars($phone) ?>"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-500 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
                            readonly data-editable>
                          <button type="button" class="edit-toggle absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-300 hover:text-brand transition-colors" tabindex="-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                          </button>
                        </div>
                      </div>
                    </div>

                    <div class="mb-4">
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Kartu Pelajar<span class="text-red-500">*</span></label>
                      <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-brand/50 hover:bg-brand/5 transition-colors p-4 group cursor-pointer">
                        <input type="file" accept="image/*" required class="absolute inset-0 opacity-0 cursor-pointer">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand/10 transition-colors">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0" />
                            </svg>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-700 group-hover:text-brand transition-colors">Upload Kartu Pelajar</p>
                            <p class="text-xs text-gray-400">Scan atau foto kartu pelajar</p>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Bukti Follow Instagram <span class="text-red-500">*</span></label>
                        <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-brand/50 hover:bg-brand/5 transition-colors p-4 group cursor-pointer">
                          <input type="file" accept="image/*" required class="absolute inset-0 opacity-0 cursor-pointer">
                          <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand/10 transition-colors">
                              <svg class="w-5 h-5 text-gray-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                              </svg>
                            </div>
                            <div>
                              <p class="text-sm font-medium text-gray-700 group-hover:text-brand transition-colors">Screenshot Follow</p>
                              <p class="text-xs text-gray-400">Bukti sudah follow Instagram</p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload Twibbon <span class="text-red-500">*</span></label>
                        <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-brand/50 hover:bg-brand/5 transition-colors p-4 group cursor-pointer">
                          <input type="file" accept="image/*" required class="absolute inset-0 opacity-0 cursor-pointer">
                          <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand/10 transition-colors">
                              <svg class="w-5 h-5 text-gray-400 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                              </svg>
                            </div>
                            <div>
                              <p class="text-sm font-medium text-gray-700 group-hover:text-brand transition-colors">Upload Twibbon</p>
                              <p class="text-xs text-gray-400">Foto dengan twibbon</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php if ($mi < count($members) - 1): ?>
                    <hr class="border-gray-100">
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>

              <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" id="resetEditBtn" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all hidden ontouchstart="">
                  Reset
                </button>
                <button type=" submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand text-white text-sm font-semibold rounded-xl hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-brand/30 transition-all active:scale-[0.98] ontouchstart="">
                  <svg class=" w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                  Simpan Perubahan
                </button>
              </div>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="step step-2 <?= $currentStep === 2 ? '' : 'hidden' ?>" data-step="2">
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold <?= $payment && $payment['status'] === 'verified' ? 'bg-green-500 text-white' : 'bg-brand text-white' ?>">
              <?php if ($payment && $payment['status'] === 'verified'): ?>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                <?php else: ?>2<?php endif; ?>
            </div>
            <span class="text-sm font-semibold text-gray-900">Pembayaran</span>
            <?php if ($payment): $st = $payment['status']; ?>
              <span class="text-xs px-2 py-0.5 rounded-full font-medium <?= match ($st) {
                                                                          'verified' => 'bg-green-100 text-green-700',
                                                                          'pending' => 'bg-yellow-100 text-yellow-700',
                                                                          'rejected' => 'bg-red-100 text-red-700',
                                                                          default => 'bg-gray-100 text-gray-600'
                                                                        } ?>">
                <?= match ($st) {
                  'verified' => 'Lunas',
                  'pending' => 'Menunggu',
                  'rejected' => 'Ditolak',
                  default => ''
                } ?>
              </span>
            <?php endif; ?>
          </div>
        </div>

        <div class="p-6">
          <?php if (!$payment): ?>
            <?php $pay_error = \App\Utils\Session::flash('payment_error'); ?>
            <?php if ($pay_error): ?>
              <div class="flex items-center gap-2 p-3 mb-6 bg-red-50 border border-red-200 rounded-xl">
                <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm text-red-800"><?= htmlspecialchars($pay_error) ?></span>
              </div>
            <?php endif; ?>
            <form action="/dashboard/payment" method="POST" enctype="multipart/form-data" class="space-y-6">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Transfer <span class="text-red-500">*</span></label>
                <div class="relative overflow-hidden rounded-xl border-2 border-dashed border-gray-300 hover:border-brand transition cursor-pointer" id="paymentDropzone">
                  <input id="proofImage" name="proofImage" type="file" class="hidden" accept="image/*" required>
                  <div id="dropzonePlaceholder" class="flex flex-col items-center justify-center px-6 py-10">
                    <svg class="w-12 h-12 text-gray-400 mb-3" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                      <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="text-sm text-gray-600">
                      <span class="font-medium text-brand">Pilih file</span> atau seret ke sini
                    </p>
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF, WebP — maks 2MB</p>
                  </div>
                  <div id="dropzonePreview" class="hidden relative">
                    <img id="previewImage" class="w-full max-h-64 object-contain bg-gray-50">
                    <div class="absolute inset-0 bg-black/0 hover:bg-black/20 transition-colors flex items-center justify-center">
                      <span class="text-white font-semibold text-sm bg-black/60 px-4 py-2 rounded-xl opacity-0 hover:opacity-100 transition-opacity">Klik untuk ganti</span>
                    </div>
                  </div>
                </div>
              </div>
              <button type="submit" class="w-full flex justify-center items-center gap-2 py-2.5 px-4 bg-brand text-white text-sm font-semibold rounded-xl hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-brand/30 transition-all active:scale-[0.98] ontouchstart="">
                <svg class=" w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                Upload Bukti Pembayaran
              </button>
            </form>

          <?php elseif ($payment['status'] === 'pending'): ?>
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="flex-1 min-w-0">
                <p class="text-sm text-yellow-800 font-medium">Pembayaran menunggu verifikasi</p>
                <p class="text-xs text-yellow-600 mt-1">Admin akan memeriksa bukti pembayaran kamu. Kembali lagi nanti.</p>
                <?php if (!empty($payment['proofImage'])): ?>
                  <img src="/uploads/payments/<?= htmlspecialchars($payment['proofImage']) ?>" class="mt-3 max-h-48 rounded-xl border border-gray-200 object-contain bg-gray-50">
                <?php endif; ?>
              </div>
            </div>

          <?php elseif ($payment['status'] === 'verified'): ?>
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div>
                <p class="text-sm text-green-800 font-medium">Pembayaran telah diverifikasi!</p>
                <p class="text-xs text-green-600 mt-0.5">Pendaftaran tim kamu sudah lengkap. Lanjut ke submit karya.</p>
                <?php if (!empty($payment['proofImage'])): ?>
                  <img src="/uploads/payments/<?= htmlspecialchars($payment['proofImage']) ?>" class="mt-3 max-h-48 rounded-xl border border-gray-200 object-contain bg-gray-50">
                <?php endif; ?>
              </div>
            </div>

          <?php elseif ($payment['status'] === 'rejected'): ?>
            <div class="flex items-start gap-3 mb-5">
              <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="flex-1 min-w-0">
                <p class="text-sm text-red-800 font-medium">Pembayaran ditolak</p>
                <?php if (!empty($payment['note'])): ?><p class="text-sm text-red-700 mt-1">Alasan: <?= htmlspecialchars($payment['note']) ?></p><?php endif; ?>
              </div>
            </div>
            <form action="/dashboard/payment" method="POST" enctype="multipart/form-data" class="space-y-5 pt-4 border-t border-gray-100">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Ulang</label>
                <div class="relative overflow-hidden rounded-xl border-2 border-dashed border-gray-300 hover:border-brand transition cursor-pointer" id="paymentDropzone2">
                  <input id="proofImage2" name="proofImage" type="file" class="hidden" accept="image/*" required>
                  <div id="dropzonePlaceholder2" class="flex flex-col items-center justify-center px-6 py-8">
                    <p class="text-sm text-gray-600"><span class="font-medium text-brand">Pilih file</span> atau seret ke sini</p>
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF, WebP — maks 2MB</p>
                  </div>
                  <div id="dropzonePreview2" class="hidden relative">
                    <img id="previewImage2" class="w-full max-h-48 object-contain bg-gray-50">
                    <div class="absolute inset-0 bg-black/0 hover:bg-black/20 transition-colors flex items-center justify-center">
                      <span class="text-white font-semibold text-sm bg-black/60 px-4 py-2 rounded-xl opacity-0 hover:opacity-100 transition-opacity">Klik untuk ganti</span>
                    </div>
                  </div>
                </div>
              </div>
              <button type="submit" class="w-full flex justify-center items-center gap-2 py-2.5 px-4 bg-brand text-white text-sm font-semibold rounded-xl hover:bg-brand/90 transition-all active:scale-[0.98] ontouchstart="">
                Upload Ulang
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class=" step step-3 <?= $currentStep === 3 ? '' : 'hidden' ?>" data-step="3">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                  <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold <?= $submission ? 'bg-green-500 text-white' : ($payment && $payment['status'] === 'verified' ? 'bg-brand text-white' : 'bg-gray-200 text-gray-400') ?>">
                        <?php if ($submission): ?>
                          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                          </svg>
                          <?php else: ?>3<?php endif; ?>
                      </div>
                      <span class="text-sm font-semibold <?= $payment && $payment['status'] === 'verified' ? 'text-gray-900' : 'text-gray-400' ?>">Submit Karya</span>
                      <?php if ($submission): ?>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-green-100 text-green-700">Sudah diupload</span>
                      <?php endif; ?>
                    </div>
                  </div>

                  <!-- body -->
                  <?php if (!$payment || $payment['status'] !== 'verified'): ?>
                    <div class="flex items-center gap-3 opacity-50">
                      <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                      </svg>
                      <p class="text-sm text-gray-400">Selesaikan pembayaran terlebih dahulu untuk mengakses bagian ini.</p>
                    </div>
                  <?php else:
                    $submission_error = \App\Utils\Session::flash('submission_error');
                    $submission_success = \App\Utils\Session::flash('submission_success');
                  ?>
                    <?php if ($submission_success): ?>
                      <div class="flex items-center gap-2 p-3 mb-4 bg-green-50 border border-green-200 rounded-xl">
                        <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm text-green-800"><?= htmlspecialchars($submission_success) ?></span>
                      </div>
                    <?php endif; ?>
                    <?php if ($submission_error): ?>
                      <div class="flex items-center gap-2 p-3 mb-4 bg-red-50 border border-red-200 rounded-xl">
                        <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm text-red-800"><?= htmlspecialchars($submission_error) ?></span>
                      </div>
                    <?php endif; ?>
                    <?php if ($submission): ?>
                      <div class="flex items-center gap-2 p-3 mb-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm text-blue-800">
                          Karya sudah diupload:
                          <?php if ($submission['type'] === 'youtube_link'): ?>
                            <a href="<?= htmlspecialchars($submission['value']) ?>" target="_blank" class="underline font-medium"><?= htmlspecialchars($submission['value']) ?></a>
                          <?php else: ?>
                            <?= htmlspecialchars($submission['value']) ?>
                          <?php endif; ?>
                        </span>
                      </div>
                    <?php endif; ?>
                    <form action="/dashboard/submission" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                      <?php if ($team['division'] === 'FFR'): ?>
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-1.5">Link YouTube <span class="text-red-500">*</span></label>
                          <input type="url" name="youtube_link" required
                            value="<?= $submission && $submission['type'] === 'youtube_link' ? htmlspecialchars($submission['value']) : '' ?>"
                            placeholder="https://www.youtube.com/watch?v=..."
                            class="peer w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
                          <p class="mt-1.5 text-xs text-gray-500">Upload video robot ke YouTube, tempelkan linknya di sini.</p>
                        </div>
                      <?php else: ?>
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-1.5">File Karya <span class="text-red-500">*</span></label>
                          <input type="file" name="submission_file" required
                            class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand file:text-white hover:file:bg-brand/90 transition-all cursor-pointer">
                          <p class="mt-1.5 text-xs text-gray-500">Maksimal 10MB.</p>
                        </div>
                      <?php endif; ?>
                      <button type="submit"
                        class="mt-5 w-full flex justify-center items-center gap-2 py-2.5 px-4 bg-brand text-white text-sm font-semibold rounded-xl hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-brand/30 transition-all active:scale-[0.98] ontouchstart="">
                <svg class=" w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        <?= $submission ? 'Update Karya' : 'Upload Karya' ?>
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
        </div>
      </div>

      <div class="flex justify-between items-center" id="stepNav">
        <button id="prevBtn" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-800 transition-all disabled:opacity-30 disabled:pointer-events-none ontouchstart="">
        <svg class=" w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
          Sebelumnya
        </button>
        <div class="flex items-center gap-2 text-xs text-gray-400">
          <span id="stepIndicator">Langkah <?= $currentStep ?> dari 3</span>
        </div>
        <button id="nextBtn" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand rounded-xl hover:bg-brand/90 transition-all disabled:opacity-30 disabled:pointer-events-none ontouchstart="">
        Selanjutnya
        <svg class=" w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {

      const divisionCards = document.getElementById('divisionCards');
      const m2 = document.getElementById('member2_section');
      const m3 = document.getElementById('member3_section');
      const hint = document.getElementById('division_hint');
      const m2name = document.getElementById('secondMemberName');

      if (divisionCards) {
        const radios = divisionCards.querySelectorAll('input[name="division"]');
        const hints = divisionCards.querySelectorAll('[data-division-hint]');

        function updateDivisions(val) {
          const two = val === 'LF' || val === 'PLC';
          const three = val === 'FFR' || val === 'LKTI';
          m2?.classList.toggle('hidden', !two && !three);
          m3?.classList.toggle('hidden', !three);
          if (hint) {
            hint.textContent = two ? '* Maksimal 2 anggota (1 Ketua + 1 Anggota)' : (three ? '* Maksimal 3 anggota (1 Ketua + 2 Anggota)' : '');
            hint.classList.toggle('hidden', !val);
          }
          hints.forEach(el => {
            const p = el.closest('.division-card');
            const rb = p?.querySelector('input[type="radio"]');
            if (!rb) return;
            el.textContent = rb.value === 'LF' || rb.value === 'PLC' ? 'max 2 org' : 'max 3 org';
          });
        }

        radios.forEach(r => r.addEventListener('change', () => updateDivisions(r.value)));
        const checked = divisionCards.querySelector('input[name="division"]:checked');
        if (checked) updateDivisions(checked.value);
        hints.forEach(el => {
          const p = el.closest('.division-card');
          const rb = p?.querySelector('input[type="radio"]');
          if (!rb) return;
          el.textContent = rb.value === 'LF' || rb.value === 'PLC' ? 'max 2 org' : 'max 3 org';
        });
      }

      const toggleBtns = document.querySelectorAll('.edit-toggle');
      let anyEdited = false;
      toggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const container = this.closest('.relative');
          const input = container?.querySelector('input[data-editable]');
          if (!input) return;
          const wasReadonly = input.hasAttribute('readonly');
          if (wasReadonly) {
            input.removeAttribute('readonly');
            input.className = 'peer w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all';
            this.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
            anyEdited = true;
          } else {
            input.setAttribute('readonly', '');
            input.className = 'peer w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-500 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all readonly:bg-gray-50 readonly:text-gray-500';
            this.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>';
          }
          const resetBtn = document.getElementById('resetEditBtn');
          if (resetBtn) resetBtn.classList.toggle('hidden', !anyEdited);
        });
      });

      const resetBtn = document.getElementById('resetEditBtn');
      resetBtn?.addEventListener('click', function() {
        document.querySelectorAll('input[data-editable][readonly]').forEach(input => {});
        toggleBtns.forEach(btn => {
          const container = btn.closest('.relative');
          const input = container?.querySelector('input[data-editable]');
          if (input && !input.hasAttribute('readonly')) {
            input.setAttribute('readonly', '');
            input.className = 'peer w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-500 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all readonly:bg-gray-50 readonly:text-gray-500';
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>';
          }
        });
        anyEdited = false;
        this.classList.add('hidden');
      });

      function setupDropzone(dropzoneId, inputId, placeholderId, previewId, imgId) {
        const dz = document.getElementById(dropzoneId);
        const input = document.getElementById(inputId);
        const placeholder = document.getElementById(placeholderId);
        const preview = document.getElementById(previewId);
        const img = document.getElementById(imgId);
        if (!dz || !input) return;

        function showPreview(file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            img.src = e.target.result;
            placeholder?.classList.add('hidden');
            preview?.classList.remove('hidden');
            dz.classList.remove('border-dashed', 'border-gray-300', 'hover:border-brand');
            dz.classList.add('border-solid', 'border-gray-200');
          };
          reader.readAsDataURL(file);
        }

        function resetDropzone() {
          placeholder?.classList.remove('hidden');
          preview?.classList.add('hidden');
          dz.classList.add('border-dashed', 'border-gray-300', 'hover:border-brand');
          dz.classList.remove('border-solid', 'border-gray-200');
          input.value = '';
        }

        input.addEventListener('change', function() {
          if (this.files.length > 0) showPreview(this.files[0]);
          else resetDropzone();
        });

        preview?.addEventListener('click', function(e) {
          if (e.target.tagName !== 'IMG' && !e.target.closest('img')) return;
          input.click();
        });

        dz.addEventListener('click', function(e) {
          if (e.target.tagName === 'IMG' || e.target.closest('img')) return;
          input.click();
        });

        dz.addEventListener('dragover', function(e) {
          e.preventDefault();
          this.classList.add('border-brand', 'bg-brand/5');
        });
        dz.addEventListener('dragleave', function() {
          this.classList.remove('border-brand', 'bg-brand/5');
        });
        dz.addEventListener('drop', function(e) {
          e.preventDefault();
          this.classList.remove('border-brand', 'bg-brand/5');
          if (e.dataTransfer.files.length > 0) {
            input.files = e.dataTransfer.files;
            showPreview(e.dataTransfer.files[0]);
          }
        });
      }

      setupDropzone('paymentDropzone', 'proofImage', 'dropzonePlaceholder', 'dropzonePreview', 'previewImage');
      setupDropzone('paymentDropzone2', 'proofImage2', 'dropzonePlaceholder2', 'dropzonePreview2', 'previewImage2');

      /* ── Step Navigation ── */
      const container = document.getElementById('stepContainer');
      const steps = container?.querySelectorAll('.step');
      const prevBtn = document.getElementById('prevBtn');
      const nextBtn = document.getElementById('nextBtn');
      const stepIndicator = document.getElementById('stepIndicator');
      const progressSteps = document.querySelectorAll('#stepProgress [data-step]');
      if (!steps || !steps.length) return;

      let current = <?= $currentStep ?>;
      const total = <?= count($steps) ?>;

      function goToStep(num) {
        if (num < 1 || num > total) return;
        steps.forEach(s => s.classList.add('hidden'));
        const target = container.querySelector(`.step-${num}`);
        if (target) target.classList.remove('hidden');
        current = num;
        prevBtn.disabled = current <= 1;
        nextBtn.disabled = current >= total;
        stepIndicator.textContent = `Langkah ${current} dari ${total}`;
        progressSteps.forEach(el => {
          const stepNum = parseInt(el.dataset.step);
          const circle = el.querySelector('.w-8');
          if (!circle) return;
          if (stepNum < current) {
            circle.className = 'w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-xs font-bold bg-green-500 text-white';
            circle.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
          } else if (stepNum === current) {
            circle.className = 'w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-xs font-bold bg-brand text-white';
            circle.textContent = stepNum;
          } else {
            circle.className = 'w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-xs font-bold bg-gray-200 text-gray-400';
            circle.textContent = stepNum;
          }
        });
      }

      prevBtn?.addEventListener('click', () => goToStep(current - 1));
      nextBtn?.addEventListener('click', () => goToStep(current + 1));
      progressSteps.forEach(el => {
        el.addEventListener('click', function() {
          goToStep(parseInt(this.dataset.step));
        });
      });
    });
  </script>