<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automation Week 9</title>
    <link rel="icon" href="/image/logo-aw.png">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="font-sans text-gray-800 antialiased">

<nav class="fixed top-0 left-0 w-full z-10 bg-gray-900/90 backdrop-blur-sm border-b border-gray-800">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2 no-underline text-white">
            <img src="/image/logo-aw.png" alt="AW" class="w-10 h-10 object-contain bg-white rounded-full">
            <span class="font-bold text-lg">Automation Week 9</span>
        </a>
        <div class="hidden md:flex items-center gap-6 text-sm font-medium">
            <a href="#competitions" class="text-gray-300 hover:text-white transition-colors no-underline">Lomba</a>
            <a href="#videos" class="text-gray-300 hover:text-white transition-colors no-underline">Video</a>
            <a href="#timeline" class="text-gray-300 hover:text-white transition-colors no-underline">Timeline</a>
            <a href="#contact" class="text-gray-300 hover:text-white transition-colors no-underline">Kontak</a>
            <a href="/login" class="px-4 py-2 rounded-lg text-sm font-semibold text-white shadow-sm transition-colors" style="background-color:#bc0301;">Login/Daftar</a>
        </div>
        <button id="menu-toggle" class="md:hidden text-white text-2xl bg-transparent border-0 cursor-pointer">☰</button>
    </div>
    <div id="mobile-menu" class="hidden md:hidden px-4 pb-4 space-y-2 bg-gray-900/95">
        <a href="#competitions" class="block text-gray-300 hover:text-white no-underline text-sm">Lomba</a>
        <a href="#videos" class="block text-gray-300 hover:text-white no-underline text-sm">Video</a>
        <a href="#timeline" class="block text-gray-300 hover:text-white no-underline text-sm">Timeline</a>
        <a href="#contact" class="block text-gray-300 hover:text-white no-underline text-sm">Kontak</a>
        <a href="/login" class="block text-center px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background-color:#bc0301;">Login/Daftar</a>
    </div>
</nav>

<section class="min-h-screen flex items-center justify-center text-center text-white relative overflow-hidden" style="background: linear-gradient(135deg, #1a1a2e 0%, #bc0301 50%, #1a1a2e 100%);">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 25% 50%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
    <div class="relative z-1 px-4 max-w-4xl">
        <img src="/image/logo-aw.png" alt="AW Logo" class="w-48 h-48 mx-auto mb-6 object-contain bg-white rounded-full shadow-xl">
        <p class="text-sm font-semibold uppercase tracking-wider mb-2 opacity-80">Politeknik Perkapalan Negeri Surabaya</p>
        <h1 class="text-5xl md:text-7xl font-extrabold mb-4 leading-tight">Automation<br><span class="text-6xl md:text-8xl" style="color:#facc15;">Week 9</span></h1>
        <p class="text-xl md:text-2xl font-light mb-2 italic">"Fuel the Red Automation"</p>
        <p class="text-base md:text-lg mb-8 max-w-2xl mx-auto opacity-90">Kompetisi Nasional bergengsi tingkat SMA/SMK/MA sederajat. Total hadiah puluhan juta rupiah!</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/dashboard/team/register" class="px-8 py-3 rounded-lg text-base font-bold text-white shadow-lg transition-transform hover:scale-105" style="background-color:#bc0301;">Daftar Sekarang</a>
            <a href="#competitions" class="px-8 py-3 rounded-lg text-base font-bold text-white border-2 border-white/30 hover:border-white/60 transition-colors no-underline">Lihat Lomba</a>
        </div>
    </div>
</section>

