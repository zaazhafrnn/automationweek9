<?php

/** @var string $user_name */
/** @var string $page_title */ ?>
<div class="rounded-xl border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="text-2xl font-semibold leading-none tracking-tight">Selamat Datang, Admin <?= htmlspecialchars($user_name ?? '') ?></h3>
        <p class="text-sm text-muted-foreground">Panel admin. Hanya pengguna dengan peran 'admin' yang dapat mengakses halaman ini.</p>
    </div>
    <div class="p-6 pt-0">
        <div class="rounded-lg border bg-muted/50 p-4">
            <h3 class="mb-3 text-sm font-semibold">Kemampuan Admin</h3>
            <ul class="space-y-2 text-sm text-muted-foreground">
                <li class="flex items-start gap-2"><span class="mt-1.5 block h-1.5 w-1.5 rounded-full bg-primary shrink-0"></span><span><strong class="text-foreground">Manajemen Pengguna:</strong> Lihat data pengguna dan tim dari menu samping.</span></li>
                <li class="flex items-start gap-2"><span class="mt-1.5 block h-1.5 w-1.5 rounded-full bg-primary shrink-0"></span><span><strong class="text-foreground">Verifikasi Pembayaran:</strong> Terima atau tolak bukti pembayaran tim.</span></li>
                <li class="flex items-start gap-2"><span class="mt-1.5 block h-1.5 w-1.5 rounded-full bg-primary shrink-0"></span><span><strong class="text-foreground">Lihat Karya:</strong> Akses file/link yang diupload peserta per divisi.</span></li>
            </ul>
        </div>
    </div>
</div>