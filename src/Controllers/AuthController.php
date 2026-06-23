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

    public function loginForm()
    {
        if (Session::get('user_id')) {
            if (Session::get('role') === 'admin') {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/dashboard');
            }
        }

        $this->view('auth/login', [
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die("Invalid CSRF token.");
        }

        $email = Security::sanitizeString($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $data = [
                'csrf_token' => Security::generateCsrfToken(),
                'old_email' => $email
            ];
            if (empty($email)) $data['email_error'] = 'email wajib diisi!';
            if (empty($password)) $data['password_error'] = 'password wajib diisi!';

            $this->view('auth/login', $data);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('auth/login', [
                'email_error' => 'email harus berupa "email@mail.com"',
                'old_email' => $email,
                'csrf_token' => Security::generateCsrfToken()
            ]);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $this->view('auth/login', [
                'email_error' => 'email tidak ditemukan!',
                'old_email' => $email,
                'old_password' => $password,
                'csrf_token' => Security::generateCsrfToken()
            ]);
            return;
        }

        if (!password_verify($password, $user['password'])) {
            $this->view('auth/login', [
                'password_error' => 'password salah!',
                'old_email' => $email,
                'csrf_token' => Security::generateCsrfToken()
            ]);
            return;
        }

        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);

        $role = $user['role'] ?? 'member';
        Session::set('role', $role);

        if ($role === 'admin') {
            $this->redirect('/admin/dashboard');
        } else {
            $this->redirect('/dashboard');
        }
    }

    public function registerForm()
    {
        if (Session::get('user_id')) {
            if (Session::get('role') === 'admin') {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/dashboard');
            }
        }

        $this->view('auth/register', [
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die("Invalid CSRF token.");
        }

        $name = Security::sanitizeString($_POST['name'] ?? '');
        $email = Security::sanitizeString($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $data = [
                'csrf_token' => Security::generateCsrfToken(),
                'old_name' => $name,
                'old_email' => $email
            ];
            if (empty($name)) $data['name_error'] = 'nama wajib diisi!';
            if (empty($email)) $data['email_error'] = 'email wajib diisi!';
            if (empty($password)) $data['password_error'] = 'password wajib diisi!';

            $this->view('auth/register', $data);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('auth/register', [
                'email_error' => 'email harus berupa "email@mail.com"',
                'old_name' => $name,
                'old_email' => $email,
                'csrf_token' => Security::generateCsrfToken()
            ]);
            return;
        }

        if ($this->userModel->findByEmail($email)) {
            $this->view('auth/register', [
                'email_error' => 'Email sudah terdaftar! Silahkan lakukan login',
                'old_name' => $name,
                'old_email' => $email,
                'csrf_token' => Security::generateCsrfToken()
            ]);
            return;
        }

        if ($this->userModel->create($name, $email, $password)) {
            $this->redirect('/login');
        } else {
            $this->view('auth/register', [
                'error' => 'Registrasi gagal. Silahkan coba lagi.',
                'csrf_token' => Security::generateCsrfToken()
            ]);
        }
    }

    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die("Invalid CSRF token.");
        }

        Session::destroy();
        $this->redirect('/login');
    }
}