<div class="py-3 text-white text-sm font-medium overflow-hidden whitespace-nowrap relative" style="background: linear-gradient(90deg, #8a1414, #bc0301, #8a1414);">
    <div class="marquee-track inline-flex" style="animation: marquee 40s linear infinite;">
        <span class="px-8 inline-flex items-center gap-2"><i class="fas fa-bullhorn text-yellow-500"></i> <strong>Pendaftaran dibuka!</strong> Segera daftarkan tim Anda — <strong>29 September – 14 Oktober 2025</strong></span>
        <span class="px-8 inline-flex items-center gap-2"><i class="fas fa-trophy text-yellow-500"></i> Total hadiah <strong>puluhan juta rupiah</strong> + trophy + e-sertifikat</span>
        <span class="px-8 inline-flex items-center gap-2"><i class="fas fa-robot text-yellow-500"></i> 4 kategori: LF · PLC · FFR · LKTI</span>
        <span class="px-8 inline-flex items-center gap-2"><i class="fas fa-bullhorn text-yellow-500"></i> <strong>Pendaftaran dibuka!</strong> Segera daftarkan tim Anda — <strong>29 September – 14 Oktober 2025</strong></span>
        <span class="px-8 inline-flex items-center gap-2"><i class="fas fa-trophy text-yellow-500"></i> Total hadiah <strong>puluhan juta rupiah</strong> + trophy + e-sertifikat</span>
        <span class="px-8 inline-flex items-center gap-2"><i class="fas fa-robot text-yellow-500"></i> 4 kategori: LF · PLC · FFR · LKTI</span>
    </div>
</div>

<section id="competitions" class="py-16 px-4" style="background: #f8f9fa;">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Kategori Lomba</h2>
            <p class="text-gray-600 max-w-3xl mx-auto text-lg">Automation Week merupakan event tahunan terbesar dari Himpunan Mahasiswa Teknik Otomasi PPNS. Empat kompetisi bergengsi tingkat Nasional.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="rounded-xl p-6 shadow-md border border-gray-200 transition-transform hover:scale-[1.02] bg-white flex flex-col">
                <div class="text-center mb-4">
                    <img src="/image/LKTI_AW8.png" alt="LKTI" class="w-20 h-20 mx-auto object-contain">
                </div>
                <h3 class="text-xl font-bold text-center mb-3">LKTI</h3>
                <p class="text-sm text-gray-600 text-center flex-grow">Lomba Karya Tulis Ilmiah — mengembangkan ide kreatif dan inovatif dalam memecahkan masalah lingkungan sekitar.</p>
                <div class="text-center mt-4">
                    <a href="https://drive.google.com/drive/folders/1LlB1h7dXbIcFxFiWV2BUf2RJfVFYDEZb" target="_blank" class="inline-block px-5 py-2 rounded-full text-sm font-semibold text-white shadow-sm transition-all hover:scale-105" style="background: linear-gradient(135deg, #ffc107, #ff9800);">
                        <i class="fas fa-download mr-2"></i>Guide Book
                    </a>
                </div>
            </div>

            <div class="rounded-xl p-6 shadow-md border border-gray-200 transition-transform hover:scale-[1.02] bg-white flex flex-col">
                <div class="text-center mb-4">
                    <img src="/image/FFR_AW8.png" alt="FFR" class="w-20 h-20 mx-auto object-contain">
                </div>
                <h3 class="text-xl font-bold text-center mb-3">FFR</h3>
                <p class="text-sm text-gray-600 text-center flex-grow">Fire Fighting Roboboat — kapal tanpa awak yang bergerak otomatis dengan misi memadamkan api.</p>
                <div class="text-center mt-4">
                    <a href="https://drive.google.com/drive/folders/1Xc2tKgDXoXcN0q_Y-34UnCOw-E6OmqM0" target="_blank" class="inline-block px-5 py-2 rounded-full text-sm font-semibold text-white shadow-sm transition-all hover:scale-105" style="background: linear-gradient(135deg, #17a2b8, #007bff);">
                        <i class="fas fa-download mr-2"></i>Guide Book
                    </a>
                </div>
            </div>

            <div class="rounded-xl p-6 shadow-md border border-gray-200 transition-transform hover:scale-[1.02] bg-white flex flex-col">
                <div class="text-center mb-4">
                    <img src="/image/PLC_AW8.png" alt="PLC" class="w-20 h-20 mx-auto object-contain">
                </div>
                <h3 class="text-xl font-bold text-center mb-3">PLC</h3>
                <p class="text-sm text-gray-600 text-center flex-grow">Programmable Logic Controller — mengasah logika dan kemampuan dalam bidang pemrograman PLC.</p>
                <div class="text-center mt-4">
                    <a href="https://drive.google.com/drive/folders/1QPSJh0ktutXskInEGvoYBCw69jRwd0KO" target="_blank" class="inline-block px-5 py-2 rounded-full text-sm font-semibold text-white shadow-sm transition-all hover:scale-105" style="background: linear-gradient(135deg, #dc3545, #c82333);">
                        <i class="fas fa-download mr-2"></i>Guide Book
                    </a>
                </div>
            </div>

            <div class="rounded-xl p-6 shadow-md border border-gray-200 transition-transform hover:scale-[1.02] bg-white flex flex-col">
                <div class="text-center mb-4">
                    <img src="/image/LF_AW8.png" alt="LF" class="w-20 h-20 mx-auto object-contain">
                </div>
                <h3 class="text-xl font-bold text-center mb-3">Line Follower</h3>
                <p class="text-sm text-gray-600 text-center flex-grow">Robot berbasis mikrokontroler yang ditantang mengikuti lintasan secara otomatis dengan kecepatan dan ketepatan tinggi.</p>
                <div class="text-center mt-4">
                    <a href="https://drive.google.com/drive/folders/19rjeLr4o4ZYeSvLECxF9etIvNlbaSsfq" target="_blank" class="inline-block px-5 py-2 rounded-full text-sm font-semibold text-white shadow-sm transition-all hover:scale-105" style="background: linear-gradient(135deg, #5028a7, #9a3ebe);">
                        <i class="fas fa-download mr-2"></i>Guide Book
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="py-8 px-4 bg-white">
    <div class="max-w-6xl mx-auto">
        <img src="/image/poster/TES_BANNER 1.png" alt="Banner Automation Week 9" class="w-full rounded-xl shadow-md">
    </div>
