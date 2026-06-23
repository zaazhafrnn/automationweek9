<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Team;
use App\Utils\Session;
use App\Utils\Security;

class TeamController extends Controller
{
    private $teamModel;

    public function __construct()
    {
        $this->teamModel = new Team();
    }

    public function registerForm()
    {
        if (!Session::get('user_id')) {
            $this->redirect('/login');
        }

        if (Session::get('role') === 'admin') {
            $this->redirect('/admin/dashboard');
        }

        $existingTeam = $this->teamModel->findByUserId(Session::get('user_id'));
        if ($existingTeam) {
            $this->redirect('/dashboard');
        }

        $this->view('dashboard/team_register', [
            'user_name' => Session::get('user_name'),
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }

    public function register()
    {
        if (!Session::get('user_id')) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard/team/register');
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->view('dashboard/team_register', ['error' => 'Invalid CSRF token', 'csrf_token' => Security::generateCsrfToken()]);
            return;
        }

        if ($this->teamModel->findByUserId(Session::get('user_id'))) {
            $this->redirect('/dashboard');
        }

        $teamName = trim($_POST['name'] ?? '');
        $division = $_POST['division'] ?? '';
        $ketuaName = trim($_POST['leader_name'] ?? '');
        $member1Name = trim($_POST['member_1_name'] ?? '');
        $member2Name = trim($_POST['member_2_name'] ?? '');

        if (empty($teamName) || empty($division) || empty($ketuaName)) {
            $this->view('dashboard/team_register', ['error' => 'Please fill all required fields.', 'csrf_token' => Security::generateCsrfToken()]);
            return;
        }

        $allowedDivisions = ['LF', 'PLC', 'FFR', 'LKTI'];
        if (!in_array($division, $allowedDivisions)) {
            $this->view('dashboard/team_register', ['error' => 'Invalid division selected.', 'csrf_token' => Security::generateCsrfToken()]);
            return;
        }

        if (in_array($division, ['LF', 'PLC'])) {
            $member2Name = null;
        }

        try {
            $this->teamModel->create(Session::get('user_id'), $teamName, $division, $ketuaName, $member1Name, $member2Name);
            $this->redirect('/dashboard');
        } catch (\Exception $e) {
            $this->view('dashboard/team_register', ['error' => 'Registration failed: ' . $e->getMessage(), 'csrf_token' => Security::generateCsrfToken()]);
        }
    }
}
