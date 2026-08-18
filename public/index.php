<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';


if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($path !== '/' && file_exists(__DIR__ . $path)) {
        return false;
    }
}

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = BASE_PATH . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

\App\Utils\Session::init();

$router = new \App\Core\Router();

$router->get('/', 'LandingController@index');
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');

$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');

$router->get('/forgot-password', 'AuthController@forgotPasswordForm');
$router->post('/forgot-password', 'AuthController@forgotPassword');
$router->get('/reset-password', 'AuthController@resetPasswordForm');
$router->post('/reset-password', 'AuthController@resetPassword');

$router->get('/home', 'DashboardController@home');
$router->get('/application', 'DashboardController@index');
$router->get('/application/team-register', 'DashboardController@tab');
$router->get('/application/members', 'DashboardController@tab');
$router->get('/application/social-media', 'DashboardController@tab');
$router->get('/application/review', 'DashboardController@tab');
$router->get('/payments', 'DashboardController@payment');
$router->get('/profile', 'DashboardController@profile');
$router->post('/profile', 'DashboardController@updateProfile');
$router->post('/profile/update-password', 'DashboardController@updatePassword');
$router->get('/admin/dashboard', 'AdminController@dashboard');
$router->get('/admin/accounts', 'AdminController@accounts');
$router->get('/admin/teams', 'AdminController@teams');
$router->get('/admin/payments', 'AdminController@payments');
$router->post('/admin/payments/process', 'AdminController@processPayment');
$router->get('/admin/submissions', 'AdminController@submissions');

$router->post('/application/team/register', 'TeamController@register');
$router->post('/application/team/update', 'TeamController@update');
$router->post('/application/team/submit', 'TeamController@submit');

$router->post('/payments', 'PaymentController@upload');

$router->post('/application/submission', 'SubmissionController@upload');

$router->post('/logout', 'AuthController@logout');

try {
    $router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (\Throwable $e) {
    http_response_code(500);
    echo '<div style="padding:2rem; background:#fee2e2; border:2px solid #ef4444; font-family:sans-serif; color:#991b1b; margin:2rem; border-radius:1rem;">';
    echo '<h2 style="margin-top:0;">PHP Fatal Exception / Error</h2>';
    echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ' on line ' . $e->getLine() . '</p>';
    echo '<pre style="background:#fff; padding:1rem; overflow:auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}

