<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Utils\Mailer;
use App\Utils\Session;
use App\Utils\Security;

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    private function validateCsrf(): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            if ($this->wantsJson()) {
                $this->jsonExit(['success' => false, 'errors' => ['error' => 'Invalid CSRF token.']]);
            }
            die("Invalid CSRF token.");
        }
    }

    private function wantsJson(): bool
    {
        return isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
    }

    private function jsonExit(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function redirectWithError($route, $errors, $oldData = [])
    {
        if ($this->wantsJson()) {
            $this->jsonExit(['success' => false, 'errors' => $errors]);
        }
        Session::flash('errors', $errors);
        Session::flash('old_data', $oldData);
        $this->redirect($route);
    }

    public function loginForm()
    {
        $this->requireGuest();

        $data = [
            'csrf_token' => Security::generateCsrfToken()
        ];

        $errors = Session::flash('errors') ?? [];
        $oldData = Session::flash('old_data') ?? [];
        $success = Session::flash('success');

        $this->view('auth/login', array_merge($data, $errors, $oldData, ['success' => $success]));
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $this->validateCsrf();

        $email = Security::sanitizeString($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];
        if (empty($email)) {
            $errors['email_error'] = 'email wajib diisi!';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email_error'] = 'format berupa "email@mail.com"!';
        }

        if (empty($password)) {
            $errors['password_error'] = 'password wajib diisi!';
        } elseif (strlen($password) < 8) {
            $errors['password_error'] = 'password minimal 8 karakter!';
        }

        if (!empty($errors)) {
            $this->redirectWithError('/login', $errors, ['old_email' => $email]);
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $this->redirectWithError('/login', ['email_error' => 'email tidak ditemukan!'], ['old_email' => $email]);
        }

        if (!password_verify($password, $user['password'])) {
            $this->redirectWithError('/login', ['password_error' => 'password salah!'], ['old_email' => $email]);
        }

        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('role', $user['role'] ?? 'member');

        $redirectUrl = Session::get('role') === 'admin' ? '/admin/dashboard' : '/home';

        if ($this->wantsJson()) {
            $this->jsonExit(['success' => true, 'redirect' => $redirectUrl]);
        }

        $this->redirect($redirectUrl);
    }

    public function registerForm()
    {
        $this->requireGuest();

        $data = [
            'csrf_token' => Security::generateCsrfToken()
        ];

        $errors = Session::flash('errors') ?? [];
        $oldData = Session::flash('old_data') ?? [];

        $this->view('auth/register', array_merge($data, $errors, $oldData));
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $this->validateCsrf();

        $name = Security::sanitizeString($_POST['name'] ?? '');
        $email = Security::sanitizeString($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];
        if (empty($name)) {
            $errors['name_error'] = 'nama wajib diisi!';
        }

        if (empty($email)) {
            $errors['email_error'] = 'email wajib diisi!';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email_error'] = 'email harus berupa "email@mail.com"';
        } elseif ($this->userModel->findByEmail($email)) {
            $errors['email_error'] = 'email sudah terdaftar! silahkan login';
        }

        if (empty($password)) {
            $errors['password_error'] = 'password wajib diisi!';
        } elseif (strlen($password) < 8) {
            $errors['password_error'] = 'password minimal 8 karakter!';
        }

        if (!empty($errors)) {
            $this->redirectWithError('/register', $errors, ['old_name' => $name, 'old_email' => $email]);
        }

        if ($this->userModel->create($name, $email, $password)) {
            if ($this->wantsJson()) {
                $this->jsonExit(['success' => true, 'redirect' => '/login']);
            }
            $this->redirect('/login');
        } else {
            if ($this->wantsJson()) {
                $this->jsonExit(['success' => false, 'errors' => ['error' => 'Registrasi gagal. Silahkan coba lagi.']]);
            }
            $this->redirectWithError('/register', ['error' => 'Registrasi gagal. Silahkan coba lagi.'], ['old_name' => $name, 'old_email' => $email]);
        }
    }

    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $this->validateCsrf();

        Session::destroy();
        $this->redirect('/login');
    }

    public function forgotPasswordForm()
    {
        $this->requireGuest();

        $data = ['csrf_token' => Security::generateCsrfToken()];
        $errors = Session::flash('errors') ?? [];
        $success = Session::flash('success');

        $this->view('auth/forgot', array_merge($data, $errors, ['success' => $success]));
    }

    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $this->validateCsrf();

        $email = Security::sanitizeString($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithError('/forgot-password', ['email_error' => 'format berupa "email@mail.com"!']);
        }

        $user = $this->userModel->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->storeResetToken($user['id'], $token);

            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $link = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/reset-password?token=' . $token;
            $body = '<p>Halo ' . htmlspecialchars($user['name']) . ',</p>'
                . '<p>Klik link berikut untuk mereset password Anda (berlaku 1 jam):</p>'
                . '<p><a href="' . htmlspecialchars($link) . '">Reset password</a></p>'
                . '<p>Jika bukan Anda, abaikan email ini.</p>';

            Mailer::send($email, 'Reset Password - Automation Week IX', $body);
        }

        $message = 'Jika email terdaftar, link reset password telah dikirim. Periksa inbox Anda.';
        if ($this->wantsJson()) {
            $this->jsonExit(['success' => true, 'message' => $message]);
        }

        Session::flash('success', $message);
        $this->redirect('/forgot-password');
    }

    public function resetPasswordForm()
    {
        $this->requireGuest();

        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $this->redirectWithError('/forgot-password', ['error' => 'Link reset tidak valid.']);
        }

        $reset = $this->userModel->findResetToken($token);
        if (!$reset || strtotime($reset['expires_at']) < time()) {
            $this->redirectWithError('/forgot-password', ['error' => 'Link reset tidak valid atau sudah kedaluwarsa. Silakan minta link baru.']);
        }

        $account = $this->userModel->findById($reset['account_id']);

        $data = [
            'csrf_token' => Security::generateCsrfToken(),
            'token' => $token,
            'email' => $account['email'] ?? null
        ];
        $errors = Session::flash('errors') ?? [];

        $this->view('auth/reset', array_merge($data, $errors));
    }

    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $this->validateCsrf();

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirmation'] ?? '';

        $errors = [];
        if (strlen($password) < 8) {
            $errors['password_error'] = 'password minimal 8 karakter!';
        }
        if ($password !== $confirm) {
            $errors['password_error'] = 'konfirmasi password tidak sama!';
        }

        $resetUrl = '/reset-password?token=' . urlencode($token);

        if (!empty($errors)) {
            $this->redirectWithError($resetUrl, $errors);
        }

        $reset = $this->userModel->findResetToken($token);

        if (!$reset || strtotime($reset['expires_at']) < time()) {
            $this->redirectWithError('/forgot-password', ['error' => 'Link reset tidak valid atau sudah kedaluwarsa. Silakan minta link baru.']);
        }

        $this->userModel->updatePassword($reset['account_id'], $password);
        $this->userModel->deleteResetToken($reset['account_id']);

        if ($this->wantsJson()) {
            $this->jsonExit(['success' => true, 'redirect' => '/login']);
        }

        Session::flash('success', 'Password berhasil diubah. Silakan login.');
        $this->redirect('/login');
    }
}
