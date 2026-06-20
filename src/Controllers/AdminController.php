<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Utils\Session;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Must be logged in
        if (!Session::get('user_id')) {
            $this->redirect('/login');
        }

        // Must be an admin
        if (Session::get('role') !== 'admin') {
            $this->redirect('/dashboard');
        }

        $this->view('admin/dashboard', [
            'user_name' => Session::get('user_name')
        ]);
    }
}
