<div class="bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-700 bg-gray-900 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-white">Admin Dashboard</h1>
        <form action="/logout" method="POST" class="m-0">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Utils\Security::generateCsrfToken() ?? '') ?>">
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150">
                Logout
            </button>
        </form>
    </div>

    <div class="px-6 py-8">
        <h2 class="text-3xl font-extrabold text-white mb-4">Welcome, Admin <?= htmlspecialchars($user_name ?? '') ?>! 🛡️</h2>
        <p class="text-lg text-gray-300 mb-8">This is the secure admin panel. Only users with the 'admin' role can see this page.</p>
        
        <div class="bg-indigo-900 border border-indigo-700 rounded-xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-indigo-200 mb-4 flex items-center">
                Admin Capabilities
            </h3>
            <ul class="space-y-3 text-indigo-300">
                <li class="flex items-start">
                    <span class="mr-2 text-indigo-400">•</span>
                    <span><strong>User Management:</strong> You will be able to manage other users from here.</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2 text-indigo-400">•</span>
                    <span><strong>Role Based Access Control:</strong> Only accessible to users whose role is set to 'admin' in the database.</span>
                </li>
            </ul>
        </div>
    </div>
</div>
