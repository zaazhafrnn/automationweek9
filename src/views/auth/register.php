<?php

/** @var string $csrf_token */
/** @var string|null $error */
/** @var string|null $old_name */
/** @var string|null $old_email */


/** @var string|null $name_error */
/** @var string|null $email_error */
/** @var string|null $password_error */
/** @var string|null $confirm_password_error */

$body_class = 'bg-zinc-100 text-black font-sans antialiased min-h-screen';
$main_class = 'mx-auto flex w-full grow flex-col justify-center max-w-3xl min-[1000px]:max-w-[90rem]';
?>
<div class="min-h-screen relative grid grid-cols-1 gap-4 xl:grid-cols-2">
    <div class="relative flex-1 flex flex-col sm:px-12 lg:px-16 py-8 overflow-hidden">
        <div class="absolute left-1/2 -top-10 -translate-x-1/2 w-full h-[70px] bg-primary rounded-b-4xl shadow-sm z-10"></div>
        <div class="flex-1 flex flex-col justify-center items-center relative min-h-0">
            <div class="w-full max-w-md px-8 md:px-0 xl:px-0">

                <div class="flex justify-center mb-6">
                    <img src="/image/logo-aw.png" alt="automationweek logo" class="h-20 sm:h-28 mix-blend-multiply w-auto select-none pointer-events-none">
                </div>

                <div class="text-center mb-8">
                    <h1 class="text-2xl sm:text-3xl leading-tighter font-display font-extrabold mb-4">
                        AUTOMATION WEEK IX
                    </h1>
                    <p class="text-sm sm:text-base">
                        Buat akun untuk memulai pendaftaran,
                        bergabung dengan tim, dan mengikuti kompetisi.
                    </p>
                </div>

                <div class="flex justify-center bg-red-600/30 -mb-6 pb-6 rounded-t-4xl border border-red-500">
                    <div class="flex flex-col sm:flex-row items-center gap-1 sm:gap-2 px-3 py-2 sm:py-4 text-xs">
                        <span class="text-brand font-semibold">
                            Pendaftaran lomba dibuka
                        </span>
                        <span>24 Agustus - 1 Oktober 2026</span>
                    </div>
                </div>

                <div class="border border-white/10 rounded-4xl overflow-hidden bg-[#1e1d1a]">
                    <div class="p-6">

                        <?php if (isset($error)): ?>
                            <div id="error" class="mb-4 rounded-xl border border-red-500 bg-red-600/10 p-3 text-sm text-red-400">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <div id="register-form">
                            <form action="/register" method="POST" class="space-y-4" novalidate>
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                                <div id="step-1-fields" class="space-y-4">
                                    <div>
                                        <label for="name" class="block max-sm:text-xs text-sm font-medium mb-1 text-gray-300">
                                            Nama Lengkap<span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="name"
                                            name="name"
                                            placeholder="Masukkan nama lengkap"
                                            required
                                            value="<?= htmlspecialchars($old_name ?? '') ?>"
                                            class="appearance-none block w-full px-3 py-2.5 max-sm:py-2 border rounded-xl shadow-sm
                                                   bg-[#2a2926] placeholder-gray-500 text-white
                                                   focus:outline-none focus:border-white/40 transition duration-150 ease-in-out sm:text-sm max-sm:text-xs
                                                   <?= isset($name_error) ? 'border-red-500 text-red-400' : 'border-white/10' ?>"
                                            oninput="
                                                this.classList.remove('border-red-500', 'text-red-400');
                                                this.classList.add('border-white/10');
                                                document.getElementById('name-error')?.classList.add('hidden');
                                            ">
                                        <p id="name-error" class="mt-1 ml-1 max-sm:text-xs text-sm text-red-400 <?= isset($name_error) ? '' : 'hidden' ?>">
                                            <?= htmlspecialchars($name_error ?? '') ?>
                                        </p>
                                    </div>
                                    <div>
                                        <label for="email" class="block max-sm:text-xs text-sm font-medium mb-1 text-gray-300">
                                            Email<span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            placeholder="Masukkan email"
                                            required
                                            value="<?= htmlspecialchars($old_email ?? '') ?>"
                                            class="appearance-none block w-full px-3 py-2.5 max-sm:py-2 border rounded-xl shadow-sm
                                                   bg-[#2a2926] placeholder-gray-500 text-white
                                                   focus:outline-none focus:border-white/40 transition duration-150 ease-in-out sm:text-sm max-sm:text-xs
                                                   <?= isset($email_error) ? 'border-red-500 text-red-400' : 'border-white/10' ?>"
                                            oninput="
                                                 this.classList.remove('border-red-500', 'text-red-400');
                                                 this.classList.add('border-white/10');
                                                 document.getElementById('email-error')?.classList.add('hidden');
                                                 this.dataset.rejected = '';
                                             ">
                                        <p id="email-error" class="mt-1 ml-1 max-sm:text-xs text-sm text-red-400 <?= isset($email_error) ? '' : 'hidden' ?>">
                                            <?= htmlspecialchars($email_error ?? '') ?>
                                        </p>
                                    </div>
                                </div>

                                <div id="step-2-fields" class="space-y-4 hidden">
                                    <div class="text-center text-sm flex flex-col -mt-2">
                                        <span class="text-gray-100 font-thin -mb-1 text-xs">Daftar untuk akun</span>
                                        <span id="confirm-email" class="text-white font-bold"></span>
                                    </div>
                                    <div>
                                        <label for="password" class="block max-sm:text-xs text-sm font-medium mb-1 text-gray-300">
                                            Password<span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input
                                                type="password"
                                                id="password"
                                                name="password"
                                                placeholder="Masukkan password"
                                                required
                                                minlength="8"
                                                style="padding-right: 3.5rem"
                                                class="appearance-none block w-full px-3 py-2.5 max-sm:py-2 border rounded-xl shadow-sm
                                                       bg-[#2a2926] placeholder-gray-500 text-white
                                                       focus:outline-none focus:border-white/40 transition duration-150 ease-in-out sm:text-sm max-sm:text-xs
                                                       <?= isset($password_error) ? 'border-red-500 text-red-400' : 'border-white/10' ?>"
                                                oninput="
                                                    this.classList.remove('border-red-500', 'text-red-400');
                                                    this.classList.add('border-white/10');
                                                    document.getElementById('password-error')?.classList.add('hidden');
                                                ">
                                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white cursor-pointer" data-password-toggle="password"></button>
                                        </div>
                                        <p id="password-error" class="mt-1 ml-1 max-sm:text-xs text-sm text-red-400 <?= isset($password_error) ? '' : 'hidden' ?>">
                                            <?= htmlspecialchars($password_error ?? '') ?>
                                        </p>
                                    </div>
                                    <div>
                                        <label for="confirm_password" class="block max-sm:text-xs text-sm font-medium mb-1 text-gray-300">
                                            Konfirmasi Password<span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input
                                                type="password"
                                                id="confirm_password"
                                                name="confirm_password"
                                                placeholder="Masukkan kembali password"
                                                required
                                                minlength="8"
                                                style="padding-right: 3.5rem"
                                                class="appearance-none block w-full px-3 py-2.5 max-sm:py-2 border rounded-xl shadow-sm
                                                       bg-[#2a2926] placeholder-gray-500 text-white
                                                       focus:outline-none focus:border-white/40 transition duration-150 ease-in-out sm:text-sm max-sm:text-xs
                                                       <?= isset($confirm_password_error) ? 'border-red-500 text-red-400' : 'border-white/10' ?>"
                                                oninput="
                                                    this.classList.remove('border-red-500', 'text-red-400');
                                                    this.classList.add('border-white/10');
                                                    document.getElementById('confirm-password-error')?.classList.add('hidden');
                                                ">
                                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white cursor-pointer" data-password-toggle="confirm_password"></button>
                                        </div>
                                        <p id="confirm-password-error" class="mt-1 ml-1 max-sm:text-xs text-sm text-red-400 <?= isset($confirm_password_error) ? '' : 'hidden' ?>">
                                            <?= htmlspecialchars($confirm_password_error ?? '') ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <button
                                        type="button"
                                        id="action-btn"
                                        onclick="handleRegister(event)"
                                        class="w-full flex justify-center py-2.5 max-sm:py-2 px-4 border border-transparent rounded-xl shadow-sm
                                               text-sm max-sm:text-xs font-bold text-[#161512] bg-white hover:bg-gray-200
                                               transition duration-150 ease-in-out transform hover:scale-[1.02] active:scale-95 cursor-pointer
                                               disabled:opacity-50 disabled:hover:bg-white disabled:hover:scale-100 disabled:active:scale-100" ontouchstart="" style="touch-action: manipulation">
                                        Lanjutkan
                                    </button>
                                    <button
                                        type="button"
                                        id="back-btn"
                                        class="hidden w-full flex justify-center py-2 max-sm:py-1.5 px-4 border border-white/50 rounded-xl shadow-sm
                                               text-sm max-sm:text-xs font-medium text-gray-400 hover:bg-white/20 hover:text-white
                                               transition duration-150 ease-in-out cursor-pointer">
                                        Kembali ke sebelumnya
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div id="register-success" class="hidden text-center pt-8 space-y-4">
                            <div id="success-icon" class="w-16 h-16 mx-auto text-green-400"></div>
                            <h2 class="text-xl font-bold text-white">Pendaftaran Berhasil!</h2>
                            <p class="text-gray-400 text-sm">Akun untuk <span id="success-email" class="text-white font-semibold"></span> berhasil dibuat!<br>Silakan masuk untuk melanjutkan pendaftaran.</p>
                            <div class="flex flex-col gap-2 pt-4">
                                <a href="/login" class="w-full block text-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-[#161512] bg-white hover:bg-gray-200 transition">Masuk</a>
                                <!-- <a href="/register" class="w-full block text-center py-2 px-4 border border-white/10 rounded-xl shadow-sm text-sm font-medium text-gray-400 hover:text-white transition">Daftar Akun Lain</a> -->
                            </div>
                        </div>

                        <div id="register-error" class="hidden text-center pt-8 space-y-4">
                            <div id="error-icon" class="w-16 h-16 mx-auto text-red-400"></div>
                            <h2 class="text-xl font-bold text-white">Email Sudah Terdaftar</h2>
                            <p class="text-gray-400 text-sm">Gunakan email lain untuk mendaftar akun baru.</p>
                            <div class="flex flex-col gap-2 pt-4">
                                <button type="button" id="retry-btn" class="w-full block text-center py-2.5 px-4 border border-white/50 rounded-xl shadow-sm text-sm font-medium text-gray-400 hover:text-white hover:bg-white/20 transition cursor-pointer">Gunakan Email Lain</button>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="mt-2 text-center">
                    <p class="max-sm:text-xs text-sm">
                        Sudah punya akun?
                        <a
                            href="/login"
                            class="inline-block font-semibold text-brand
                                   transition-all duration-100 ease-out">
                            Login
                        </a>
                    </p>
                </div>

            </div>
        </div>
        <div class="absolute left-1/2 -bottom-10 -translate-x-1/2 w-full h-[70px] bg-primary rounded-t-4xl shadow-sm z-10"></div>
    </div>

    <div class="hidden xl:flex justify-self-start items-center w-full">
        <div class="flex justify-self-start rounded-2xl w-full max-w-xl xl:max-w-none xl:aspect-auto xl:h-[85vh] xl:min-h-[500px] items-center overflow-hidden mt-8 ml-0 mr-auto">
            <video
                class="w-full h-full object-cover"
                src="/video/preview.mov"
                autoplay
                muted
                loop
                playsinline
                poster="/image/preview-poster.jpg">
            </video>
        </div>
    </div>
</div>

<script>
    const form = document.querySelector('form[action="/register"]');
    if (form) {
        (async () => {
            const [eye, eyeOff, lc, ur, urx] = await Promise.all([
                fetch('/icons/eye.svg').then(r => r.text()),
                fetch('/icons/eye-off.svg').then(r => r.text()),
                fetch('/icons/loader-circle.svg').then(r => r.text()),
                fetch('/icons/user-round.svg').then(r => r.text()),
                fetch('/icons/user-round-x.svg').then(r => r.text())
            ]);
            form.dataset.loaderCircle = lc;
            form.dataset.userRound = ur.replace(/width="\d+"/, '').replace(/height="\d+"/, '');
            form.dataset.userRoundX = urx.replace(/width="\d+"/, '').replace(/height="\d+"/, '');
            document.querySelectorAll('[data-password-toggle]').forEach(btn => {
                btn.innerHTML = eye;
                btn.addEventListener('click', () => {
                    const inp = document.getElementById(btn.dataset.passwordToggle);
                    inp.type = inp.type === 'password' ? 'text' : 'password';
                    btn.innerHTML = inp.type === 'password' ? eye : eyeOff;
                });
            });
        })();

        const step1 = document.getElementById('step-1-fields');
        const step2 = document.getElementById('step-2-fields');
        const actionBtn = document.getElementById('action-btn');
        const backBtn = document.getElementById('back-btn');

        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const passInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');

        const errName = document.getElementById('name-error');
        const errEmail = document.getElementById('email-error');
        const errPass = document.getElementById('password-error');
        const errConfirm = document.getElementById('confirm-password-error');

        let step = 1;

        function inputFor(errEl) {
            return document.getElementById(errEl.id.replace(/-error$/, '').replace(/-/g, '_'));
        }

        function clearErrors() {
            [errName, errEmail, errPass, errConfirm].forEach(el => {
                if (el) {
                    el.classList.add('hidden');
                    inputFor(el)?.classList.remove('border-red-500', 'text-red-400');
                    inputFor(el)?.classList.add('border-white/10');
                }
            });
        }

        function showFieldError(errEl, msg) {
            if (!errEl) return;
            errEl.textContent = msg;
            errEl.classList.remove('hidden');
            const inp = inputFor(errEl);
            if (inp) {
                inp.classList.remove('border-white/10');
                inp.classList.add('border-red-500', 'text-red-400');
            }
        }

        function goToStep1() {
            window.__regStep = 1;
            var s1 = document.getElementById('step-1-fields');
            var s2 = document.getElementById('step-2-fields');
            var bb = document.getElementById('back-btn');
            var ab = document.getElementById('action-btn');
            if (s1) s1.classList.remove('hidden');
            if (s2) s2.classList.add('hidden');
            if (bb) bb.classList.add('hidden');
            if (ab) ab.textContent = 'Lanjutkan';
        }

        backBtn.addEventListener('click', goToStep1);
        document.getElementById('retry-btn')?.addEventListener('click', function() {
            document.getElementById('register-error').classList.add('hidden');
            document.getElementById('register-form').classList.remove('hidden');
            goToStep1();
            var ab = document.getElementById('action-btn');
            if (ab) ab.disabled = false;
            var ee = document.getElementById('email-error');
            var ei = document.getElementById('email');
            if (ee) {
                ee.textContent = 'Email sudah terdaftar';
                ee.classList.remove('hidden');
            }
            if (ei) {
                ei.classList.remove('border-white/10');
                ei.classList.add('border-red-500', 'text-red-400');
            }
        });

        form.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
                e.preventDefault();
                handleRegister(e);
            }
        });
    }

    window.handleRegister = async function(e) {
        if (e && e.preventDefault) e.preventDefault();
        var f = document.querySelector('form[action="/register"]');
        if (!f) return;

        var s1 = document.getElementById('step-1-fields');
        var s2 = document.getElementById('step-2-fields');
        var ab = document.getElementById('action-btn');
        var bb = document.getElementById('back-btn');
        var ni = document.getElementById('name');
        var ei = document.getElementById('email');
        var pi = document.getElementById('password');
        var ci = document.getElementById('confirm_password');
        var en = document.getElementById('name-error');
        var ee = document.getElementById('email-error');
        var ep = document.getElementById('password-error');
        var ec = document.getElementById('confirm-password-error');
        var ce = document.getElementById('confirm-email');

        var errs = [{
                el: en,
                inp: ni,
                msg: 'nama wajib diisi!'
            },
            {
                el: ee,
                inp: ei,
                msg: 'email wajib diisi!'
            },
            {
                el: ep,
                inp: pi,
                msg: 'password wajib diisi!'
            },
            {
                el: ec,
                inp: ci,
                msg: 'konfirmasi password wajib diisi!'
            }
        ];

        function clr() {
            errs.forEach(function(x) {
                if (x.el) {
                    x.el.classList.add('hidden');
                }
                if (x.inp) {
                    x.inp.classList.remove('border-red-500', 'text-red-400');
                    x.inp.classList.add('border-white/10');
                }
            });
        }

        function shw(errEl, inp, msg) {
            if (errEl) {
                errEl.textContent = msg;
                errEl.classList.remove('hidden');
            }
            if (inp) {
                inp.classList.remove('border-white/10');
                inp.classList.add('border-red-500', 'text-red-400');
            }
        }

        clr();

        if (window.__regStep === undefined) window.__regStep = 1;

        if (window.__regStep === 1) {
            var err = false;
            if (!ni || !ni.value.trim()) {
                shw(en, ni, 'nama wajib diisi!');
                err = true;
            }
            if (!ei || !ei.value.trim()) {
                shw(ee, ei, 'email wajib diisi!');
                err = true;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(ei.value.trim())) {
                shw(ee, ei, 'email harus berupa "email@mail.com"');
                err = true;
            } else if (ei.dataset.rejected === 'true') {
                shw(ee, ei, 'Email sudah terdaftar');
                err = true;
            }
            if (err) return;

            window.__regStep = 2;
            if (ce) ce.textContent = (ei ? ei.value.trim() : '');
            if (s1) s1.classList.add('hidden');
            if (s2) s2.classList.remove('hidden');
            if (bb) bb.classList.remove('hidden');
            if (ab) ab.textContent = 'Daftar';
            return;
        }

        var err = false;
        if (!pi || !pi.value) {
            shw(ep, pi, 'password wajib diisi!');
            err = true;
        } else if (pi.value.length < 8) {
            shw(ep, pi, 'password minimal 8 karakter!');
            err = true;
        }
        if (!ci || !ci.value) {
            shw(ec, ci, 'konfirmasi password wajib diisi!');
            err = true;
        } else if (pi.value !== ci.value) {
            shw(ec, ci, 'password tidak cocok!');
            err = true;
        }
        if (err) return;

        if (ab) ab.disabled = true;
        var origText = ab ? ab.textContent : '';
        if (ab) ab.innerHTML = f.dataset.loaderCircle || '...';

        try {
            var fd = new FormData();
            fd.append('csrf_token', (document.querySelector('input[name="csrf_token"]') || {}).value || '');
            fd.append('name', ni ? ni.value.trim() : '');
            fd.append('email', ei ? ei.value.trim() : '');
            fd.append('password', pi ? pi.value : '');

            var res = await fetch('/register', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: fd
            });
            var data = await res.json();
            if (data.success) {
                var rf = document.getElementById('register-form');
                var si = document.getElementById('success-icon');
                var se = document.getElementById('success-email');
                var rs = document.getElementById('register-success');
                if (rf) rf.classList.add('hidden');
                if (si) si.innerHTML = f.dataset.userRound || '';
                if (se) se.textContent = ei ? ei.value.trim() : '';
                if (rs) rs.classList.remove('hidden');
            } else if (data.errors && data.errors.email_error) {
                var rf2 = document.getElementById('register-form');
                var ei2 = document.getElementById('error-icon');
                var re2 = document.getElementById('register-error');
                if (rf2) rf2.classList.add('hidden');
                if (ei2) ei2.innerHTML = f.dataset.userRoundX || '';
                if (re2) re2.classList.remove('hidden');
                if (ei) ei.dataset.rejected = 'true';
                if (ab) {
                    ab.disabled = false;
                    ab.textContent = origText;
                }
            } else {
                for (var k in data.errors) {
                    var el = document.getElementById(k.replace(/_error$/, '-error'));
                    if (el) {
                        el.textContent = data.errors[k];
                        el.classList.remove('hidden');
                        var inp = document.getElementById(k.replace(/_error$/, ''));
                        if (inp) {
                            inp.classList.remove('border-white/10');
                            inp.classList.add('border-red-500', 'text-red-400');
                        }
                    }
                }
                if (data.errors && (data.errors.name_error || data.errors.email_error)) {
                    window.__regStep = 1;
                    if (s1) s1.classList.remove('hidden');
                    if (s2) s2.classList.add('hidden');
                    if (bb) bb.classList.add('hidden');
                    if (ab) ab.textContent = 'Lanjutkan';
                }
                if (ab) {
                    ab.disabled = false;
                    ab.textContent = origText;
                }
            }
        } catch (e) {
            if (ab) {
                ab.disabled = false;
                ab.textContent = origText;
            }
        }
    };
</script>
