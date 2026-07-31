<?php

/** @var array|null $team */
/** @var string $csrf_token */

$DIVISION_LABELS = ['LF' => 'Line Follower', 'PLC' => 'Programmable Logic Controller', 'FFR' => 'Fire Fighting Robot', 'LKTI' => 'Lomba Karya Tulis Ilmiah', 'PROG' => 'Program'];
?>
<form action="<?= $team ? '/application/team/update' : '/application/team/register' ?>" method="POST" enctype="multipart/form-data" novalidate>
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
  <input type="hidden" name="next_tab" value="members">
  <input type="hidden" name="current_tab" value="team-register">

  <div class="space-y-6 gap-3">
    <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
      <label class="block text-sm font-semibold text-black mb-3">Pilih Divisi Lomba<span class="text-red-500">*</span></label>
      <div class="flex flex-wrap justify-center gap-3" id="divisionCards">
        <?php foreach ($DIVISION_LABELS as $k => $v): ?>
          <label class="division-card relative block w-[calc(50%-0.375rem)] rounded-xl border-2 border-gray-200 p-4 cursor-pointer hover:border-brand/50 hover:bg-brand/5 transition-all has-[:checked]:border-brand has-[:checked]:bg-brand/5 has-[:checked]:ring-2 has-[:checked]:ring-brand/20">
            <input type="radio" name="division" value="<?= $k ?>" class="hidden" <?= ($team['division'] ?? '') === $k ? 'checked' : '' ?> required data-error="err-division" onchange="document.querySelectorAll('.division-card').forEach(c=>c.classList.remove('border-red-500')); document.getElementById('err-division')?.classList.add('hidden')">
            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center mb-2 text-xs font-bold text-gray-600"><?= $k ?></div>
            <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($v) ?></p>
            <p class="text-xs text-gray-400 mt-0.5" data-division-hint></p>
          </label>
        <?php endforeach; ?>
      </div>
      <p id="err-division" class="text-xs text-red-500 mt-1 hidden">Pilih salah satu divisi</p>
      <p id="division_hint" class="mt-1 text-xs text-gray-500"></p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
      <label class="block text-sm font-semibold  mb-3">Informasi Tim</label>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium  mb-1.5">Nama Tim<span class="text-red-500">*</span></label>
          <input type="text" name="name" required value="<?= htmlspecialchars($team['name'] ?? '') ?>" data-error="err-nama-tim" placeholder="Masukkan nama tim"
            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
            oninput="this.classList.remove('border-red-500'); document.getElementById('err-nama-tim')?.classList.add('hidden')">
          <p id="err-nama-tim" class="text-xs text-red-500 mt-1 hidden">Nama tim wajib diisi</p>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5">Asal Sekolah<span class="text-red-500">*</span></label>
          <input type="text" name="teamSchool" required value="<?= htmlspecialchars($team['teamSchool'] ?? '') ?>" data-error="err-sekolah" placeholder="Masukkan nama sekolah"
            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all"
            oninput="this.classList.remove('border-red-500'); document.getElementById('err-sekolah')?.classList.add('hidden')">
          <p id="err-sekolah" class="text-xs text-red-500 mt-1 hidden">Asal sekolah wajib diisi</p>
        </div>
      </div>
    </div>
  </div>
</form>