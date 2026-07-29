<?php

/** @var string $current */
?>
<div class="hidden md:block flex items-end gap-0.25 tracking-wide -mb-4.75">
  <a href="/application"
    class="px-4 py-2 text-sm font-medium transition-colors no-underline rounded-t-lg bg-white
      <?= ($current ?? '') !== 'payment' ? 'text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-700' ?>">
    Pendaftaran
  </a>
  <a href="/payments"
    class="px-4 py-2 text-sm font-medium transition-colors no-underline rounded-t-lg bg-white
      <?= ($current ?? '') === 'payment' ? 'text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-700' ?>">
    Pembayaran
  </a>
</div>