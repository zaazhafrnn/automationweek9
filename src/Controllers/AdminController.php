<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Utils\Session;

use App\Utils\Security;
use App\Models\TeamDocumentationUpload;
use App\Models\Submission;

class AdminController extends Controller
{
    private function adminDivision(): ?string
    {
        $userName = (string) Session::get('user_name');
        if (stripos($userName, 'superadmin') !== false) {
            return null;
        }
        foreach (['LF', 'PLC', 'FFR', 'LKTI', 'PROG'] as $code) {
            if (stripos($userName, $code) !== false) {
                return $code;
            }
        }

        return null;
    }

    private function isBendahara(): bool
    {
        $userName = (string) Session::get('user_name');
        return stripos($userName, 'bendahara') !== false || stripos($userName, 'keuangan') !== false;
    }

    private function requirePaymentsAccess(): void
    {
        if (!($this->isBendahara() || stripos((string) Session::get('user_name'), 'superadmin') !== false)) {
            $this->redirect('/admin/dashboard');
        }
    }

    private function filteredTeams(): array
    {
        $teamModel = new \App\Models\Team();
        $teams = $teamModel->getAllTeams();

        $division = $this->adminDivision();

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

        if ($division !== null) {
            $teams = array_values(array_filter(
                $teams,
                fn($t) => strtoupper($t['division'] ?? '') === $division
            ));
        }

        return $teams;
    }

    public function dashboard()
    {
        $this->requireAdmin();

        $division = $this->adminDivision();

        $teamModel = new \App\Models\Team();
        $allTeams = $teamModel->getAllTeams();
        $teams = ($division === null)
            ? $allTeams
            : array_values(array_filter($allTeams, fn($t) => strtoupper($t['division'] ?? '') === $division));

        $total_participants = 0;
        foreach ($teams as $t) {
            $total_participants += 1;
            if (!empty($t['firstMemberName'])) $total_participants += 1;
            if (!empty($t['secondMemberName'])) $total_participants += 1;
        }

        $this->view('admin/dashboard', [
            'user_name' => Session::get('user_name'),
            'division' => $division,
            'total_users' => $total_participants,
            'total_teams' => count($teams),
            'all_divisions' => ($division === null) ? $teamModel->countByDivision() : [],
            'page_title' => 'Dasbor'
        ], 'admin');
    }

    public function accounts()
    {
        $this->requireAdmin();

        $userModel = new \App\Models\User();
        $isSuperAdmin = stripos((string) Session::get('user_name'), 'superadmin') !== false;
        $division = $this->adminDivision();
        $members = $userModel->getAllMembers($isSuperAdmin);

        $teamsByUser = [];
        foreach ((new \App\Models\Team())->getAllTeams() as $t) {
            $teamsByUser[$t['user_id']] = $t;
        }

        if ($division !== null) {
            $members = array_values(array_filter(
                $members,
                fn($m) => strtoupper($teamsByUser[$m['id']]['division'] ?? '') === $division
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

        $this->view('admin/teams', [
            'teams' => $this->filteredTeams(),
            'page_title' => 'Tim'
        ], 'admin');
    }

    public function exportTeams()
    {
        $this->requireAdmin();

        $teams = $this->filteredTeams();

        $division = $this->adminDivision();
        $slug = $division ? strtolower($division) : 'semua';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="daftar_tim_' . $slug . '_' . date('Y-m-d_H-i') . '.csv"');
        echo "\xEF\xBB\xBF";

        $out = fopen('php://output', 'w');
        fputcsv($out, ['No.', 'Nama Tim', 'Asal Sekolah', 'Email', 'Nama Anggota', 'No. HP', 'Jenis Kelamin'], ',', '"', '');

        $no = 1;
        foreach ($teams as $t) {
            $firstRow = true;
            foreach (self::teamCsvRows($t) as $row) {
                array_unshift($row, $firstRow ? $no++ : '');
                $firstRow = false;
                fputcsv($out, $row, ',', '"', '');
            }
        }

        fclose($out);
        exit();
    }

    public static function teamCsvRows(array $t): array
    {
        $school = $t['teamSchool'] ?? '-';
        $email = $t['user_email'] ?? '-';
        $rows = [];

        $members = [
            [$t['leaderName'], $t['leaderPhoneNumber'] ?? '', $t['leaderGender'] ?? ''],
            [$t['firstMemberName'] ?? '', $t['firstMemberPhoneNumber'] ?? '', $t['firstMemberGender'] ?? ''],
            [$t['secondMemberName'] ?? '', $t['secondMemberPhoneNumber'] ?? '', $t['secondMemberGender'] ?? ''],
        ];

        $first = true;
        foreach ($members as [$name, $phone, $gender]) {
            if ($name === '') continue;
            $rows[] = [
                $first ? $t['name'] : '',
                $first ? $school : '',
                $first ? $email : '',
                $name,
                $phone,
                $gender,
            ];
            $first = false;
        }

        return $rows;
    }

    public function payments()
    {
        $this->requireAdmin();
        $this->requirePaymentsAccess();

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
        $submissions = array_values(array_filter($submissionModel->getAll(), fn($s) => in_array($s['type'], ['abstract', 'full_paper'], true)));

        $division = $this->adminDivision();

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

        if ($division !== null) {
            $submissions = array_values(array_filter(
                $submissions,
                fn($s) => strtoupper($s['division'] ?? '') === $division
            ));
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
        $this->requirePaymentsAccess();

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
