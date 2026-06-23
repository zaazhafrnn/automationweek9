<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Utils\Session;
use App\Utils\Security;
use App\Models\Team;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Session::get('user_id')) {
            $this->redirect('/login');
        }

        if (Session::get('role') === 'admin') {
            $this->redirect('/admin/dashboard');
        }

        $teamModel = new Team();
        $existingTeam = $teamModel->findByUserId(Session::get('user_id'));

        $this->view('dashboard/index', [
            'user_name' => Session::get('user_name'),
            'csrf_token' => Security::generateCsrfToken(),
            'team' => $existingTeam
        ]);
    }
}
