<?php

use App\Components\Toast;

/** @var string $csrf_token */
/** @var string|null $email_error */
/** @var string|null $error */

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
                        Masukkan email Anda, kami akan mengirim link untuk mereset password.
                    </p>
                </div>

                <?php if (!empty($error)): ?>
                    <?= Toast::make()->title('Link tidak valid')->message($error)->variant('error') ?>
                <?php endif; ?>

                <div class="border border-white/10 rounded-4xl overflow-hidden bg-[#1e1d1a]">
                    <div class="p-6">

                        <div id="forgot-form">
                            <form action="/forgot-password" method="POST" class="space-y-4" novalidate>
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

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
                                        class="appearance-none block w-full px-3 py-2.5 max-sm:py-2 border rounded-xl shadow-sm
                                               bg-[#2a2926] placeholder-gray-500 text-white
                                               focus:outline-none focus:border-white/40 transition duration-150 ease-in-out sm:text-sm max-sm:text-xs
                                               <?= isset($email_error) ? 'border-red-500 text-red-400' : 'border-white/10' ?>">
                                    <p id="email-error" class="mt-1 ml-1 max-sm:text-xs text-sm text-red-400 <?= isset($email_error) ? '' : 'hidden' ?>">
                                        <?= htmlspecialchars($email_error ?? '') ?>
                                    </p>
                                </div>

                                <button
                                    type="submit"
                                    class="w-full flex justify-center py-2.5 max-sm:py-2 px-4 border border-transparent rounded-xl shadow-sm
                                           text-sm max-sm:text-xs font-bold text-[#161512] bg-white hover:bg-gray-200
                                           transition duration-150 ease-in-out transform hover:scale-[1.02] active:scale-95 cursor-pointer
                                           disabled:opacity-50 disabled:hover:bg-white disabled:hover:scale-100 disabled:active:scale-100"
                                    ontouchstart="">
                                    Kirim Link Reset
                                </button>
                            </form>
                        </div>

                        <div id="forgot-success" class="hidden text-center py-8 space-y-4">
                            <div id="success-icon" class="w-16 h-16 mx-auto text-green-400"></div>
                            <h2 class="text-xl font-bold text-white">Link Terkirim!</h2>
                            <p class="text-gray-400 text-sm">Link reset password telah dikirim ke <span id="success-email" class="text-white font-semibold"></span>.<br>Periksa inbox Anda.</p>
                        </div>

                    </div>
                </div>

                <div class="mt-2 text-center">
                    <p class="max-sm:text-xs text-sm">
                        <a href="/login" class="inline-block font-semibold text-brand transition-all duration-100 ease-out">
                            Kembali ke login
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
    (async () => {
        const [mail, lc] = await Promise.all([
            fetch('/icons/mail.svg').then(r => r.text()),
            fetch('/icons/loader-circle.svg').then(r => r.text())
        ]);
        const form = document.querySelector('form[action="/forgot-password"]');
        if (form) form.dataset.loaderCircle = lc;
        const icon = document.getElementById('success-icon');
        if (icon) icon.innerHTML = mail.replace(/width="\d+"/, '').replace(/height="\d+"/, '');
    })();

    document.querySelector('form[action="/forgot-password"]')?.addEventListener('submit', async function(e) {
        e.preventDefault();

        const errEmail = document.getElementById('email-error');
        const inpEmail = document.getElementById('email');

        errEmail?.classList.add('hidden');
        inpEmail?.classList.remove('border-red-500', 'text-red-400');
        inpEmail?.classList.add('border-white/10');

        const emailVal = inpEmail.value.trim();
        if (!emailVal) {
            errEmail.textContent = 'email wajib diisi!';
            errEmail.classList.remove('hidden');
            inpEmail.classList.remove('border-white/10');
            inpEmail.classList.add('border-red-500', 'text-red-400');
            return;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
            errEmail.textContent = 'format berupa "email@mail.com"!';
            errEmail.classList.remove('hidden');
            inpEmail.classList.remove('border-white/10');
            inpEmail.classList.add('border-red-500', 'text-red-400');
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
                document.getElementById('success-email').textContent = emailVal;
                document.getElementById('forgot-form').classList.add('hidden');
                document.getElementById('forgot-success').classList.remove('hidden');
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
                btn.disabled = false;
                btn.textContent = originalText;
            }
        } catch (e) {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });
</script>