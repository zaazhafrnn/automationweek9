<?php
$body_class = 'bg-[#bc0301] text-gray-800 font-sans antialiased min-h-screen flex flex-col justify-center';
$main_class = 'w-full max-w-md mx-auto px-4 sm:px-6 lg:px-8';
?>
<div class="bg-white p-8 rounded-2xl shadow-xl">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">Create Account</h1>
        <p class="mt-2 text-sm text-gray-600">Join us today to get started</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
            <p class="text-sm"><?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>

    <form action="/register" method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
        
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" id="name" name="name" required 
                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out sm:text-sm">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" id="email" name="email" required 
                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out sm:text-sm">
        </div>
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" id="password" name="password" required 
                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out sm:text-sm">
        </div>
        
        <button type="submit" 
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out transform hover:-translate-y-0.5">
            Register
        </button>
    </form>

    <div class="mt-8 text-center">
        <p class="text-sm text-gray-600">
            Already have an account? 
            <a href="/login" class="font-medium text-blue-600 hover:text-blue-500 transition ease-in-out duration-150">Login here</a>
        </p>
    </div>
</div>
