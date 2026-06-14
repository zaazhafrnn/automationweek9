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
            $this->redirect('/dashboard');
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
            $this->view('auth/login', [
                'error' => 'Please fill in all fields.',
                'csrf_token' => Security::generateCsrfToken()
            ]);
            return;
        }

        $user = $this->userModel->authenticate($email, $password);

        if ($user) {
            Session::regenerate();
            Session::set('user_id', $user['id']);
            Session::set('user_name', $user['name']);
            $this->redirect('/dashboard');
        } else {
            $this->view('auth/login', [
                'error' => 'Invalid email or password.',
                'csrf_token' => Security::generateCsrfToken()
            ]);
        }
    }

    public function registerForm()
    {
        if (Session::get('user_id')) {
            $this->redirect('/dashboard');
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
            $this->view('auth/register', [
                'error' => 'Please fill in all fields.',
                'csrf_token' => Security::generateCsrfToken()
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('auth/register', [
                'error' => 'Invalid email format.',
                'csrf_token' => Security::generateCsrfToken()
            ]);
            return;
        }

        if ($this->userModel->findByEmail($email)) {
            $this->view('auth/register', [
                'error' => 'Email is already registered.',
                'csrf_token' => Security::generateCsrfToken()
            ]);
            return;
        }

        if ($this->userModel->create($name, $email, $password)) {
            $this->redirect('/login');
        } else {
            $this->view('auth/register', [
                'error' => 'Registration failed. Please try again.',
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
