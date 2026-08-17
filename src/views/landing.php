<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automation Week 9</title>
    <link class="w-4 h-4" rel="icon" href="/image/logo-aw.png">
    <link rel="stylesheet" href="/css/app.css">
    <style>
        .hero-bg {
            background: url('/image/hero-landing.png') left center/cover no-repeat;
        }

        @media (min-width: 640px) {
            .hero-bg {
                background-position: center;
            }
        }

        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="font-sans antialiased text-foreground">
    <nav class="fixed top-4 left-1/2 -translate-x-1/2 flex items-center gap-8 py-2 px-3 max-w-[calc(100vw-2rem)] bg-card/75 [-webkit-backdrop-filter:blur(16px)_saturate(120%)] [backdrop-filter:blur(16px)_saturate(120%)] border border-border rounded-full shadow-[0_12px_40px_-12px_oklch(0%_0_0/0.5)] z-50 transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]" aria-label="Primary">
        <button type="button"
            id="mobile-nav-pill-toggle"
            class="flex md:pointer-events-none items-center gap-2 no-underline text-foreground bg-transparent border-none outline-none focus:outline-none relative z-20"
            aria-haspopup="dialog"
            aria-expanded="false"
            style="appearance: none; -webkit-appearance: none; padding: 0; margin: 0;">
            <img src="/image/logo-aw.png" alt="AW" class="w-6 h-6 object-contain bg-white rounded-full border border-border">
            <span class="font-bold text-sm tracking-tight whitespace-nowrap">Automation Week 9</span>
            <span class="md:hidden inline-flex items-center justify-center" id="mobile-nav-pill-chevron">
                <?= \App\Components\Icon::make()->name('menu')->class('w-4 h-4') ?>
            </span>
        </button>

        <div class="hidden md:flex items-center gap-6 text-xs font-semibold">
            <a href="#competitions" class="hover:text-primary transition-colors no-underline">Lomba</a>
            <a href="#videos" class="hover:text-primary transition-colors no-underline">Video</a>
            <a href="#contact" class="hover:text-primary transition-colors no-underline">Kontak</a>
            <!-- <a href="/application/team-register" class="inline-flex items-center gap-1.5 hover:text-primary transition-colors no-underline">
                <?= \App\Components\Icon::make()->name('user-round')->class('w-4 h-4') ?>
                Pendaftaran
            </a> -->
        </div>

        <div id="mobile-nav-pill-dropdown"
            class="absolute left-0 right-auto w-max min-w-[180px] bg-white border border-border rounded-xl shadow-lg py-2 z-10 hidden md:hidden"
            style="top: calc(100% + 0.5rem);">
            <a href="#competitions" class="block px-4 py-2 text-xs font-semibold text-foreground hover:bg-gray-100 hover:text-primary transition-colors no-underline">Lomba</a>
            <a href="#videos" class="block px-4 py-2 text-xs font-semibold text-foreground hover:bg-gray-100 hover:text-primary transition-colors no-underline">Video</a>
            <a href="#contact" class="block px-4 py-2 text-xs font-semibold text-foreground hover:bg-gray-100 hover:text-primary transition-colors no-underline">Kontak</a>
        </div>

        <a href="/login" class="px-4 py-1.5 rounded-full text-xs font-bold text-white shadow-sm transition-all bg-accent hover:bg-accent/90 no-underline">Login</a>
    </nav>

    <div id="mobile-nav-sheet-backdrop" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 pointer-events-none md:hidden"></div>

    <div id="mobile-nav-sheet" class="fixed inset-y-0 right-0 z-50 w-72 sm:w-80 bg-background border-l border-border shadow-2xl transition-transform duration-300 ease-in-out translate-x-full md:hidden flex flex-col">
        <div class="flex items-center justify-between border-b border-border px-5 py-4">
            <div class="flex items-center gap-2">
                <img src="/image/logo-aw.png" alt="AW" class="w-7 h-7 object-contain bg-white rounded-full border border-border">
                <span class="font-bold text-sm tracking-tight text-foreground whitespace-nowrap">Automation Week 9</span>
            </div>
            <button type="button" id="mobile-nav-sheet-close" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors" aria-label="Tutup menu">
                <?= \App\Components\Icon::make()->name('x')->class('w-5 h-5') ?>
            </button>
        </div>

        <nav class="flex-1 space-y-1.5 p-5">
            <!-- <a href="/application/team-register" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-foreground hover:bg-gray-100 transition-colors no-underline">
                <?= \App\Components\Icon::make()->name('user-round')->class('w-4 h-4 text-accent') ?>
                Pendaftaran
            </a> -->
            <a href="#competitions" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-foreground hover:bg-gray-100 transition-colors no-underline">Lomba</a>
            <a href="#videos" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-foreground hover:bg-gray-100 transition-colors no-underline">Video</a>
            <a href="#contact" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-foreground hover:bg-gray-100 transition-colors no-underline">Kontak</a>
        </nav>

        <div class="border-t border-border p-5">
            <a href="/login" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-full text-xs font-bold text-white bg-accent hover:bg-accent/90 transition-all no-underline">Login</a>
        </div>
    </div>
    <section class="min-h-screen flex flex-col items-center justify-center text-center px-4 relative overflow-hidden">
        <div class="absolute inset-0 z-0 hero-bg"></div>
        <div class="relative z-10 max-w-4xl mx-auto flex flex-col items-center pt-24 pb-12">
            <img src="/image/logo-aw.png" alt="AW Logo" class="w-28 h-28 mb-6 object-contain bg-white rounded-full shadow-2xl p-1 border border-border">

            <div class="flex items-center justify-center gap-3 sm:gap-4 mb-8 bg-white/70 rounded-full p-2">
                <a href="#competitions" class="w-8 h-8 sm:w-14 sm:h-14 hover:scale-110 transition-transform block">
                    <img src="/image/LKTI_AW8.png" alt="LKTI Logo" class="w-full h-full object-contain">
                </a>
                <a href="#competitions" class="w-8 h-8 sm:w-14 sm:h-14 hover:scale-110 transition-transform block">
                    <img src="/image/FFR_AW8.png" alt="FFR Logo" class="w-full h-full object-contain">
                </a>
                <a href="#competitions" class="w-8 h-8 sm:w-14 sm:h-14 hover:scale-110 transition-transform block">
                    <img src="/image/PLC_AW8.png" alt="PLC Logo" class="w-full h-full object-contain">
                </a>
                <a href="#competitions" class="w-8 h-8 sm:w-14 sm:h-14 hover:scale-110 transition-transform block">
                    <img src="/image/LF_AW8.png" alt="Line Follower Logo" class="w-full h-full object-contain">
                </a>
            </div>

            <p class="text-xs font-bold uppercase tracking-tighter md:tracking-wide text-white mb-3">Politeknik Perkapalan Negeri Surabaya</p>
            <h1 class="text-3xl md:text-6xl font-black mb-6 leading-none tracking-tight text-white">Automation <span class="text-white">Week 9</span></h1>
            <p class="text-lg md:text-xl font-medium tracking-wide text-white mb-8 italic">"Powering the Next Evolution"</p>
            <div class="flex flex-row w-full max-w-xs sm:max-w-none gap-3 sm:gap-4 justify-center mx-auto">
                <a href="/application/team-register" class="flex-1 px-4 py-2 sm:px-8 sm:py-3.5 rounded-full text-xs sm:text-sm font-bold text-white shadow-lg border border-white/40 transition-all hover:scale-105 bg-accent hover:bg-accent/90 no-underline text-center">Daftar Sekarang</a>
                <a href="#competitions" class="flex-1 px-4 py-2 sm:px-8 sm:py-3.5 rounded-full text-xs sm:text-sm text-white font-bold text-foreground border border-border hover:text-black hover:bg-card transition-all no-underline text-center">Lihat Lomba</a>
            </div>
        </div>
        <div class="absolute left-0 right-0 bottom-0 z-20">
            <div class="curved-marquee w-full overflow-hidden" style="background: linear-gradient(90deg, #ba1229 0%, #f27c29 50%, #ba1229 100%);">
                <svg class="w-full block" viewBox="0 0 1440 200" preserveAspectRatio="none" style="transform: rotate(2deg); transform-origin: center;">
                    <defs>
                        <path id="marquee-curve" d="M-100,40 Q500,280 1540,40" fill="none"/>
                    </defs>
                    <text class="fill-white font-bold uppercase" style="font-size: 4.5rem; letter-spacing: 0.08em;" xml:space="preserve">
                        <textPath href="#marquee-curve" id="curved-marquee-text"></textPath>
                    </text>
                </svg>
            </div>
        </div>
        </div>
    </section>


    <section id="competitions" class="py-24 px-4 bg-background">
        <div class="max-w-6xl mx-auto">
            <div class="mb-16 text-center">
                <p class="text-xs font-bold uppercase tracking-widest mb-3 text-accent">5 Kategori Lomba</p>
                <h2 class="text-3xl md:text-5xl font-black text-foreground mb-4 tracking-tight">Kategori & Jadwal Lomba</h2>
                <p class="text-muted max-w-2xl mx-auto text-base md:text-lg">
                    Ikuti 5 kategori kompetisi nasional bergengsi tingkat SMA/SMK/MA sederajat di Automation Week. Unduh Guide Book untuk kriteria lengkap.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="card-glow flex flex-col overflow-hidden shadow-sm border border-border rounded-2xl">
                    <div class="h-1.5 bg-gradient-to-r from-yellow-500 to-yellow-600"></div>
                    <div class="p-8 flex flex-col items-center flex-grow">
                        <div class="p-4 bg-secondary border border-border rounded-2xl mb-6 shadow-sm">
                            <img src="/image/LKTI_AW8.png" alt="LKTI" class="w-16 h-16 object-contain">
                        </div>
                        <h3 class="text-2xl font-bold mb-2 text-accent tracking-tight">LKTI</h3>
                        <p class="text-center text-muted mb-6 text-sm leading-relaxed max-w-sm">Lomba Karya Tulis Ilmiah — mengembangkan ide kreatif dan inovatif dalam memecahkan masalah lingkungan sekitar.</p>
                        <ul class="w-full mb-8 text-sm">
                            <li class="flex items-center justify-between py-3 border-b border-border">
                                <span class="text-muted">Pendaftaran Dibuka</span>
                                <span class="font-semibold">22 Sep 2025</span>
                            </li>
                            <li class="flex items-center justify-between py-3 border-b border-border">
                                <span class="text-muted">Deadline Abstrak</span>
                                <span class="font-semibold">9 Okt 2025</span>
                            </li>
                            <li class="flex items-center justify-between py-3">
                                <span class="text-muted">Pelaksanaan Final</span>
                                <span class="text-primary font-bold">28 Okt 2025</span>
                            </li>
                        </ul>
                        <a href="https://drive.google.com/drive/folders/1LlB1h7dXbIcFxFiWV2BUf2RJfVFYDEZb" target="_blank"
                            class="mt-auto inline-flex items-center gap-2 px-6 py-2.5 rounded-full font-bold text-xs text-white bg-accent hover:bg-accent/90 transition-all no-underline shadow">
                            <?= \App\Components\Icon::make()->name('download')->class('w-4 h-4') ?>Guide Book
                        </a>
                    </div>
                </div>

                <div class="card-glow flex flex-col overflow-hidden shadow-sm border border-border rounded-2xl">
                    <div class="h-1.5 bg-gradient-to-r from-cyan-500 to-blue-600"></div>
                    <div class="p-8 flex flex-col items-center flex-grow">
                        <div class="p-4 bg-secondary border border-border rounded-2xl mb-6 shadow-sm">
                            <img src="/image/FFR_AW8.png" alt="FFR" class="w-16 h-16 object-contain">
                        </div>
                        <h3 class="text-2xl font-bold mb-2 text-cyan-600 tracking-tight">FFR</h3>
                        <p class="text-center text-muted mb-6 text-sm leading-relaxed max-w-sm">Fire Fighting Roboboat — kapal tanpa awak yang bergerak otomatis dengan misi memadamkan api.</p>
                        <ul class="w-full mb-8 text-sm">
                            <li class="flex items-center justify-between py-3 border-b border-border">
                                <span class="text-muted">Pendaftaran Dibuka</span>
                                <span class="font-semibold">22 Sep 2025</span>
                            </li>
                            <li class="flex items-center justify-between py-3 border-b border-border">
                                <span class="text-muted">Deadline Pembayaran</span>
                                <span class="font-semibold">27 Okt 2025</span>
                            </li>
                            <li class="flex items-center justify-between py-3">
                                <span class="text-muted">Pelaksanaan Final</span>
                                <span class="text-cyan-600 font-bold">28 Okt 2025</span>
                            </li>
                        </ul>
                        <a href="https://drive.google.com/drive/folders/1Xc2tKgDXoXcN0q_Y-34UnCOw-E6OmqM0" target="_blank"
                            class="mt-auto inline-flex items-center gap-2 px-6 py-2.5 rounded-full font-bold text-xs text-white bg-cyan-600 hover:bg-cyan-500 transition-all no-underline shadow">
                            <?= \App\Components\Icon::make()->name('download')->class('w-4 h-4') ?>Guide Book
                        </a>
                    </div>
                </div>

                <div class="card-glow flex flex-col overflow-hidden shadow-sm border border-border rounded-2xl">
                    <div class="h-1.5 bg-gradient-to-r from-accent to-red-800"></div>
                    <div class="p-8 flex flex-col items-center flex-grow">
                        <div class="p-4 bg-secondary border border-border rounded-2xl mb-6 shadow-sm">
                            <img src="/image/PLC_AW8.png" alt="PLC" class="w-16 h-16 object-contain">
                        </div>
                        <h3 class="text-2xl font-bold mb-2 text-accent tracking-tight">PLC</h3>
                        <p class="text-center text-muted mb-6 text-sm leading-relaxed max-w-sm">Programmable Logic Controller — mengasah logika dan kemampuan dalam bidang pemrograman PLC industri.</p>
                        <ul class="w-full mb-8 text-sm">
                            <li class="flex items-center justify-between py-3 border-b border-border">
                                <span class="text-muted">Pendaftaran Dibuka</span>
                                <span class="font-semibold">22 Sep 2025</span>
                            </li>
                            <li class="flex items-center justify-between py-3 border-b border-border">
                                <span class="text-muted">Deadline Pembayaran</span>
                                <span class="font-semibold">23 Okt 2025</span>
                            </li>
                            <li class="flex items-center justify-between py-3">
                                <span class="text-muted">Pelaksanaan Final</span>
                                <span class="text-primary font-bold">28 Okt 2025</span>
                            </li>
                        </ul>
                        <a href="https://drive.google.com/drive/folders/1QPSJh0ktutXskInEGvoYBCw69jRwd0KO" target="_blank"
                            class="mt-auto inline-flex items-center gap-2 px-6 py-2.5 rounded-full font-bold text-xs text-white bg-accent hover:bg-accent/90 transition-all no-underline shadow">
                            <?= \App\Components\Icon::make()->name('download')->class('w-4 h-4') ?>Guide Book
                        </a>
                    </div>
                </div>

                <div class="card-glow flex flex-col overflow-hidden shadow-sm border border-border rounded-2xl">
                    <div class="h-1.5 bg-gradient-to-r from-purple-600 to-purple-400"></div>
                    <div class="p-8 flex flex-col items-center flex-grow">
                        <div class="p-4 bg-secondary border border-border rounded-2xl mb-6 shadow-sm">
                            <img src="/image/LF_AW8.png" alt="Line Follower" class="w-16 h-16 object-contain">
                        </div>
                        <h3 class="text-2xl font-bold mb-2 text-purple-700 tracking-tight">Line Follower</h3>
                        <p class="text-center text-muted mb-6 text-sm leading-relaxed max-w-sm">Robot mikrokontroler yang ditantang mengikuti lintasan secara otomatis dengan kecepatan tinggi.</p>
                        <ul class="w-full mb-8 text-sm">
                            <li class="flex items-center justify-between py-3 border-b border-border">
                                <span class="text-muted">Pendaftaran Dibuka</span>
                                <span class="font-semibold">22 Sep 2025</span>
                            </li>
                            <li class="flex items-center justify-between py-3 border-b border-border">
                                <span class="text-muted">Deadline Pembayaran</span>
                                <span class="font-semibold">27 Okt 2025</span>
                            </li>
                            <li class="flex items-center justify-between py-3">
                                <span class="text-muted">Pelaksanaan Final</span>
                                <span class="text-purple-700 font-bold">28 Okt 2025</span>
                            </li>
                        </ul>
                        <a href="https://drive.google.com/drive/folders/19rjeLr4o4ZYeSvLECxF9etIvNlbaSsfq" target="_blank"
                            class="mt-auto inline-flex items-center gap-2 px-6 py-2.5 rounded-full font-bold text-xs text-white bg-purple-600 hover:bg-purple-500 transition-all no-underline shadow">
                            <?= \App\Components\Icon::make()->name('download')->class('w-4 h-4') ?>Guide Book
                        </a>
                    </div>
                </div>

                <div class="md:col-span-2 flex justify-center">
                    <div class="card-glow flex flex-col overflow-hidden shadow-sm border border-border rounded-2xl w-full md:w-[calc(50%-1rem)]">
                        <div class="h-1.5 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
                        <div class="p-8 flex flex-col items-center flex-grow">
                            <div class="p-4 bg-secondary border border-border rounded-2xl mb-6 shadow-sm">
                                <div class="w-16 h-16 flex items-center justify-center text-2xl font-black text-emerald-600">PRG</div>
                            </div>
                            <h3 class="text-2xl font-bold mb-2 text-emerald-600 tracking-tight">Program</h3>
                            <p class="text-center text-muted mb-6 text-sm leading-relaxed max-w-sm">Kompetisi pemrograman yang menguji kemampuan algoritma dan logika dalam menyelesaikan masalah secara efisien.</p>
                            <ul class="w-full mb-8 text-sm">
                                <li class="flex items-center justify-between py-3 border-b border-border">
                                    <span class="text-muted">Pendaftaran Dibuka</span>
                                    <span class="font-semibold">22 Sep 2025</span>
                                </li>
                                <li class="flex items-center justify-between py-3 border-b border-border">
                                    <span class="text-muted">Deadline Pembayaran</span>
                                    <span class="font-semibold">27 Okt 2025</span>
                                </li>
                                <li class="flex items-center justify-between py-3">
                                    <span class="text-muted">Pelaksanaan Final</span>
                                    <span class="text-emerald-600 font-bold">28 Okt 2025</span>
                                </li>
                            </ul>
                            <a href="#" target="_blank"
                                class="mt-auto inline-flex items-center gap-2 px-6 py-2.5 rounded-full font-bold text-xs text-white bg-emerald-600 hover:bg-emerald-500 transition-all no-underline shadow">
                                <?= \App\Components\Icon::make()->name('download')->class('w-4 h-4') ?>Guide Book
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- <section class="py-12 px-4 bg-background">
        <div class="max-w-6xl mx-auto">
            <div class="border border-border rounded-2xl overflow-hidden shadow-xl">
                <img src="/image/poster/TES_BANNER 1.png" alt="Banner Automation Week 9" class="w-full block object-cover">
            </div>
        </div>
    </section> -->

    <section id="videos" class="py-24 px-4 bg-background">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-black text-foreground mb-4 tracking-tight">Keseruan Dokumentasi</h2>
                <p class="text-muted max-w-lg mx-auto text-sm md:text-base">Lihat profil jurusan Teknik Otomasi PPNS serta rekap keseruan Automation Week tahun-tahun sebelumnya.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="flex flex-col">
                    <h4 class="font-bold text-sm text-muted mb-3 tracking-wide uppercase">Video Profil Teknik Otomasi</h4>
                    <div class="video-frame aspect-video">
                        <iframe src="https://www.youtube.com/embed/3ZxTrpAgDQI" class="w-full h-full block" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="flex flex-col">
                    <h4 class="font-bold text-sm text-muted mb-3 tracking-wide uppercase">After Movie AW III</h4>
                    <div class="video-frame aspect-video">
                        <iframe src="https://www.youtube.com/embed/foCUO25NnRM" class="w-full h-full block" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="flex flex-col">
                    <h4 class="font-bold text-sm text-muted mb-3 tracking-wide uppercase">After Movie AW V</h4>
                    <div class="video-frame aspect-video">
                        <iframe src="https://youtube.com/embed/U4Iim3J0EeA" class="w-full h-full block" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="flex flex-col">
                    <h4 class="font-bold text-sm text-muted mb-3 tracking-wide uppercase">After Movie AW VII</h4>
                    <div class="video-frame aspect-video">
                        <iframe src="https://youtube.com/embed/1WNG8sT1igc" class="w-full h-full block" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 px-4 bg-background border-t border-border">
        <div class="max-w-6xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-foreground mb-4 tracking-tight">Sponsor</h2>
            <p class="text-muted mb-12 text-sm max-w-sm mx-auto">Didukung oleh berbagai mitra industri dan sponsor terpercaya.</p>
            <div class="flex flex-wrap justify-center gap-8 items-center opacity-75">
                <div class="w-32 h-16 bg-card border border-border rounded-xl flex items-center justify-center text-muted text-xs font-semibold shadow-sm">Logo Sponsor</div>
                <div class="w-32 h-16 bg-card border border-border rounded-xl flex items-center justify-center text-muted text-xs font-semibold shadow-sm">Logo Sponsor</div>
                <div class="w-32 h-16 bg-card border border-border rounded-xl flex items-center justify-center text-muted text-xs font-semibold shadow-sm">Logo Sponsor</div>
                <div class="w-32 h-16 bg-card border border-border rounded-xl flex items-center justify-center text-muted text-xs font-semibold shadow-sm">Logo Sponsor</div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-24 px-4 bg-background border-t border-border">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-5xl font-black text-foreground mb-4 tracking-tight">Kontak</h2>
            <p class="text-muted mb-12 max-w-sm mx-auto text-sm">Hubungi kami untuk informasi lebih lanjut mengenai kompetisi dan pendaftaran.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto text-left">
                <div class="p-6 rounded-2xl border border-border bg-card shadow-md flex flex-col items-start hover:border-accent transition-colors">
                    <div class="p-2.5 bg-accent/10 border border-accent/20 rounded-xl mb-4 text-accent">
                        <?= \App\Components\Icon::make()->name('phone')->class('w-5 h-5') ?>
                    </div>
                    <h4 class="font-bold text-foreground tracking-tight text-lg">Hubungi Kami</h4>
                    <p class="text-sm text-muted mt-2 leading-relaxed">Hubungi panitia via WhatsApp untuk bantuan dan pertanyaan pendaftaran:</p>
                    <span class="text-foreground font-semibold text-sm mt-3">+62 819-9828-2954</span>
                </div>
                <div class="p-6 rounded-2xl border border-border bg-card shadow-md flex flex-col items-start hover:border-accent transition-colors">
                    <div class="p-2.5 bg-accent/10 border border-accent/20 rounded-xl mb-4 text-accent">
                        <?= \App\Components\Icon::make()->name('mail')->class('w-5 h-5') ?>
                    </div>
                    <h4 class="font-bold text-foreground tracking-tight text-lg">Surel Resmi</h4>
                    <p class="text-sm text-muted mt-2 leading-relaxed">Hubungi panitia via email resmi untuk permohonan kerjasama dan proposal:</p>
                    <span class="text-foreground font-semibold text-sm mt-3">automationweek@ppns.ac.id</span>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-16 px-4 bg-secondary/50 border-t border-border">
        <div class="max-w-6xl mx-auto flex flex-col items-center">
            <p class="font-black text-primary text-3xl md:text-5xl tracking-tight text-center max-w-xl leading-tight mb-8">
                Powering the Next Evolution.
            </p>
            <div class="flex justify-center gap-4 mb-8">
                <a href="https://www.youtube.com/channel/UCXepgfxFNcLQcMHgyTrykjw" target="_blank"
                    class="w-10 h-10 rounded-full border border-border text-muted hover:text-primary flex items-center justify-center hover:bg-card transition-all no-underline shadow-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17z"></path>
                        <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon>
                    </svg>
                </a>
                <a href="https://www.instagram.com/automationweek/" target="_blank"
                    class="w-10 h-10 rounded-full border border-border text-muted hover:text-primary flex items-center justify-center hover:bg-card transition-all no-underline shadow-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg>
                </a>
            </div>
            <div class="w-full pt-6 border-t border-border flex flex-col sm:flex-row justify-between items-center text-xs text-muted gap-4">
                <span class="font-bold text-primary">Automation Week 9</span>
                <span>&copy; 2026 Himpunan Mahasiswa Teknik Otomasi PPNS. All rights reserved.</span>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var pill = document.getElementById('mobile-nav-pill-toggle');
            var backdrop = document.getElementById('mobile-nav-sheet-backdrop');
            var sheet = document.getElementById('mobile-nav-sheet');
            var closeBtn = document.getElementById('mobile-nav-sheet-close');
            if (!pill || !sheet || !backdrop) return;

            function close() {
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                sheet.classList.add('translate-x-full');
                document.body.classList.remove('overflow-hidden');
                pill.setAttribute('aria-expanded', 'false');
            }

            function open() {
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                sheet.classList.remove('translate-x-full');
                document.body.classList.add('overflow-hidden');
                pill.setAttribute('aria-expanded', 'true');
            }

            pill.addEventListener('click', function(e) {
                if (window.innerWidth >= 768) return;
                e.stopPropagation();
                backdrop.classList.contains('pointer-events-none') ? open() : close();
            });
            if (closeBtn) closeBtn.addEventListener('click', close);
            backdrop.addEventListener('click', close);
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') close();
            });
            sheet.querySelectorAll('a').forEach(function(a) {
                a.addEventListener('click', close);
            });
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) close();
            });
        });

        (function() {
            var textPath = document.getElementById('curved-marquee-text');
            if (!textPath) return;

            var text = '✦ Pendaftaran dibuka! Segera daftarkan tim Anda — 29 September – 14 Oktober 2025 ✦ Total hadiah puluhan juta rupiah + trophy + e-sertifikat ✦ 5 kategori: LF · PLC · FFR · LKTI · PROGRAM ';
            var speed = 1.5;
            var spacing = 0;
            var measureEl = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            measureEl.setAttribute('xml:space', 'preserve');
            measureEl.style.visibility = 'hidden';
            measureEl.style.fontSize = '4.5rem';
            measureEl.style.fontWeight = 'bold';
            measureEl.style.letterSpacing = '0.1em';
            measureEl.style.textTransform = 'uppercase';
            measureEl.textContent = text;
            textPath.closest('svg').appendChild(measureEl);
            spacing = measureEl.getComputedTextLength();
            measureEl.remove();

            var repeat = Math.ceil(1800 / spacing) + 2;
            var totalText = Array(repeat).fill(text).join('');
            textPath.textContent = totalText;
            textPath.setAttribute('startOffset', -spacing + 'px');

            var dragRef = false, lastXRef = 0, velRef = 0, dirRef = 'left';
            var svg = textPath.closest('svg');

            function step() {
                if (!dragRef) {
                    var cur = parseFloat(textPath.getAttribute('startOffset') || '0');
                    var delta = dirRef === 'right' ? speed : -speed;
                    var next = cur + delta;
                    if (next <= -spacing) next += spacing;
                    if (next > 0) next -= spacing;
                    textPath.setAttribute('startOffset', next + 'px');
                }
                requestAnimationFrame(step);
            }
            requestAnimationFrame(step);

            svg.style.cursor = 'grab';
            svg.addEventListener('pointerdown', function(e) {
                dragRef = true;
                lastXRef = e.clientX;
                velRef = 0;
                svg.style.cursor = 'grabbing';
                e.preventDefault();
            });
            svg.addEventListener('pointermove', function(e) {
                if (!dragRef) return;
                var dx = e.clientX - lastXRef;
                lastXRef = e.clientX;
                velRef = dx;
                var cur = parseFloat(textPath.getAttribute('startOffset') || '0');
                var next = cur + dx;
                if (next <= -spacing) next += spacing;
                if (next > 0) next -= spacing;
                textPath.setAttribute('startOffset', next + 'px');
            });
            function endDrag() {
                if (!dragRef) return;
                dragRef = false;
                dirRef = velRef > 0 ? 'right' : 'left';
                svg.style.cursor = 'grab';
            }
            svg.addEventListener('pointerup', endDrag);
            svg.addEventListener('pointerleave', endDrag);
        })();
    </script>
</body>

</html>