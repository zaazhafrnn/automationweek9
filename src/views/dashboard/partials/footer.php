<?php

$DIVISION_CONTACTS = [
  'LF' => ['name' => 'Line Follower', 'wa' => '08xxxxxxxxxx'],
  'PLC' => ['name' => 'Programmable Logic Controller', 'wa' => '08xxxxxxxxxx'],
  'FFR' => ['name' => 'Fire Fighting Robot', 'wa' => '08xxxxxxxxxx'],
  'LKTI' => ['name' => 'Lomba Karya Tulis Ilmiah', 'wa' => '08xxxxxxxxxx'],
  'PROG' => ['name' => 'Algoritma Program', 'wa' => '08xxxxxxxxxx'],
];
$MAPS_URL = 'https://www.google.com/maps/search/Jl.+Teknik+Kimia+Kampus+ITS+Sukolilo+Keputih+Sukolilo+Surabaya+60111';
?>
<footer class="px-4 sm:px-6 lg:px-8">
  <div class="bg-zinc-200 text-black rounded-4xl overflow-hidden shadow-2xl/24 border border-gray-300">
    <div class="p-6 sm:p-8">
      <div class="flex flex-col md:flex-row gap-8 md:gap-10">
        <div class="md:w-2/5">
          <h2 class="font-black text-brand text-3xl sm:text-4xl lg:text-5xl xl:text-6xl tracking-tight leading-[1.1]">
            Powering the Next<br>Evolution.
          </h2>
        </div>

        <div class="md:w-3/5 grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8">
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Sosial</h4>
            <ul class="space-y-2.5 text-sm">
              <li class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="20" height="16" x="2" y="4" rx="2" />
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                </svg>
                <a href="mailto:automationweek@ppns.ac.id" class="hover:text-brand transition-colors no-underline break-all">automationweek@ppns.ac.id</a>
              </li>
              <li class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                  <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                  <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                </svg>
                <a href="https://www.instagram.com/automationweek/" target="_blank" class="hover:text-brand transition-colors no-underline">@automationweek</a>
              </li>
              <li class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17z" />
                  <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" />
                </svg>
                <a href="https://www.youtube.com/@himatoppnsofficialaccount6342" target="_blank" class="hover:text-brand transition-colors no-underline">HIMATO PPNS</a>
              </li>
            </ul>
          </div>

          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Alamat</h4>
            <a href="<?= $MAPS_URL ?>" target="_blank" class="text-sm leading-relaxed hover:text-brand transition-colors no-underline block">
              Jl. Teknik Kimia, Kampus ITS Sukolilo,<br>
              Keputih, Kec. Sukolilo,<br>
              Kota Surabaya, Jawa Timur 60111
            </a>
            <p class="text-sm mt-2">Telp. (031) 5947186</p>
          </div>

          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">
              Kontak Divisi <span class="font-normal normal-case tracking-normal">(WhatsApp)</span>
            </h4>

            <ul class="space-y-2 text-sm">
              <?php foreach ($DIVISION_CONTACTS as $code => $div): ?>
                <li class="">
                  <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $div['wa']) ?>" target="_blank" class="flex items-center justify-between sm:flex-col sm:items-start sm:justify-start gap-0.5 hover:text-brand transition-colors no-underline">
                    <span class="text-xs"><?= htmlspecialchars($div['name']) ?></span>
                    <?= htmlspecialchars($div['wa']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
      <p class="text-xs text-gray-500 mt-8 md:mt-0">&copy; 2026 Himpunan Mahasiswa Teknik Otomasi PPNS. All rights reserved.</p>
    </div>
  </div>

  <div class="relative overflow-hidden select-none pt-4 max-h-[22vw] md:max-h-[8vw]">
    <p class="font-black text-[31vw] sm:text-[14vw] md:text-[10.2vw] leading-none tracking-tight whitespace-nowrap bg-gradient-to-b from-gray-900/20 to-transparent bg-clip-text text-transparent">
      <span class="md:hidden">AW IX</span><span class="hidden md:inline">AutomationWeek IX</span>
    </p>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-gray-50 pointer-events-none"></div>
  </div>
</footer>