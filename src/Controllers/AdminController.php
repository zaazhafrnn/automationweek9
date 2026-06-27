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
            'user_name' => Session::get('user_name'),
            'page_title' => 'Dasbor'
        ], 'admin');
    }

    public function members()
    {
        if (!Session::get('user_id') || Session::get('role') !== 'admin') {
            $this->redirect('/login');
        }

        $userModel = new \App\Models\User();
        $members = $userModel->getAllMembers();

        $this->view('admin/members', [
            'members' => $members,
            'page_title' => 'Anggota'
        ], 'admin');
    }

    public function teams()
    {
        if (!Session::get('user_id') || Session::get('role') !== 'admin') {
            $this->redirect('/login');
        }

        $teamModel = new \App\Models\Team();
        $teams = $teamModel->getAllTeams();

        $this->view('admin/teams', [
            'teams' => $teams,
            'page_title' => 'Tim'
        ], 'admin');
    }

    public function payments()
    {
        if (!Session::get('user_id') || Session::get('role') !== 'admin') {
            $this->redirect('/login');
        }

        $paymentModel = new \App\Models\Payment();
        $payments = $paymentModel->getAllPayments();

        $this->view('admin/payments', [
            'payments' => $payments,
            'csrf_token' => \App\Utils\Security::generateCsrfToken(),
            'page_title' => 'Pembayaran'
        ], 'admin');
    }

    public function processPayment()
    {
        if (!Session::get('user_id') || Session::get('role') !== 'admin') {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/payments');
        }

        if (!\App\Utils\Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
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
            $paymentModel->updateStatus($paymentId, 'pending', null, null, true);
        } else {
            $status = $action === 'verify' ? 'verified' : 'rejected';
            $paymentModel->updateStatus($paymentId, $status, $note ?: null, Session::get('user_id'));
        }

        $this->redirect('/admin/payments');
    }
}
