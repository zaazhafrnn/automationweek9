<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Utils\Session;
use App\Utils\Security;
use App\Models\Team;
use App\Models\TeamDocumentationUpload;
use App\Models\Payment;
use App\Models\Submission;

class DashboardController extends Controller
{
    private const TAB_SLUGS = [
        'team-register' => 1,
        'members' => 2,
        'social-media' => 3,
        'payment' => 4,
        'review' => 5,
    ];

    private const TAB_NUM_TO_SLUG = [
        1 => 'team-register',
        2 => 'members',
        3 => 'social-media',
        4 => 'payment',
        5 => 'review',
    ];

    public function index()
    {
        $this->requireAuth();
        if (Session::get('role') === 'admin') {
            $this->redirect('/admin/dashboard');
        }

        $team = (new Team())->findByUserId(Session::get('user_id'));

        $payment = null;
        $submission = null;
        $uploads = [];
        if ($team) {
            $payment = (new Payment())->findByTeamId($team['id']);
            $submission = (new Submission())->findByTeamId($team['id']);
            $rows = (new TeamDocumentationUpload())->findByTeam($team['id']);
            foreach ($rows as $r) {
                $uploads[$r['member_number']] = [
                    'student_card' => $r['student_card'],
                    'ig_follow' => $r['ig_follow'],
                    'twibbon' => $r['twibbon'],
                ];
            }
        }

        $upload1 = $uploads[1] ?? [];
        $tabDone = [
            1 => (bool) $team,
            2 => !empty($team['leaderName']),
            3 => !empty($upload1['ig_follow']) && !empty($upload1['twibbon']),
            4 => (bool) $payment,
            5 => (bool) $team && !empty($team['leaderName']) && !empty($upload1['ig_follow']) && !empty($upload1['twibbon']) && (bool) $payment,
        ];

        $activeTab = 1;
        foreach ($tabDone as $n => $done) {
            if (!$done) break;
            $activeTab = $n;
        }

        $this->redirect('/dashboard/' . self::TAB_NUM_TO_SLUG[$activeTab]);
    }

    public function tab()
    {
        $this->requireAuth();
        if (Session::get('role') === 'admin') {
            $this->redirect('/admin/dashboard');
        }

        $slug = basename($_SERVER['REQUEST_URI']);
        $activeTab = self::TAB_SLUGS[$slug] ?? 1;

        $team = (new Team())->findByUserId(Session::get('user_id'));

        $payment = null;
        $submission = null;
        $uploads = [];
        if ($team) {
            $payment = (new Payment())->findByTeamId($team['id']);
            $submission = (new Submission())->findByTeamId($team['id']);
            $rows = (new TeamDocumentationUpload())->findByTeam($team['id']);
            foreach ($rows as $r) {
                $uploads[$r['member_number']] = [
                    'student_card' => $r['student_card'],
                    'ig_follow' => $r['ig_follow'],
                    'twibbon' => $r['twibbon'],
                ];
            }
        }

        $this->view('dashboard/index', [
            'user_name' => Session::get('user_name'),
            'csrf_token' => Security::generateCsrfToken(),
            'team' => $team,
            'payment' => $payment,
            'submission' => $submission,
            'uploads' => $uploads,
            'activeTab' => $activeTab,
        ]);
    }
}
