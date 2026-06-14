<div class="header">
    <h1>Dashboard</h1>
    <form action="/logout" method="POST" style="margin: 0;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
        <button type="submit" style="background-color: #ef4444;">Logout</button>
    </form>
</div>

<div>
    <h2>Welcome, <?= htmlspecialchars($user_name ?? '') ?>!</h2>
    <p>You have successfully logged in. This is a protected area that only authenticated users can see.</p>
    
    <div style="margin-top: 30px; padding: 20px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
        <h3>Security Features Implemented:</h3>
        <ul>
            <li><strong>Password Hashing:</strong> Your password is securely hashed using bcrypt (`password_hash`).</li>
            <li><strong>Database Connection:</strong> Connected using secure PDO with Prepared Statements to prevent SQL injection.</li>
            <li><strong>Session Security:</strong> Session ID is regenerated upon login to prevent session fixation.</li>
            <li><strong>CSRF Protection:</strong> All forms use unique CSRF tokens.</li>
            <li><strong>XSS Prevention:</strong> All output is escaped using `htmlspecialchars`.</li>
            <li><strong>Architecture:</strong> Custom Native MVC structure (Front Controller, Router, Controllers, Models, Views).</li>
        </ul>
    </div>
</div>
