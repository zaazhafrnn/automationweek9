<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Utils\Session;

use App\Utils\Security;
use App\Models\Submission;

class AdminController extends Controller
{
    public function dashboard()
    {
        $this->requireAdmin();

        $this->view('admin/dashboard', [
            'user_name' => Session::get('user_name'),
            'total_users' => (new \App\Models\User())->countMembers(),
            'total_teams' => (new \App\Models\Team())->countAll(),
            'divisions' => (new \App\Models\Team())->countByDivision(),
            'page_title' => 'Dasbor'
        ], 'admin');
    }

    public function accounts()
    {
        $this->requireAdmin();

        $userModel = new \App\Models\User();
        $isSuperAdmin = str_contains((string) Session::get('user_name'), 'superadmin');
        $members = $userModel->getAllMembers($isSuperAdmin);

        $teamsByUser = [];
        foreach ((new \App\Models\Team())->getAllTeams() as $t) {
            $teamsByUser[$t['user_id']] = $t;
        }

        $paymentsByTeam = [];
        foreach ((new \App\Models\Payment())->getAllPayments() as $p) {
            $paymentsByTeam[$p['teamId']] = $p;
        }

        $subsByTeam = [];
        foreach ((new Submission())->getAll() as $s) {
            $subsByTeam[$s['team_id']][$s['type']] = $s;
        }

        $progress = [];
        foreach ($members as $m) {
            $team = $teamsByUser[$m['id']] ?? null;
            $progress[$m['id']] = [
                'team' => $team,
                'payment' => $team ? ($paymentsByTeam[$team['id']] ?? null) : null,
                'submissions' => $team ? ($subsByTeam[$team['id']] ?? []) : [],
            ];
        }

        $this->view('admin/accounts', [
            'members' => $members,
            'progress' => $progress,
            'page_title' => 'Akun'
        ], 'admin');
    }

    public function teams()
    {
        $this->requireAdmin();

        $teamModel = new \App\Models\Team();
        $teams = $teamModel->getAllTeams();

        $this->view('admin/teams', [
            'teams' => $teams,
            'page_title' => 'Tim'
        ], 'admin');
    }

    public function payments()
    {
        $this->requireAdmin();

        $paymentModel = new \App\Models\Payment();
        $payments = $paymentModel->getAllPayments();

        $this->view('admin/payments', [
            'payments' => $payments,
            'csrf_token' => Security::generateCsrfToken(),
            'page_title' => 'Pembayaran'
        ], 'admin');
    }

    public function submissions()
    {
        $this->requireAdmin();

        $submissionModel = new Submission();

        $this->view('admin/submissions', [
            'submissions' => $submissionModel->getAll(),
            'csrf_token' => Security::generateCsrfToken(),
            'page_title' => 'Karya'
        ], 'admin');
    }

    public function processSubmission()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/submissions');
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/admin/submissions');
        }

        $id = $_POST['submission_id'] ?? null;
        $map = [
            'approve' => 'approved',
            'reject' => 'rejected',
            'qualify' => 'qualified',
            'disqualify' => 'not_qualified',
            'reset' => 'submitted',
        ];

        if (!$id || !isset($map[$_POST['action'] ?? ''])) {
            $this->redirect('/admin/submissions');
        }

        (new Submission())->updateStatus((int) $id, $map[$_POST['action']]);
        $this->redirect('/admin/submissions');
    }

    public function processPayment()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/payments');
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/admin/payments');
        }

        $paymentId = $_POST['payment_id'] ?? null;
        $action = $_POST['action'] ?? '';
        $note = trim($_POST['note'] ?? '');

        if (!$paymentId || !in_array($action, ['verify', 'reject', 'cancel'])) {
            $this->redirect('/admin/payments');
        }

        $paymentModel = new \App\Models\Payment();

        if ($action === 'cancel') {
            $paymentModel->resetStatus($paymentId);
        } else {
            $status = $action === 'verify' ? 'verified' : 'rejected';
            $paymentModel->updateStatus($paymentId, $status, $note ?: null, Session::get('user_id'));
        }

        $this->redirect('/admin/payments');
    }
}
