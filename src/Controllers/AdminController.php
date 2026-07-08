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
            'page_title' => 'Dasbor'
        ], 'admin');
    }

    public function members()
    {
        $this->requireAdmin();

        $userModel = new \App\Models\User();
        $members = $userModel->getAllMembers();

        $this->view('admin/members', [
            'members' => $members,
            'page_title' => 'Anggota'
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

        $division = $_GET['div'] ?? '';
        $allowed = ['FFR', 'LF', 'PLC', 'LKTI'];
        if (!in_array($division, $allowed)) {
            $division = 'FFR';
        }

        $submissionModel = new Submission();
        $all = $submissionModel->getByDivision($division);

        $this->view('admin/submissions', [
            'submissions' => $all,
            'division' => $division,
            'page_title' => "Karya $division"
        ], 'admin');
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
