<?php

/** @var string $current */
?>
<div class="hidden md:block flex items-end gap-0.25 tracking-wide -mb-4.75">
  <a href="/application"
    class="px-4 py-2 text-sm font-medium transition-colors rounded-t-lg
      <?= ($current ?? '') !== 'payment' ? 'text-gray-900 font-semibold bg-gray-100 hover:bg-slate-300' : 'text-gray-500 hover:text-black bg-gray-200 hover:bg-gray-300' ?>">
    Pendaftaran
  </a>
  <a href="/payments"
    class="px-4 py-2 text-sm font-medium transition-colors rounded-t-lg
      <?= ($current ?? '') === 'payment' ? 'text-gray-900 font-semibold bg-gray-100 hover:bg-slate-300' : 'text-gray-500 hover:text-black bg-gray-200 hover:bg-gray-300' ?>">
    Pembayaran
  </a>
</div>