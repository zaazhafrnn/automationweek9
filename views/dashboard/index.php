<div class="bg-white shadow-xl rounded-2xl overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <form action="/logout" method="POST" class="m-0">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150">
                Logout
            </button>
        </form>
    </div>

    <div class="px-6 py-8">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Welcome, <?= htmlspecialchars($user_name ?? '') ?>! 👋</h2>
        <p class="text-lg text-gray-600 mb-8">You have successfully logged in. This is a secure area exclusively for authenticated users.</p>
        
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-blue-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                Security Features Implemented
            </h3>
            <ul class="space-y-3 text-blue-800">
                <li class="flex items-start">
                    <span class="mr-2 text-blue-500">•</span>
                    <span><strong>Password Hashing:</strong> Securely hashed using bcrypt (`password_hash`).</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2 text-blue-500">•</span>
                    <span><strong>Database Connection:</strong> PDO with Prepared Statements (SQL injection prevention).</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2 text-blue-500">•</span>
                    <span><strong>Session Security:</strong> Session ID regeneration to prevent session fixation.</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2 text-blue-500">•</span>
                    <span><strong>CSRF Protection:</strong> All forms use unique, session-validated CSRF tokens.</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2 text-blue-500">•</span>
                    <span><strong>XSS Prevention:</strong> All output is escaped using `htmlspecialchars`.</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2 text-blue-500">•</span>
                    <span><strong>Architecture:</strong> Custom Native MVC structure (Front Controller, Router).</span>
                </li>
            </ul>
        </div>
    </div>
</div>
