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
        $this->requireAuth();
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
        $this->requireAuth();

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
        $teamSchool = trim($_POST['teamSchool'] ?? '');
        $division = $_POST['division'] ?? '';
        $leaderName = trim($_POST['leaderName'] ?? '');
        $leaderPhoneNumber = trim($_POST['leaderPhoneNumber'] ?? '');
        $firstMemberName = trim($_POST['firstMemberName'] ?? '');
        $firstMemberPhoneNumber = trim($_POST['firstMemberPhoneNumber'] ?? '');
        $secondMemberName = trim($_POST['secondMemberName'] ?? '');
        $secondMemberPhoneNumber = trim($_POST['secondMemberPhoneNumber'] ?? '');

        if (empty($teamName) || empty($division) || empty($leaderName) || empty($leaderPhoneNumber)) {
            $this->view('dashboard/team_register', ['error' => 'Please fill all required fields.', 'csrf_token' => Security::generateCsrfToken()]);
            return;
        }

        $allowedDivisions = ['LF', 'PLC', 'FFR', 'LKTI'];
        if (!in_array($division, $allowedDivisions)) {
            $this->view('dashboard/team_register', ['error' => 'Invalid division selected.', 'csrf_token' => Security::generateCsrfToken()]);
            return;
        }

        if (in_array($division, ['LF', 'PLC'])) {
            $secondMemberName = null;
            $secondMemberPhoneNumber = null;
        }

        try {
            $this->teamModel->create(Session::get('user_id'), [
                'name' => $teamName,
                'teamSchool' => $teamSchool,
                'division' => $division,
                'leaderName' => $leaderName,
                'leaderPhoneNumber' => $leaderPhoneNumber,
                'firstMemberName' => $firstMemberName,
                'firstMemberPhoneNumber' => $firstMemberPhoneNumber,
                'secondMemberName' => $secondMemberName,
                'secondMemberPhoneNumber' => $secondMemberPhoneNumber,
            ]);
            $this->redirect('/dashboard');
        } catch (\Exception $e) {
            $this->view('dashboard/team_register', ['error' => 'Registration failed: ' . $e->getMessage(), 'csrf_token' => Security::generateCsrfToken()]);
        }
    }
}