</section>

<section id="videos" class="py-16 px-4 bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Video</h2>
            <p class="text-gray-600">Lihat keseruan Automation Week tahun-tahun sebelumnya.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h4 class="font-bold text-gray-800 mb-3 text-center">Video Profil Teknik Otomasi</h4>
                <div class="aspect-video rounded-xl overflow-hidden shadow-md">
                    <iframe src="https://www.youtube.com/embed/3ZxTrpAgDQI" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-gray-800 mb-3 text-center">After Movie AW III</h4>
                <div class="aspect-video rounded-xl overflow-hidden shadow-md">
                    <iframe src="https://www.youtube.com/embed/foCUO25NnRM" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-gray-800 mb-3 text-center">After Movie AW V</h4>
                <div class="aspect-video rounded-xl overflow-hidden shadow-md">
                    <iframe src="https://youtube.com/embed/U4Iim3J0EeA" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-gray-800 mb-3 text-center">After Movie AW VII</h4>
                <div class="aspect-video rounded-xl overflow-hidden shadow-md">
                    <iframe src="https://youtube.com/embed/1WNG8sT1igc" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="timeline" class="py-16 px-4 bg-white">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Timeline</h2>
            <p class="text-gray-600">Jadwal penting Automation Week 9.</p>
        </div>
        <div class="timeline-container">
            <div class="timeline-item">
                <div class="timeline-dot" style="background:#bc0301;"></div>
                <div class="timeline-content">
                    <h4 class="font-bold text-gray-900">Pendaftaran & Pengumpulan Abstrak LKTI</h4>
                    <p class="text-sm text-gray-500">22 September – 9 Oktober 2025</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot" style="background:#2563eb;"></div>
                <div class="timeline-content">
                    <h4 class="font-bold text-gray-900">Pendaftaran & Pembayaran FFR</h4>
                    <p class="text-sm text-gray-500">22 September – 27 Oktober 2025</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot" style="background:#059669;"></div>
                <div class="timeline-content">
                    <h4 class="font-bold text-gray-900">Pendaftaran & Pembayaran PLC</h4>
                    <p class="text-sm text-gray-500">22 September – 23 Oktober 2025</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot" style="background:#d97706;"></div>
                <div class="timeline-content">
                    <h4 class="font-bold text-gray-900">Pendaftaran & Pembayaran Line Follower</h4>
                    <p class="text-sm text-gray-500">22 September – 27 Oktober 2025</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot" style="background:#7c3aed;"></div>
                <div class="timeline-content">
                    <h4 class="font-bold text-gray-900">Hari Pelaksanaan Lomba</h4>
                    <p class="text-sm text-gray-500">28 Oktober 2025</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 px-4 bg-gray-50">
    <div class="max-w-6xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Sponsor</h2>
        <p class="text-gray-600 mb-8">Didukung oleh berbagai mitra dan sponsor.</p>
        <div class="flex flex-wrap justify-center gap-8 items-center opacity-60">
            <div class="w-32 h-20 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-sm font-medium">Logo Sponsor</div>
            <div class="w-32 h-20 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-sm font-medium">Logo Sponsor</div>
            <div class="w-32 h-20 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-sm font-medium">Logo Sponsor</div>
            <div class="w-32 h-20 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-sm font-medium">Logo Sponsor</div>
        </div>
    </div>
