<?php

use App\Components\Dialog;
use App\Components\Icon;
use App\Components\Toast;

/** @var string $csrf_token */
/** @var string $user_name */
/** @var string $user_email */
/** @var string|null $success */
/** @var string|null $error */

$errors = $errors ?? [];
?>
<div class="min-h-screen bg-gray-50">
  <?php $current = 'profile';
  include BASE_PATH . '/src/Components/nav-tabs.php'; ?>

  <div class="px-4 sm:px-6 lg:px-8 py-6">
    <?php if (!empty($success)): ?>
      <?= Toast::make()->title('Berhasil')->message($success)->variant('success') ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 flex flex-col">
        <div class="flex items-center gap-4 mb-6">
          <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center shrink-0">
            <?= Icon::make()->name('user-round')->class('w-7 h-7 text-brand') ?>
          </div>
          <div>
            <h2 class="text-lg font-bold text-gray-800">Profil Saya</h2>
            <p class="text-xs text-gray-500">Kelola informasi akun kamu.</p>
          </div>
        </div>

        <form action="/profile" method="POST" novalidate class="flex flex-col flex-1">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

          <div class="space-y-4 flex-1">
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

          <div class="flex justify-end mt-auto pt-6">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand rounded-xl hover:bg-red-800 transition-colors">
              <?= Icon::make()->name('check')->class('w-4 h-4') ?>
              Simpan Profil
            </button>
          </div>
        </form>
      </div>

      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 flex flex-col">
        <div class="flex items-center gap-4 mb-6">
          <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center shrink-0">
            <?= Icon::make()->name('lock')->class('w-7 h-7 text-brand') ?>
          </div>
          <div>
            <h2 class="text-lg font-bold text-gray-800">Ubah Password</h2>
            <p class="text-xs text-gray-500">Pastikan password baru minimal 8 karakter.</p>
          </div>
        </div>

        <form action="/profile/update-password" method="POST" novalidate class="flex flex-col flex-1">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

          <div class="space-y-4">
            <div>
              <label for="current_password" class="block text-sm font-semibold text-gray-800 mb-1.5">Password Saat Ini <span class="text-red-500">*</span></label>
              <div class="relative">
                <input type="password" id="current_password" name="current_password" required minlength="8"
                  class="w-full px-4 py-2.5 pr-10 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/20 focus:border-brand transition-colors
                  <?= !empty($errors['current_password_error']) ? 'border-red-500' : '' ?>">
                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer" data-password-toggle="current_password"></button>
              </div>
              <?php if (!empty($errors['current_password_error'])): ?>
                <p class="text-xs text-red-500 mt-1"><?= htmlspecialchars($errors['current_password_error']) ?></p>
              <?php endif; ?>
            </div>

            <div>
              <label for="new_password" class="block text-sm font-semibold text-gray-800 mb-1.5">Password Baru <span class="text-red-500">*</span></label>
              <div class="relative">
                <input type="password" id="new_password" name="new_password" required minlength="8"
                  class="w-full px-4 py-2.5 pr-10 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/20 focus:border-brand transition-colors
                  <?= !empty($errors['new_password_error']) ? 'border-red-500' : '' ?>">
                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer" data-password-toggle="new_password"></button>
              </div>
              <?php if (!empty($errors['new_password_error'])): ?>
                <p class="text-xs text-red-500 mt-1"><?= htmlspecialchars($errors['new_password_error']) ?></p>
              <?php endif; ?>
            </div>

            <div>
              <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-800 mb-1.5">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
              <div class="relative">
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" required minlength="8"
                  class="w-full px-4 py-2.5 pr-10 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/20 focus:border-brand transition-colors">
                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer" data-password-toggle="new_password_confirmation"></button>
              </div>
            </div>
          </div>

          <div class="flex justify-between items-center mt-auto pt-6">
            <button type="button" onclick="openDialog('reset-password-dialog')" class="text-sm text-brand font-bold hover:underline cursor-pointer">
              Lupa Password?
            </button>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand rounded-xl hover:bg-red-800 transition-colors">
              <?= Icon::make()->name('check')->class('w-4 h-4') ?>
              Simpan Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include BASE_PATH . '/src/Components/footer.php'; ?>
</div>

<?= Dialog::make()->id('reset-password-dialog')->title('Reset Password')->width('max-w-md')->content('
    <p class="text-sm text-gray-600 mb-6">Kami akan mengirimkan link reset password ke email anda <strong>' . htmlspecialchars($user_email) . '</strong></p>
    <div class="flex justify-end gap-3">
      <button onclick="closeDialog(\'reset-password-dialog\')" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">Batal</button>
      <button onclick="requestPasswordReset()" class="px-4 py-2 text-sm font-semibold text-white bg-brand rounded-lg hover:bg-red-800 transition-colors">Kirim Link</button>
    </div>
') ?>

<script>
  (async () => {
    const [eye, eyeOff] = await Promise.all([
      fetch('/icons/eye.svg').then(r => r.text()),
      fetch('/icons/eye-off.svg').then(r => r.text())
    ]);
    document.querySelectorAll('[data-password-toggle]').forEach(btn => {
      btn.innerHTML = eye;
      btn.addEventListener('click', () => {
        const inp = document.getElementById(btn.dataset.passwordToggle);
        inp.type = inp.type === 'password' ? 'text' : 'password';
        btn.innerHTML = inp.type === 'password' ? eye : eyeOff;
      });
    });
  })();

  window.requestPasswordReset = function() {
    closeDialog('reset-password-dialog');
    fetch('/forgot-password', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'email=' + encodeURIComponent('<?= htmlspecialchars($user_email) ?>') + '&csrf_token=' + encodeURIComponent('<?= htmlspecialchars($csrf_token) ?>')
      })
      .then(r => r.json())
      .then(data => {
        const toast = document.createElement('div');
        toast.id = 'flash-toast';
        toast.setAttribute('role', 'alert');
        toast.className = 'pointer-events-auto relative w-full rounded-xl border border-green-500/50 bg-[#1e1d1a] px-4 py-3 pl-10 text-sm text-white shadow-lg opacity-0 -translate-x-2 transition-all duration-300';
        toast.innerHTML = '<p class="font-semibold">Link terkirim</p><p class="mt-0.5 pr-6 text-xs text-gray-400">Periksa inbox email Anda.</p>';
        const root = document.getElementById('toast-root');
        root.appendChild(toast);
        requestAnimationFrame(() => toast.classList.remove('opacity-0', '-translate-x-2'));
        setTimeout(() => {
          toast.classList.add('opacity-0', '-translate-x-2');
          setTimeout(() => toast.remove(), 300);
        }, 5000);
      })
      .catch(() => {});
  };
</script>