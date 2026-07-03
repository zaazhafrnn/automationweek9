<?php /** @var string $user_name */ /** @var string $page_title */ ?>
<div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-200">
    <div class="px-6 py-8">
        <h2 class="text-3xl font-extrabold text-gray-800 mb-4">Selamat Datang, Admin <?= htmlspecialchars($user_name ?? '') ?>! 🛡️</h2>
        <p class="text-lg text-gray-600 mb-8">Ini adalah panel admin yang aman. Hanya pengguna dengan peran 'admin' yang dapat melihat halaman ini.</p>

        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-indigo-900 mb-4 flex items-center">
                Kemampuan Admin
            </h3>
            <ul class="space-y-3 text-indigo-800">
                <li class="flex items-start">
                    <span class="mr-2 text-indigo-600">•</span>
                    <span><strong>Manajemen Pengguna:</strong> Anda dapat melihat data pengguna dari sini.</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2 text-indigo-600">•</span>
                    <span><strong>Kontrol Akses Berbasis Peran:</strong> Hanya dapat diakses oleh pengguna yang perannya ditetapkan sebagai 'admin' di database.</span>
                </li>
            </ul>
        </div>
    </div>
</div>