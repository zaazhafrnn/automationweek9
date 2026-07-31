<?php

use App\Components\Icon;

/** @var string $current */
/** @var string|null $csrf_token */
?>

<div class="hidden md:flex items-end gap-0.25 tracking-wide -mb-4.75">
  <a href="/home"
    class="px-4 py-2 text-sm font-medium transition-colors rounded-t-lg
      <?= ($current ?? '') === 'home' ? 'text-gray-900 font-semibold bg-gray-100 hover:bg-slate-300' : 'text-gray-500 hover:text-black bg-gray-200 hover:bg-gray-300' ?>">
    Beranda
  </a>
  <a href="/application"
    class="px-4 py-2 text-sm font-medium transition-colors rounded-t-lg
      <?= ($current ?? '') === 'application' ? 'text-gray-900 font-semibold bg-gray-100 hover:bg-slate-300' : 'text-gray-500 hover:text-black bg-gray-200 hover:bg-gray-300' ?>">
    Pendaftaran
  </a>
  <a href="/payments"
    class="px-4 py-2 text-sm font-medium transition-colors rounded-t-lg
      <?= ($current ?? '') === 'payment' ? 'text-gray-900 font-semibold bg-gray-100 hover:bg-slate-300' : 'text-gray-500 hover:text-black bg-gray-200 hover:bg-gray-300' ?>">
    Pembayaran
  </a>
</div>

<div class="md:hidden">
  <button type="button" id="mobile-sheet-trigger" class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white focus:outline-none transition-colors border border-white/20" aria-label="Open Navigation Drawer">
    <?= Icon::make()->name('menu')->class('w-5 h-5') ?>
  </button>
</div>

<div id="mobile-sheet-backdrop" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 pointer-events-none md:hidden"></div>

<div id="mobile-sheet-content" class="fixed inset-y-0 right-0 z-50 w-72 bg-white shadow-2xl transition-transform duration-300 ease-in-out translate-x-full md:hidden flex flex-col justify-between p-6">
  <div>
    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
      <div class="flex items-center gap-2">
        <img src="/image/logo-aw.png" alt="Automation Week" class="w-7 h-7 object-contain">
        <span class="font-bold text-gray-900 text-sm">Automation Week IX</span>
      </div>
      <button type="button" id="mobile-sheet-close" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
        <?= Icon::make()->name('x')->class('w-5 h-5') ?>
      </button>
    </div>

    <nav class="space-y-1.5">
      <a href="/home" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors no-underline
        <?= ($current ?? '') === 'home' ? 'bg-red-50 text-brand font-bold' : 'text-gray-700 hover:bg-gray-50' ?>">
        <?= Icon::make()->name('home')->class('w-4 h-4') ?>
        Beranda
      </a>
      <a href="/application" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors no-underline
        <?= ($current ?? '') === 'application' ? 'bg-red-50 text-brand font-bold' : 'text-gray-700 hover:bg-gray-50' ?>">
        <?= Icon::make()->name('user-round')->class('w-4 h-4') ?>
        Pendaftaran
      </a>
      <a href="/payments" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors no-underline
        <?= ($current ?? '') === 'payment' ? 'bg-red-50 text-brand font-bold' : 'text-gray-700 hover:bg-gray-50' ?>">
        <?= Icon::make()->name('credit-card')->class('w-4 h-4') ?>
        Pembayaran
      </a>
    </nav>
  </div>

  <!-- Sheet Footer -->
  <div class="border-t border-gray-100 pt-4">
    <form action="/logout" method="POST" class="m-0">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
      <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
        <?= Icon::make()->name('log-out')->class('w-4 h-4') ?>
        Logout
      </button>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('mobile-sheet-trigger');
    const closeBtn = document.getElementById('mobile-sheet-close');
    const backdrop = document.getElementById('mobile-sheet-backdrop');
    const content = document.getElementById('mobile-sheet-content');

    function openSheet() {
      backdrop.classList.remove('opacity-0', 'pointer-events-none');
      content.classList.remove('translate-x-full');
      document.body.classList.add('overflow-hidden');
    }

    function closeSheet() {
      backdrop.classList.add('opacity-0', 'pointer-events-none');
      content.classList.add('translate-x-full');
      document.body.classList.remove('overflow-hidden');
    }

    if (trigger) trigger.addEventListener('click', openSheet);
    if (closeBtn) closeBtn.addEventListener('click', closeSheet);
    if (backdrop) backdrop.addEventListener('click', closeSheet);
  });
</script>