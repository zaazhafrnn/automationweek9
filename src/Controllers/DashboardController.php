<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Utils\Session;
use App\Utils\Security;
use App\Models\Team;
use App\Models\Payment;
use App\Models\Submission;

class DashboardController extends Controller
{
    public function index()
    {
        $this->requireAuth();
        if (Session::get('role') === 'admin') {
            $this->redirect('/admin/dashboard');
        }

        $team = (new Team())->findByUserId(Session::get('user_id'));

        $payment = null;
        $submission = null;
        if ($team) {
            $payment = (new Payment())->findByTeamId($team['id']);
            $submission = (new Submission())->findByTeamId($team['id']);
        }

        $this->view('dashboard/index', [
            'user_name' => Session::get('user_name'),
            'csrf_token' => Security::generateCsrfToken(),
            'team' => $team,
            'payment' => $payment,
            'submission' => $submission,
        ]);
    }
}
