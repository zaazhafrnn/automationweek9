<?php

use App\Components\Icon;

/** @var string $csrf_token */
/** @var string $user_name */
/** @var string $user_email */
/** @var string|null $success */
/** @var string|null $error */

$errors = $errors ?? [];
?>
<div class="min-h-screen bg-gray-50">
  <div class="bg-brand border-b border-gray-200 text-white">
    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-14">
      <div>
        <h1 class="text-lg font-bold">Hi, <?= htmlspecialchars(explode(' ', $user_name ?? '')[0]) ?>!</h1>
        <p class="text-xs -mt-0.5">Kelola pendaftaran tim kamu.</p>
      </div>
      <?php $current = 'profile';
      include __DIR__ . '/partials/nav-tabs.php'; ?>
      <form action="/logout" method="POST" class="m-0 hidden md:block">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-black bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
          <?= Icon::make()->name('log-out')->class('w-3.5 h-3.5') ?>
          Logout
        </button>
      </form>
    </div>
  </div>

  <div class="px-4 sm:px-6 lg:px-8 py-6">
    <?php if (!empty($success)): ?>
      <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-xl mb-6">
        <?= Icon::make()->name('check-circle')->class('w-5 h-5 text-green-500 shrink-0') ?>
        <p class="text-sm text-green-700"><?= htmlspecialchars($success) ?></p>
      </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <div class="flex items-center gap-4 mb-6">
          <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center shrink-0">
            <?= Icon::make()->name('user-round')->class('w-7 h-7 text-brand') ?>
          </div>
          <div>
            <h2 class="text-lg font-bold text-gray-800">Profil Saya</h2>
            <p class="text-xs text-gray-500">Kelola informasi akun kamu.</p>
          </div>
        </div>

        <form action="/profile" method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

          <div class="space-y-4">
            <div>
              <label for="name" class="block text-sm font-semibold text-gray-800 mb-1.5">Nama <span class="text-red-500">*</span></label>
              <input type="text" id="name" name="name" required
                value="<?= htmlspecialchars($old_name ?? $user_name ?? '') ?>"
                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/20 focus:border-brand transition-colors
                <?= !empty($errors['name_error']) ? 'border-red-500' : '' ?>">
              <?php if (!empty($errors['name_error'])): ?>
                <p class="text-xs text-red-500 mt-1"><?= htmlspecialchars($errors['name_error']) ?></p>
              <?php endif; ?>
            </div>

            <div>
              <label for="email" class="block text-sm font-semibold text-gray-800 mb-1.5">Email</label>
              <input type="email" id="email" name="email" readonly
                value="<?= htmlspecialchars($user_email ?? '') ?>"
                class="w-full px-4 py-2.5 text-sm border border-gray-100 rounded-xl bg-gray-50 text-gray-500 cursor-not-allowed">
              <p class="text-xs text-gray-400 mt-1">Email tidak dapat diubah.</p>
            </div>
          </div>

          <div class="flex justify-end mt-6">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand rounded-xl hover:bg-red-800 transition-colors">
              <?= Icon::make()->name('check')->class('w-4 h-4') ?>
              Simpan Profil
            </button>
          </div>
        </form>
      </div>

      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <div class="flex items-center gap-4 mb-6">
          <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center shrink-0">
            <?= Icon::make()->name('lock')->class('w-7 h-7 text-brand') ?>
          </div>
          <div>
            <h2 class="text-lg font-bold text-gray-800">Ubah Password</h2>
            <p class="text-xs text-gray-500">Pastikan password baru minimal 8 karakter.</p>
          </div>
        </div>

        <form action="/profile/update-password" method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

          <div class="space-y-4">
            <div>
              <label for="current_password" class="block text-sm font-semibold text-gray-800 mb-1.5">Password Saat Ini <span class="text-red-500">*</span></label>
              <input type="password" id="current_password" name="current_password" required minlength="8"
                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/20 focus:border-brand transition-colors
                <?= !empty($errors['current_password_error']) ? 'border-red-500' : '' ?>">
              <?php if (!empty($errors['current_password_error'])): ?>
                <p class="text-xs text-red-500 mt-1"><?= htmlspecialchars($errors['current_password_error']) ?></p>
              <?php endif; ?>
            </div>

            <div>
              <label for="new_password" class="block text-sm font-semibold text-gray-800 mb-1.5">Password Baru <span class="text-red-500">*</span></label>
              <input type="password" id="new_password" name="new_password" required minlength="8"
                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/20 focus:border-brand transition-colors
                <?= !empty($errors['new_password_error']) ? 'border-red-500' : '' ?>">
              <?php if (!empty($errors['new_password_error'])): ?>
                <p class="text-xs text-red-500 mt-1"><?= htmlspecialchars($errors['new_password_error']) ?></p>
              <?php endif; ?>
            </div>

            <div>
              <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-800 mb-1.5">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
              <input type="password" id="new_password_confirmation" name="new_password_confirmation" required minlength="8"
                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/20 focus:border-brand transition-colors">
            </div>
          </div>

          <div class="flex justify-end mt-6">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand rounded-xl hover:bg-red-800 transition-colors">
              <?= Icon::make()->name('check')->class('w-4 h-4') ?>
              Simpan Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include __DIR__ . '/partials/footer.php'; ?>
</div>