</section>

<section id="contact" class="py-16 px-4 bg-white">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Kontak</h2>
        <p class="text-gray-600 mb-8">Hubungi kami untuk informasi lebih lanjut.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto">
            <div class="p-6 rounded-xl border border-gray-200 shadow-sm bg-gray-50">
                <i class="fas fa-phone text-2xl mb-3" style="color:#bc0301;"></i>
                <h4 class="font-bold text-gray-800">Contact Person</h4>
                <p class="text-sm text-gray-600 mt-1">+62 812-3456-7890 (Panitia)</p>
            </div>
            <div class="p-6 rounded-xl border border-gray-200 shadow-sm bg-gray-50">
                <i class="fas fa-envelope text-2xl mb-3" style="color:#bc0301;"></i>
                <h4 class="font-bold text-gray-800">Email</h4>
                <p class="text-sm text-gray-600 mt-1">automationweek@ppns.ac.id</p>
            </div>
        </div>
    </div>
</section>

<footer class="py-8 px-4 text-center" style="background:#D4ECDD;">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-center gap-4 mb-4">
            <a href="https://www.youtube.com/channel/UCXepgfxFNcLQcMHgyTrykjw" target="_blank" class="w-10 h-10 rounded-full bg-gray-800 text-white flex items-center justify-center hover:bg-gray-600 transition-colors no-underline"><i class="fab fa-youtube"></i></a>
            <a href="https://www.instagram.com/automationweek/" target="_blank" class="w-10 h-10 rounded-full bg-gray-800 text-white flex items-center justify-center hover:bg-gray-600 transition-colors no-underline"><i class="fab fa-instagram"></i></a>
        </div>
        <p class="text-sm text-gray-600">&copy; 2025 Automation Week 9. HMT Otomasi PPNS.</p>
    </div>
</footer>

<style>
    .marquee-track { display: inline-flex; }
    @keyframes marquee {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    .marquee-track:hover { animation-play-state: paused; }

    .timeline-container {
        position: relative;
        padding-left: 2rem;
    }
    .timeline-container::before {
        content: '';
        position: absolute;
        left: 0.75rem;
        top: 0.5rem;
        bottom: 0.5rem;
        width: 2px;
        background: #e5e7eb;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 2rem;
    }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-dot {
        position: absolute;
        left: -1.35rem;
        top: 0.25rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 0 2px #e5e7eb;
        z-index: 1;
    }
    .timeline-content {
        padding-left: 0.5rem;
    }
    html { scroll-behavior: smooth; }
</style>

<script>
    document.getElementById('menu-toggle')?.addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
    document.querySelectorAll('#mobile-menu a').forEach(function(a) {
        a.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.add('hidden');
        });
    });
</script>

</body>
</html>
