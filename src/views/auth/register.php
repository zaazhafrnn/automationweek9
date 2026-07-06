<?php

/** @var string $csrf_token */
/** @var string|null $error */
/** @var string|null $old_name */
/** @var string|null $old_email */
/** @var string|null $old_password */
/** @var string|null $old_confirm_password */
/** @var string|null $name_error */
/** @var string|null $email_error */
/** @var string|null $password_error */
/** @var string|null $confirm_password_error */

$body_class = 'bg-zinc-100 text-black font-sans antialiased min-h-screen';
$main_class = 'mx-auto flex w-full grow flex-col justify-center max-w-3xl min-[1000px]:max-w-[90rem]';
?>

<div class="min-h-screen relative grid grid-cols-1 gap-4 xl:grid-cols-2">
    <div class="flex-1 flex flex-col sm:px-12 lg:px-16 py-8">
        <div class="flex items-center gap-2">
            <span class="navbar-logo">
                <img
                    src="/image/logo-aw.png"
                    alt="automationweek logo"
                    class="h-20 mix-blend-multiply w-auto select-none pointer-events-none">
            </span>
        </div>

        <div class="flex-1 flex flex-col justify-center items-center">
            <div class="w-full max-w-md">

                <div class="text-center mb-8">
                    <h1 class="text-4xl sm:text-5xl font-serif leading-tight mb-4">
                        AutomationWeek IX
                    </h1>
                    <p class="text-sm sm:text-base">
                        Buat akun untuk memulai pendaftaran,
                        bergabung dengan tim, dan mengikuti kompetisi.
                    </p>
                </div>

                <div class="flex justify-center bg-red-600/10 -mb-6 pb-6 rounded-t-4xl border border-red-500">
                    <div class="inline-flex items-center gap-2 px-3 py-4 text-xs">
                        <span class="text-[#bc0301] font-semibold">
                            Pendaftaran lomba dibuka
                        </span>
                        <span>20 Agustus - 20 Oktober 2026!</span>
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
                            <form action="/register" method="POST" class="space-y-4">
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                                <div id="step-1-fields" class="space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium mb-1 text-gray-300">
                                            Nama Lengkap<span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="name"
                                            name="name"
                                            placeholder="Masukkan nama lengkap"
                                            required
                                            value="<?= htmlspecialchars($old_name ?? '') ?>"
                                            class="appearance-none block w-full px-3 py-2.5 border rounded-xl shadow-sm
                                                   bg-[#2a2926] placeholder-gray-500 text-white
                                                   focus:outline-none focus:border-white/40 transition duration-150 ease-in-out sm:text-sm
                                                   <?= isset($name_error) ? 'border-red-500 text-red-400' : 'border-white/10' ?>"
                                            oninput="
                                                this.classList.remove('border-red-500', 'text-red-400');
                                                this.classList.add('border-white/10');
                                                document.getElementById('name-error')?.classList.add('hidden');
                                            ">
                                        <p id="name-error" class="mt-1 ml-1 text-sm text-red-400 <?= isset($name_error) ? '' : 'hidden' ?>">
                                            <?= htmlspecialchars($name_error ?? '') ?>
                                        </p>
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium mb-1 text-gray-300">
                                            Email<span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            placeholder="Masukkan email"
                                            required
                                            value="<?= htmlspecialchars($old_email ?? '') ?>"
                                            class="appearance-none block w-full px-3 py-2.5 border rounded-xl shadow-sm
                                                   bg-[#2a2926] placeholder-gray-500 text-white
                                                   focus:outline-none focus:border-white/40 transition duration-150 ease-in-out sm:text-sm
                                                   <?= isset($email_error) ? 'border-red-500 text-red-400' : 'border-white/10' ?>"
                                            oninput="
                                                 this.classList.remove('border-red-500', 'text-red-400');
                                                 this.classList.add('border-white/10');
                                                 document.getElementById('email-error')?.classList.add('hidden');
                                                 this.dataset.rejected = '';
                                             ">
                                        <p id="email-error" class="mt-1 ml-1 text-sm text-red-400 <?= isset($email_error) ? '' : 'hidden' ?>">
                                            <?= htmlspecialchars($email_error ?? '') ?>
                                        </p>
                                    </div>
                                </div>

                                <div id="step-2-fields" class="space-y-4 hidden">
                                    <div class="text-center text-sm flex flex-col">
                                        <span class="text-gray-100 font-thin -mb-1">Daftar untuk akun</span>
                                        <span id="confirm-email" class="text-white font-medium"></span>
                                    </div>
                                    <div>
                                        <label for="password" class="block text-sm font-medium mb-1 text-gray-300">
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
                                                value="<?= htmlspecialchars($old_password ?? '') ?>"
                                                style="padding-right: 3.5rem"
                                                class="appearance-none block w-full px-3 py-2.5 border rounded-xl shadow-sm
                                                       bg-[#2a2926] placeholder-gray-500 text-white
                                                       focus:outline-none focus:border-white/40 transition duration-150 ease-in-out sm:text-sm
                                                       <?= isset($password_error) ? 'border-red-500 text-red-400' : 'border-white/10' ?>"
                                                oninput="
                                                    this.classList.remove('border-red-500', 'text-red-400');
                                                    this.classList.add('border-white/10');
                                                    document.getElementById('password-error')?.classList.add('hidden');
                                                ">
                                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white cursor-pointer" data-password-toggle="password"></button>
                                        </div>
                                        <p id="password-error" class="mt-1 ml-1 text-sm text-red-400 <?= isset($password_error) ? '' : 'hidden' ?>">
                                            <?= htmlspecialchars($password_error ?? '') ?>
                                        </p>
                                    </div>
                                    <div>
                                        <label for="confirm_password" class="block text-sm font-medium mb-1 text-gray-300">
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
                                                value="<?= htmlspecialchars($old_confirm_password ?? '') ?>"
                                                style="padding-right: 3.5rem"
                                                class="appearance-none block w-full px-3 py-2.5 border rounded-xl shadow-sm
                                                       bg-[#2a2926] placeholder-gray-500 text-white
                                                       focus:outline-none focus:border-white/40 transition duration-150 ease-in-out sm:text-sm
                                                       <?= isset($confirm_password_error) ? 'border-red-500 text-red-400' : 'border-white/10' ?>"
                                                oninput="
                                                    this.classList.remove('border-red-500', 'text-red-400');
                                                    this.classList.add('border-white/10');
                                                    document.getElementById('confirm-password-error')?.classList.add('hidden');
                                                ">
                                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white cursor-pointer" data-password-toggle="confirm_password"></button>
                                        </div>
                                        <p id="confirm-password-error" class="mt-1 ml-1 text-sm text-red-400 <?= isset($confirm_password_error) ? '' : 'hidden' ?>">
                                            <?= htmlspecialchars($confirm_password_error ?? '') ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <button
                                        type="button"
                                        id="action-btn"
                                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm
                                               text-sm font-bold text-[#161512] bg-white hover:bg-gray-200
                                               transition duration-150 ease-in-out transform hover:scale-[1.02] active:scale-95 cursor-pointer">
                                        Lanjutkan
                                    </button>
                                    <button
                                        type="button"
                                        id="back-btn"
                                        class="hidden w-full flex justify-center py-2 px-4 border border-white/50 rounded-xl shadow-sm
                                               text-sm font-medium text-gray-400 hover:bg-white/20 hover:text-white
                                               transition duration-150 ease-in-out cursor-pointer">
                                        Kembali ke sebelumnya
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div id="register-success" class="hidden text-center py-8 space-y-4">
                            <svg class="w-16 h-16 mx-auto text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 12l2 2 4-4" />
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                            <h2 class="text-xl font-bold text-white">Pendaftaran Berhasil!</h2>
                            <p class="text-gray-400 text-sm">Akun untuk <span id="success-email" class="text-white font-semibold"></span> berhasil dibuat!<br>Silakan masuk untuk melanjutkan pendaftaran.</p>
                            <div class="flex flex-col gap-2 pt-4">
                                <a href="/login" class="w-full block text-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-[#161512] bg-white hover:bg-gray-200 transition">Masuk</a>
                                <a href="/register" class="w-full block text-center py-2 px-4 border border-white/10 rounded-xl shadow-sm text-sm font-medium text-gray-400 hover:text-white transition">Daftar Akun Lain</a>
                            </div>
                        </div>

                        <div id="register-error" class="hidden text-center py-8 space-y-4">
                            <svg class="w-16 h-16 mx-auto text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="15" y1="9" x2="9" y2="15" />
                                <line x1="9" y1="9" x2="15" y2="15" />
                            </svg>
                            <h2 class="text-xl font-bold text-white">Email Sudah Terdaftar</h2>
                            <p class="text-gray-400 text-sm">Gunakan email lain untuk mendaftar akun baru.</p>
                            <div class="flex flex-col gap-2 pt-4">
                                <button type="button" id="retry-btn" class="w-full block text-center py-2.5 px-4 border border-white/50 rounded-xl shadow-sm text-sm font-medium text-gray-400 hover:text-white hover:bg-white/20 transition cursor-pointer">Gunakan Email Lain</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-sm">
                        Sudah punya akun?
                        <a
                            href="/login"
                            class="inline-block font-semibold text-[#bc0301]
                                   transition-all duration-100 ease-out">
                            Login
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <div class="hidden lg:flex justify-self-start items-center w-full">
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
            step = 1;
            step1.classList.remove('hidden');
            step2.classList.add('hidden');
            backBtn.classList.add('hidden');
            actionBtn.textContent = 'Lanjutkan';
        }

        backBtn.addEventListener('click', goToStep1);
        document.getElementById('retry-btn')?.addEventListener('click', () => {
            document.getElementById('register-error').classList.add('hidden');
            document.getElementById('register-form').classList.remove('hidden');
            goToStep1();
            actionBtn.disabled = false;
            actionBtn.textContent = 'Lanjutkan';
            showFieldError(errEmail, 'Email sudah terdaftar');
        });

        form.addEventListener('keydown', e => {
            if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
                e.preventDefault();
                actionBtn.click();
            }
        });

        actionBtn.addEventListener('click', async () => {
            clearErrors();

            if (step === 1) {
                let err = false;
                if (!nameInput.value.trim()) {
                    showFieldError(errName, 'nama wajib diisi!');
                    err = true;
                }
                if (!emailInput.value.trim()) {
                    showFieldError(errEmail, 'email wajib diisi!');
                    err = true;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
                    showFieldError(errEmail, 'email harus berupa "email@mail.com"');
                    err = true;
                } else if (emailInput.dataset.rejected === 'true') {
                    showFieldError(errEmail, 'Email sudah terdaftar');
                    err = true;
                }
                if (err) return;

                step = 2;
                document.getElementById('confirm-email').textContent = emailInput.value.trim();
                step1.classList.add('hidden');
                step2.classList.remove('hidden');
                backBtn.classList.remove('hidden');
                actionBtn.textContent = 'Daftar';
                return;
            }

            // step 2 validation
            let err = false;
            if (!passInput.value) {
                showFieldError(errPass, 'password wajib diisi!');
                err = true;
            } else if (passInput.value.length < 8) {
                showFieldError(errPass, 'password minimal 8 karakter!');
                err = true;
            }
            if (!confirmInput.value) {
                showFieldError(errConfirm, 'konfirmasi password wajib diisi!');
                err = true;
            } else if (passInput.value !== confirmInput.value) {
                showFieldError(errConfirm, 'konfirmasi password tidak cocok!');
                err = true;
            }
            if (err) return;

            actionBtn.disabled = true;
            const originalText = actionBtn.textContent;
            actionBtn.textContent = 'Memproses...';

            try {
                const fd = new FormData();
                fd.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
                fd.append('name', nameInput.value.trim());
                fd.append('email', emailInput.value.trim());
                fd.append('password', passInput.value);

                const res = await fetch('/register', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: fd
                });
                const data = await res.json();
                if (data.success) {
                    document.getElementById('register-form').classList.add('hidden');
                    document.getElementById('success-email').textContent = emailInput.value.trim();
                    document.getElementById('register-success').classList.remove('hidden');
                } else if (data.errors?.email_error) {
                    document.getElementById('register-form').classList.add('hidden');
                    document.getElementById('register-error').classList.remove('hidden');
                    emailInput.dataset.rejected = 'true';
                    actionBtn.disabled = false;
                    actionBtn.textContent = originalText;
                } else {
                    for (const [k, v] of Object.entries(data.errors || {})) {
                        const el = document.getElementById(k.replace(/_error$/, '-error'));
                        if (el) {
                            el.textContent = v;
                            el.classList.remove('hidden');
                            const inp = document.getElementById(k.replace(/_error$/, ''));
                            if (inp) {
                                inp.classList.remove('border-white/10');
                                inp.classList.add('border-red-500', 'text-red-400');
                            }
                        }
                    }
                    if (data.errors?.name_error || data.errors?.email_error) goToStep1();
                    actionBtn.disabled = false;
                    actionBtn.textContent = originalText;
                }
            } catch (e) {
                actionBtn.disabled = false;
                actionBtn.textContent = originalText;
            }
        });
    }
</script>