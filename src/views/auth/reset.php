<?php

/** @var string $csrf_token */
/** @var string $token */
/** @var string|null $email */
/** @var string|null $password_error */

$body_class = 'bg-zinc-100 text-black font-sans antialiased min-h-screen';
$main_class = 'mx-auto flex w-full grow flex-col justify-center max-w-3xl min-[1000px]:max-w-[90rem]';
?>
<div class="min-h-screen relative grid grid-cols-1 gap-4 xl:grid-cols-2">
    <div class="relative flex-1 flex flex-col sm:px-12 lg:px-16 py-8 overflow-hidden">
        <div class="absolute left-1/2 -top-10 -translate-x-1/2 w-full h-[70px] bg-primary rounded-b-4xl shadow-sm z-10"></div>
        <div class="flex-1 flex flex-col justify-center items-center relative min-h-0">
            <div class="w-full max-w-md px-8 md:px-0 xl:px-0">
                <a href="/" class="block text-center">
                    <div class="flex justify-center mb-6">
                        <img src="/image/logo-aw.png" alt="automationweek logo" class="h-20 sm:h-28 mix-blend-multiply w-auto select-none">
                    </div>

                    <div class="text-center mb-8">
                        <h1 class="text-2xl sm:text-3xl leading-tighter font-display font-extrabold mb-4">
                            Reset password
                        </h1>
                    </div>
                </a>
                <div class="text-center mb-8">
                    <p class="text-sm sm:text-base">
                        Masukkan password baru Anda.
                    </p>
                </div>

                <div class="border border-white/10 rounded-4xl overflow-hidden bg-[#1e1d1a]">
                    <div class="p-6">

                        <div id="reset-form">
                            <form action="/reset-password" method="POST" class="space-y-4" novalidate>
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

                                <div class="text-center text-sm flex flex-col -mt-2">
                                    <span class="text-gray-100 font-thin -mb-1 text-xs">Reset password untuk akun</span>
                                    <span id="confirm-email" class="text-white font-bold"><?= htmlspecialchars($email ?? '') ?></span>
                                </div>

                                <div>
                                    <label for="password" class="block max-sm:text-xs text-sm font-medium mb-1 text-gray-300">
                                        Password Baru<span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="password"
                                            id="password"
                                            name="password"
                                            placeholder="Minimal 8 karakter"
                                            required
                                            minlength="8"
                                            style="padding-right: 3.5rem"
                                            class="appearance-none block w-full px-3 py-2.5 max-sm:py-2 border rounded-xl shadow-sm
                                               bg-[#2a2926] placeholder-gray-500 text-white
                                               focus:outline-none focus:border-white/40 transition duration-150 ease-in-out sm:text-sm max-sm:text-xs
                                               <?= isset($password_error) ? 'border-red-500 text-red-400' : 'border-white/10' ?>">
                                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white cursor-pointer" data-password-toggle="password"></button>
                                    </div>
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block max-sm:text-xs text-sm font-medium mb-1 text-gray-300">
                                        Konfirmasi Password<span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="password"
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            placeholder="Ulangi password baru"
                                            required
                                            minlength="8"
                                            style="padding-right: 3.5rem"
                                            class="appearance-none block w-full px-3 py-2.5 max-sm:py-2 border rounded-xl shadow-sm
                                               bg-[#2a2926] placeholder-gray-500 text-white
                                               focus:outline-none focus:border-white/40 transition duration-150 ease-in-out sm:text-sm max-sm:text-xs
                                               <?= isset($password_error) ? 'border-red-500 text-red-400' : 'border-white/10' ?>">
                                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white cursor-pointer" data-password-toggle="password_confirmation"></button>
                                    </div>
                                    <p id="password-error" class="mt-1 ml-1 max-sm:text-xs text-sm text-red-400 <?= isset($password_error) ? '' : 'hidden' ?>">
                                        <?= htmlspecialchars($password_error ?? '') ?>
                                    </p>
                                </div>

                                <button
                                    type="submit"
                                    class="w-full flex justify-center py-2.5 max-sm:py-2 px-4 border border-transparent rounded-xl shadow-sm
                                       text-sm max-sm:text-xs font-bold text-[#161512] bg-white hover:bg-gray-200
                                       transition duration-150 ease-in-out transform hover:scale-[1.02] active:scale-95 cursor-pointer
                                       disabled:opacity-50 disabled:hover:bg-white disabled:hover:scale-100 disabled:active:scale-100"
                                    ontouchstart="">
                                    Ubah Password
                                </button>
                            </form>
                        </div>

                        <div id="reset-success" class="hidden text-center pt-8 space-y-4">
                            <div id="success-icon" class="w-16 h-16 mx-auto text-green-400"></div>
                            <h2 class="text-xl font-bold text-white">Password Berhasil Diubah!</h2>
                            <p class="text-gray-400 text-sm">Password Anda telah diubah.<br>Silakan masuk dengan password baru.</p>
                            <div class="flex flex-col gap-2 pt-4">
                                <a href="/login" class="w-full block text-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-[#161512] bg-white hover:bg-gray-200 transition">Kembali ke login</a>
                            </div>
                        </div>

                    </div>
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
    (async () => {
        const [eye, eyeOff, check, lc] = await Promise.all([
            fetch('/icons/eye.svg').then(r => r.text()),
            fetch('/icons/eye-off.svg').then(r => r.text()),
            fetch('/icons/check.svg').then(r => r.text()),
            fetch('/icons/loader-circle.svg').then(r => r.text())
        ]);
        const form = document.querySelector('form[action="/reset-password"]');
        if (form) form.dataset.loaderCircle = lc;
        const icon = document.getElementById('success-icon');
        if (icon) icon.innerHTML = check.replace(/width="\d+"/, '').replace(/height="\d+"/, '');
        document.querySelectorAll('[data-password-toggle]').forEach(btn => {
            btn.innerHTML = eye;
            btn.addEventListener('click', () => {
                const inp = document.getElementById(btn.dataset.passwordToggle);
                inp.type = inp.type === 'password' ? 'text' : 'password';
                btn.innerHTML = inp.type === 'password' ? eye : eyeOff;
            });
        });
    })();

    document.querySelector('form[action="/reset-password"]')?.addEventListener('submit', async function(e) {
        e.preventDefault();

        const errPass = document.getElementById('password-error');
        const inpPass = document.getElementById('password');
        const inpConfirm = document.getElementById('password_confirmation');

        errPass?.classList.add('hidden');
        inpPass?.classList.remove('border-red-500', 'text-red-400');
        inpPass?.classList.add('border-white/10');
        inpConfirm?.classList.remove('border-red-500', 'text-red-400');
        inpConfirm?.classList.add('border-white/10');

        const passVal = inpPass.value;
        if (!passVal) {
            errPass.textContent = 'password wajib diisi!';
            errPass.classList.remove('hidden');
            inpPass.classList.remove('border-white/10');
            inpPass.classList.add('border-red-500', 'text-red-400');
            return;
        } else if (passVal.length < 8) {
            errPass.textContent = 'password minimal 8 karakter!';
            errPass.classList.remove('hidden');
            inpPass.classList.remove('border-white/10');
            inpPass.classList.add('border-red-500', 'text-red-400');
            return;
        } else if (passVal !== inpConfirm.value) {
            errPass.textContent = 'konfirmasi password tidak sama!';
            errPass.classList.remove('hidden');
            inpPass.classList.remove('border-white/10');
            inpPass.classList.add('border-red-500', 'text-red-400');
            return;
        }

        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        const originalText = btn.textContent;
        btn.innerHTML = this.dataset.loaderCircle;
        try {
            const res = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: new FormData(this)
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('reset-form').classList.add('hidden');
                document.getElementById('reset-success').classList.remove('hidden');
            } else {
                for (const [k, v] of Object.entries(data.errors || {})) {
                    if (k === 'error') {
                        errPass.textContent = v;
                        errPass.classList.remove('hidden');
                        inpPass.classList.remove('border-white/10');
                        inpPass.classList.add('border-red-500', 'text-red-400');
                        continue;
                    }
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
                btn.disabled = false;
                btn.textContent = originalText;
            }
        } catch (e) {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });
</script>