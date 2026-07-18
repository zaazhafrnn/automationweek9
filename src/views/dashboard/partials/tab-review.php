<?php
/** @var array|null $team */
/** @var array|null $payment */
/** @var array|null $submission */
/** @var string $csrf_token */
$DIVISION_LABELS = ['LF' => 'Line Follower', 'PLC' => 'Programmable Logic Controller', 'FFR' => 'Fire Fighting Robot', 'LKTI' => 'Lomba Karya Tulis Ilmiah'];
$submission_error = \App\Utils\Session::flash('submission_error');
$submission_success = \App\Utils\Session::flash('submission_success');
?>
<?php if (!$payment || $payment['status'] !== 'verified'): ?>
  <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-200 rounded-xl">
    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    <p class="text-sm text-gray-500">Selesaikan pembayaran untuk mengakses bagian ini.</p>
  </div>
<?php else: ?>
  <?php if ($submission_success): ?>
    <div class="flex items-center gap-2 p-3 mb-4 bg-green-50 border border-green-200 rounded-xl">
      <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <span class="text-sm text-green-800"><?= htmlspecialchars($submission_success) ?></span>
    </div>
  <?php endif; ?>
  <?php if ($submission_error): ?>
    <div class="flex items-center gap-2 p-3 mb-4 bg-red-50 border border-red-200 rounded-xl">
      <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <span class="text-sm text-red-800"><?= htmlspecialchars($submission_error) ?></span>
    </div>
  <?php endif; ?>

  <div class="space-y-6">
    <!-- Team -->
    <form action="/dashboard/team/update" method="POST" class="review-form bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <h3 class="text-sm font-semibold text-gray-900 mb-4">Registrasi Tim</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Tim <span class="text-red-500">*</span></label>
          <input type="text" name="name" value="<?= htmlspecialchars($team['name'] ?? '') ?>" required class="review-field w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Asal Sekolah <span class="text-red-500">*</span></label>
          <input type="text" name="teamSchool" value="<?= htmlspecialchars($team['teamSchool'] ?? '') ?>" required class="review-field w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
        </div>
      </div>
      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
          <?php foreach ($DIVISION_LABELS as $k => $v): ?>
            <label class="block rounded-lg border-2 p-3 cursor-pointer has-[:checked]:border-brand has-[:checked]:bg-brand/5 transition-all <?= ($team['division'] ?? '') === $k ? 'border-brand bg-brand/5' : 'border-gray-200' ?>">
              <input type="radio" name="division" value="<?= $k ?>" class="hidden review-field" <?= ($team['division'] ?? '') === $k ? 'checked' : '' ?>>
              <p class="text-xs font-medium text-gray-900"><?= htmlspecialchars($v) ?></p>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="mt-4 flex justify-end">
        <button type="submit" class="review-btn text-xs font-medium text-gray-400 border border-gray-200 px-3 py-1.5 rounded-lg transition-colors">Simpan</button>
      </div>
    </form>

    <!-- Anggota -->
    <form action="/dashboard/team/update" method="POST" enctype="multipart/form-data" class="review-form bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <input type="hidden" name="division" value="<?= htmlspecialchars($team['division']) ?>">
      <h3 class="text-sm font-semibold text-gray-900 mb-4">Data Anggota</h3>
      <?php
      $members = [
        ['num' => 1, 'label' => 'Ketua Tim', 'nameKey' => 'leaderName', 'phoneKey' => 'leaderPhoneNumber'],
      ];
      $hasM2 = !empty($team['firstMemberName']);
      $hasM3 = !empty($team['secondMemberName']);
      if ($hasM2) $members[] = ['num' => 2, 'label' => 'Anggota 2', 'nameKey' => 'firstMemberName', 'phoneKey' => 'firstMemberPhoneNumber'];
      if ($hasM3) $members[] = ['num' => 3, 'label' => 'Anggota 3', 'nameKey' => 'secondMemberName', 'phoneKey' => 'secondMemberPhoneNumber'];
      ?>
      <div class="space-y-4">
        <?php foreach ($members as $m): ?>
          <div>
            <p class="text-xs font-semibold text-gray-800 mb-2"><?= htmlspecialchars($m['label']) ?></p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <input type="text" name="<?= $m['nameKey'] ?>" value="<?= htmlspecialchars($team[$m['nameKey']] ?? '') ?>" placeholder="Nama Lengkap" required class="review-field w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
              <input type="text" name="<?= $m['phoneKey'] ?>" value="<?= htmlspecialchars($team[$m['phoneKey']] ?? '') ?>" placeholder="No. Telepon" required class="review-field w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="mt-4 flex justify-end">
        <button type="submit" class="review-btn text-xs font-medium text-gray-400 border border-gray-200 px-3 py-1.5 rounded-lg transition-colors">Simpan</button>
      </div>
    </form>

    <!-- Pembayaran -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
      <h3 class="text-sm font-semibold text-gray-900 mb-3">Pembayaran</h3>
      <?php if ($payment['status'] === 'verified'): ?>
        <div class="flex items-center gap-2 text-sm text-green-700">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Pembayaran Lunas
        </div>
        <?php if (!empty($payment['proofImage'])): ?>
          <img src="/uploads/payments/<?= htmlspecialchars($payment['proofImage']) ?>" class="mt-2 max-h-32 rounded-lg border object-contain bg-gray-50">
        <?php endif; ?>
      <?php elseif ($payment['status'] === 'rejected'): ?>
        <p class="text-sm text-red-600 mb-2">Ditolak: <?= htmlspecialchars($payment['note'] ?? '') ?></p>
      <?php else: ?>
        <p class="text-sm text-yellow-600">Menunggu verifikasi</p>
      <?php endif; ?>
    </div>

    <!-- Submit Karya -->
    <form action="/dashboard/submission" method="POST" enctype="multipart/form-data" class="review-form bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <h3 class="text-sm font-semibold text-gray-900 mb-3">Submit Karya</h3>
      <?php if ($team['division'] === 'FFR'): ?>
        <input type="url" name="youtube_link" required
          value="<?= $submission && $submission['type'] === 'youtube_link' ? htmlspecialchars($submission['value']) : '' ?>"
          placeholder="https://www.youtube.com/watch?v=..."
          class="review-field w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
        <p class="mt-1.5 text-xs text-gray-500">Upload video robot ke YouTube, tempelkan linknya di sini.</p>
      <?php else: ?>
        <input type="file" name="submission_file" required
          class="review-field block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand file:text-white hover:file:bg-brand/90 transition-all cursor-pointer">
        <p class="mt-1.5 text-xs text-gray-500">Maksimal 10MB.</p>
      <?php endif; ?>
      <div class="mt-4 flex justify-end">
        <button type="submit" class="review-btn text-xs font-medium text-gray-400 border border-gray-200 px-3 py-1.5 rounded-lg transition-colors"><?= $submission ? 'Update' : 'Upload' ?></button>
      </div>
    </form>
  </div>

  <script>
  document.querySelectorAll('.review-form').forEach(form => {
    const btn = form.querySelector('.review-btn');
    const fields = form.querySelectorAll('.review-field');
    const initial = new Map();
    fields.forEach(f => { initial.set(f, f.type === 'file' ? '' : f.value); });
    function check() {
      const changed = Array.from(fields).some(f => f.type === 'file' ? f.files.length > 0 : f.value !== initial.get(f));
      btn.className = changed
        ? 'review-btn text-xs font-medium text-brand border border-brand/30 px-3 py-1.5 rounded-lg transition-colors'
        : 'review-btn text-xs font-medium text-gray-400 border border-gray-200 px-3 py-1.5 rounded-lg transition-colors';
    }
    fields.forEach(f => f.addEventListener('change', check));
    fields.forEach(f => f.addEventListener('input', check));
  });
  </script>
<?php endif; ?>
