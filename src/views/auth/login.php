<?php

/** @var string $csrf_token */
/** @var string|null $old_email */
/** @var string|null $old_password */
/** @var string|null $email_error */
/** @var string|null $password_error */
$body_class = 'bg-zinc-100 text-black font-sans antialiased min-h-screen';
$main_class = 'mx-auto flex w-full grow flex-col justify-center max-w-3xl min-[1000px]:max-w-[90rem]';
?>
<div class="min-h-screen relative grid grid-cols-1 gap-4 xl:grid-cols-2">
    <div class="flex-1 flex flex-col sm:px-12 lg:px-16 py-8">
        <div class="flex items-center gap-2">
            <span class="navbar-logo">
                <img src="/image/logo-aw.png" alt="automationweek logo" class="h-28 mix-blend-multiply w-auto select-none pointer-events-none">
            </span>
        </div>

        <div class="flex-1 flex flex-col justify-center items-center">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <h1 class="text-4xl sm:text-5xl font-serif leading-tight mb-4">
                        AutomationWeek IX
                    </h1>
                    <p class="text-sm sm:text-base">
                        Masuk untuk melanjutkan pendaftaran, memantau progres tim,
                        dan mengakses materi kompetisi Anda.
                    </p>
                </div>

                <div class="flex justify-center bg-red-600/10 -mb-6 pb-6 rounded-t-4xl border border-red-500">
                    <div class="inline-flex items-center gap-2 px-3 py-4 text-xs">
                        <span class="text-[#bc0301] font-semibold">Pendaftaran lomba dibuka</span>
                        <span>20 Agustus - 20 Oktober 2026</span>
                    </div>
                </div>

                <div class="border border-white/10 rounded-4xl overflow-hidden bg-[#1e1d1a]">
                    <div class="p-6">
                        <form action="/login" method="POST" class="space-y-4" novalidate>
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

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
                                    ">
                                <p id="email-error" class="mt-1 ml-1 text-sm text-red-400 <?= isset($email_error) ? '' : 'hidden' ?>">
                                    <?= htmlspecialchars($email_error ?? '') ?>
                                </p>
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
                                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white text-xs cursor-pointer" data-password-toggle="password">show</button>
                                </div>
                                <p id="password-error" class="mt-1 ml-1 text-sm text-red-400 <?= isset($password_error) ? '' : 'hidden' ?>">
                                    <?= htmlspecialchars($password_error ?? '') ?>
                                </p>
                            </div>

                            <button
                                type="submit"
                                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm
                                       text-sm font-bold text-[#161512] bg-white hover:bg-gray-200
                                       transition duration-150 ease-in-out transform hover:scale-[1.02] active:scale-95 cursor-pointer">
                                Login
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-sm">
                        Belum punya akun?
                        <a
                            href="/register"
                            class="inline-block font-semibold text-[#bc0301]
                        transition-all duration-100 ease-out">
                            Daftar sekarang
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
document.querySelectorAll('[data-password-toggle]').forEach(btn => {
    btn.addEventListener('click', () => {
        const inp = document.getElementById(btn.dataset.passwordToggle);
        inp.type = inp.type === 'password' ? 'text' : 'password';
        btn.textContent = inp.type === 'password' ? 'show' : 'hide';
    });
});

document.querySelector('form[action="/login"]')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const errEmail = document.getElementById('email-error');
    const errPass = document.getElementById('password-error');
    const inpEmail = document.getElementById('email');
    const inpPass = document.getElementById('password');

    [errEmail, errPass].forEach(el => {
        if (el) { el.classList.add('hidden'); }
    });
    [inpEmail, inpPass].forEach(el => {
        if (el) { el.classList.remove('border-red-500', 'text-red-400'); el.classList.add('border-white/10'); }
    });

    const emailVal = inpEmail.value.trim();
    const passVal = inpPass.value;
    let err = false;
    if (!emailVal) { errEmail.textContent = 'email wajib diisi!'; errEmail.classList.remove('hidden'); inpEmail.classList.remove('border-white/10'); inpEmail.classList.add('border-red-500', 'text-red-400'); err = true; }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) { errEmail.textContent = 'format berupa "email@mail.com"!'; errEmail.classList.remove('hidden'); inpEmail.classList.remove('border-white/10'); inpEmail.classList.add('border-red-500', 'text-red-400'); err = true; }
    if (!passVal) { errPass.textContent = 'password wajib diisi!'; errPass.classList.remove('hidden'); inpPass.classList.remove('border-white/10'); inpPass.classList.add('border-red-500', 'text-red-400'); err = true; }
    else if (passVal.length < 8) { errPass.textContent = 'password minimal 8 karakter!'; errPass.classList.remove('hidden'); inpPass.classList.remove('border-white/10'); inpPass.classList.add('border-red-500', 'text-red-400'); err = true; }
    if (err) return;

    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Memproses...';
    try {
        const res = await fetch(this.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: new FormData(this)
        });
        const data = await res.json();
        if (data.success) {
            window.location.href = data.redirect;
        } else {
                for (const [k, v] of Object.entries(data.errors || {})) {
                    const el = document.getElementById(k.replace(/_error$/, '-error'));
                    if (el) {
                        el.textContent = v;
                        el.classList.remove('hidden');
                        const inp = document.getElementById(k.replace(/_error$/, ''));
                        if (inp) { inp.classList.remove('border-white/10'); inp.classList.add('border-red-500', 'text-red-400'); }
                    }
                }
            btn.disabled = false;
            btn.textContent = originalText;
        }
    } catch(e) {
        btn.disabled = false;
        btn.textContent = originalText;
    }
});
</script>