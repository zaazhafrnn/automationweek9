<?php
$body_class = 'bg-[#bc0301] text-gray-800 font-sans antialiased min-h-screen flex flex-col justify-center';
$main_class = 'w-full max-w-md mx-auto px-4 sm:px-6 lg:px-8';
?>

<div class="bg-white p-8 rounded-2xl shadow-xl">
    <div class="transition-transform duration-300 ease-in-out hover:scale-110 hover:rotate-6">
        <span class="navbar-logo">
            <img src="/image/logo-aw.png" alt="automationweek logo">
            <img src="/image/logo-future-technology.png" alt="future technology logo">
        </span>
    </div>

    <form action="/login" method="POST" class="space-y-6 mt-6" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

        <div>
            <label for="email" class="block text-sm font-medium mb-1">Email<span class="text-red-500">*</span></label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Masukkan email"
                value="<?= htmlspecialchars($old_email ?? '') ?>"
                class="appearance-none block w-full px-3 py-2 border-2 rounded-xl shadow-sm
                       placeholder-gray-400 focus:outline-none transition duration-150 ease-in-out sm:text-sm
                       <?= isset($email_error) ? 'border-red-500 text-red-900' : 'border-gray-300 focus:border-black' ?>"
                oninput="
                    this.classList.remove('border-red-500', 'text-red-900');
                    this.classList.add('border-gray-300');
                    document.getElementById('email-error')?.classList.add('hidden');
                ">
            <p id="email-error" class="mt-1 ml-2 text-sm text-red-600 <?= isset($email_error) ? '' : 'hidden' ?>">
                <?= htmlspecialchars($email_error ?? '') ?>
            </p>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium mb-1">Password<span class="text-red-500">*</span></label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan password"
                value="<?= htmlspecialchars($old_password ?? '') ?>"
                class="appearance-none block w-full px-3 py-2 border-2 rounded-xl shadow-sm
                       placeholder-gray-400 focus:outline-none transition duration-150 ease-in-out sm:text-sm
                       <?= isset($password_error) ? 'border-red-500 text-red-900' : 'border-gray-300 focus:border-black' ?>"
                oninput="
                    this.classList.remove('border-red-500', 'text-red-900');
                    this.classList.add('border-gray-300');
                    document.getElementById('password-error')?.classList.add('hidden');
                ">
            <p id="password-error" class="mt-1 ml-2 text-sm text-red-600 <?= isset($password_error) ? '' : 'hidden' ?>">
                <?= htmlspecialchars($password_error ?? '') ?>
            </p>
        </div>

        <button
            type="submit"
            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm
                   text-sm font-bold text-white bg-[#bc0301] hover:bg-[#9a0201]
                   transition duration-150 ease-in-out transform hover:scale-[1.02] active:scale-95 cursor-pointer">
            Login
        </button>
    </form>

    <div class="mt-8 text-center">
        <p class="text-sm">
            Belum punya akun?
            <a
                href="/register"
                class="inline-block font-semibold text-[#bc0301] hover:text-[#9a0201]
                       transition-all duration-100 ease-out
                       hover:-translate-y-0.25">
                Daftar sekarang
            </a>
        </p>
    </div>
</div>