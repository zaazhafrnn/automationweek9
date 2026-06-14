<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Utils\Session;
use App\Utils\Security;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Session::get('user_id')) {
            $this->redirect('/login');
        }

        $this->view('dashboard/index', [
            'user_name' => Session::get('user_name'),
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
}
