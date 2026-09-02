<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Utils\Session;

use App\Utils\Security;
use App\Models\TeamDocumentationUpload;
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
        $isSuperAdmin = stripos((string) Session::get('user_name'), 'superadmin') !== false;
        $isLkti = stripos((string) Session::get('user_name'), 'lkti') !== false;
        $members = $userModel->getAllMembers($isSuperAdmin);

        $teamsByUser = [];
        foreach ((new \App\Models\Team())->getAllTeams() as $t) {
            $teamsByUser[$t['user_id']] = $t;
        }

        if ($isLkti) {
            $members = array_values(array_filter(
                $members,
                fn($m) => strtoupper($teamsByUser[$m['id']]['division'] ?? '') === 'LKTI'
            ));
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
            $docRows = [];
            if ($team && $team['id']) {
                $docRows = (new TeamDocumentationUpload())
                    ->findByTeam($team['id']);
                foreach ($docRows as $row) {
                    $num = $row['member_number'];
                    $team["twibbon_$num"] = $row['twibbon'] ?? null;
                    $team["student_card_$num"] = $row['student_card'] ?? null;
                    $team["ig_follow_$num"] = $row['ig_follow'] ?? null;
                }
            }
            $progress[$m['id']] = [
                'team' => $team,
                'docs' => $docRows,
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

        $isSuperAdmin = stripos((string) Session::get('user_name'), 'superadmin') !== false;
        if (!$isSuperAdmin) {
            $userModelTmp = new \App\Models\User();
            $allMembersTmp = $userModelTmp->getAllMembers(true);
            $roleByUserId = [];
            foreach ($allMembersTmp as $m) {
                $roleByUserId[$m['id']] = $m['role'] ?? '';
            }
            $teams = array_values(array_filter($teams, fn($t) => ($roleByUserId[$t['user_id']] ?? '') !== 'dummy'));
        }

        if (stripos((string) Session::get('user_name'), 'lkti') !== false) {
            $teams = array_values(array_filter(
                $teams,
                fn($t) => strtoupper($t['division'] ?? '') === 'LKTI'
            ));
        }

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

        $isSuperAdmin = stripos((string) Session::get('user_name'), 'superadmin') !== false;
        if (!$isSuperAdmin) {
            $userModelTmp = new \App\Models\User();
            $allMembersTmp = $userModelTmp->getAllMembers(true);
            $roleByUserId = [];
            foreach ($allMembersTmp as $m) {
                $roleByUserId[$m['id']] = $m['role'] ?? '';
            }
            $teamModelTmp = new \App\Models\Team();
            $teamsTmp = $teamModelTmp->getAllTeams();
            $teamUser = [];
            foreach ($teamsTmp as $t) {
                $teamUser[$t['id']] = $t['user_id'];
            }
            $payments = array_values(array_filter($payments, function ($p) use ($roleByUserId, $teamUser) {
                $uid = $teamUser[$p['teamId']] ?? null;
                return $uid === null || ($roleByUserId[$uid] ?? '') !== 'dummy';
            }));
        }

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
        $submissions = $submissionModel->getAll();

        $isSuperAdmin = stripos((string) Session::get('user_name'), 'superadmin') !== false;
        if (!$isSuperAdmin) {
            $userModelTmp = new \App\Models\User();
            $allMembersTmp = $userModelTmp->getAllMembers(true);
            $roleByUserId = [];
            foreach ($allMembersTmp as $m) {
                $roleByUserId[$m['id']] = $m['role'] ?? '';
            }
            $teamModelTmp = new \App\Models\Team();
            $teamsTmp = $teamModelTmp->getAllTeams();
            $teamUser = [];
            foreach ($teamsTmp as $t) {
                $teamUser[$t['id']] = $t['user_id'];
            }
            $submissions = array_values(array_filter($submissions, function ($s) use ($roleByUserId, $teamUser) {
                $uid = $teamUser[$s['team_id']] ?? null;
                return $uid === null || ($roleByUserId[$uid] ?? '') !== 'dummy';
            }));
        }

        $this->view('admin/submissions', [
            'submissions' => $submissions,
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
