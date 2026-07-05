<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Utils\Session;
use App\Utils\Security;

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    private function validateCsrf()
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
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

        $this->view('auth/login', array_merge($data, $errors, $oldData));
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
            $this->redirectWithError('/login', $errors, ['old_email' => $email, 'old_password' => $password]);
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $this->redirectWithError('/login', ['email_error' => 'email tidak ditemukan!'], ['old_email' => $email, 'old_password' => $password]);
        }

        if (!password_verify($password, $user['password'])) {
            $this->redirectWithError('/login', ['password_error' => 'password salah!'], ['old_email' => $email, 'old_password' => $password]);
        }

        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('role', $user['role'] ?? 'member');

        $redirectUrl = Session::get('role') === 'admin' ? '/admin/dashboard' : '/dashboard';

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
            $this->redirectWithError('/register', $errors, ['old_name' => $name, 'old_email' => $email, 'old_password' => $password]);
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
            $this->redirectWithError('/register', ['error' => 'Registrasi gagal. Silahkan coba lagi.'], ['old_name' => $name, 'old_email' => $email, 'old_password' => $password]);
        }
    }

    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $this->validateCsrf();

        Session::destroy();
        $this->redirect('/login');
    }
}
