<?php

/** @var string $csrf_token */
/** @var string|null $error */
/** @var string|null $old_name */
/** @var string|null $old_email */
/** @var string|null $old_password */
/** @var string|null $name_error */
/** @var string|null $email_error */
/** @var string|null $password_error */

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
                    class="h-28 mix-blend-multiply w-auto select-none pointer-events-none">
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

                        <form action="/register" method="POST" class="space-y-4">
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                            <div>
                                <label
                                    for="name"
                                    class="block text-sm font-medium mb-1 text-gray-300">
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

                                <p
                                    id="name-error"
                                    class="mt-1 ml-1 text-sm text-red-400 <?= isset($name_error) ? '' : 'hidden' ?>">
                                    <?= htmlspecialchars($name_error ?? '') ?>
                                </p>
                            </div>

                            <div>
                                <label
                                    for="email"
                                    class="block text-sm font-medium mb-1 text-gray-300">
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

                                <p
                                    id="email-error"
                                    class="mt-1 ml-1 text-sm text-red-400 <?= isset($email_error) ? '' : 'hidden' ?>">
                                    <?= htmlspecialchars($email_error ?? '') ?>
                                </p>
                            </div>

                            <div>
                                <label
                                    for="password"
                                    class="block text-sm font-medium mb-1 text-gray-300">
                                    Password<span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="Masukkan password"
                                    required
                                    minlength="8"
                                    value="<?= htmlspecialchars($old_password ?? '') ?>"
                                    class="appearance-none block w-full px-3 py-2.5 border rounded-xl shadow-sm
                                           bg-[#2a2926] placeholder-gray-500 text-white
                                           focus:outline-none focus:border-white/40 transition duration-150 ease-in-out sm:text-sm
                                           <?= isset($password_error) ? 'border-red-500 text-red-400' : 'border-white/10' ?>"
                                    oninput="
                                        this.classList.remove('border-red-500', 'text-red-400');
                                        this.classList.add('border-white/10');
                                        document.getElementById('password-error')?.classList.add('hidden');
                                    ">

                                <p
                                    id="password-error"
                                    class="mt-1 ml-1 text-sm text-red-400 <?= isset($password_error) ? '' : 'hidden' ?>">
                                    <?= htmlspecialchars($password_error ?? '') ?>
                                </p>
                            </div>

                            <button
                                type="submit"
                                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm
                                       text-sm font-bold text-[#161512] bg-white hover:bg-gray-200
                                       transition duration-150 ease-in-out transform hover:scale-[1.02] active:scale-95 cursor-pointer">
                                Daftar
                            </button>
                        </form>
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
document.querySelector('form[action="/register"]')?.addEventListener('submit', async function(e) {
    e.preventDefault();
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
                    el.previousElementSibling?.classList.add('border-red-500', 'text-red-400');
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