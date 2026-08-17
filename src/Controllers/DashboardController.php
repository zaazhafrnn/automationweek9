<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Utils\Session;
use App\Utils\Security;
use App\Models\User;
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
        'review' => 4,
    ];

    private const TAB_NUM_TO_SLUG = [
        1 => 'team-register',
        2 => 'members',
        3 => 'social-media',
        4 => 'review',
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
                    'original_student_card' => $r['original_student_card'] ?? null,
                    'original_ig_follow' => $r['original_ig_follow'] ?? null,
                    'original_twibbon' => $r['original_twibbon'] ?? null,
                ];
            }
        }

        $upload1 = $uploads[1] ?? [];
        $tabDone = [
            1 => (bool) $team,
            2 => !empty($team['leaderName']),
            3 => !empty($upload1['ig_follow']) && !empty($upload1['twibbon']),
            4 => (bool) $team && !empty($team['leaderName']) && !empty($upload1['ig_follow']) && !empty($upload1['twibbon']),
        ];

        $activeTab = 1;
        foreach ($tabDone as $n => $done) {
            if (!$done) {
                $activeTab = $n;
                break;
            }
            $activeTab = $n;
        }

        $this->redirect('/application/' . self::TAB_NUM_TO_SLUG[$activeTab]);
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
                    'original_student_card' => $r['original_student_card'] ?? null,
                    'original_ig_follow' => $r['original_ig_follow'] ?? null,
                    'original_twibbon' => $r['original_twibbon'] ?? null,
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

    public function payment()
    {
        $this->requireAuth();
        if (Session::get('role') === 'admin') {
            $this->redirect('/admin/dashboard');
        }

        $team = (new Team())->findByUserId(Session::get('user_id'));
        $payment = null;
        if ($team) {
            $payment = (new Payment())->findByTeamId($team['id']);
        }

        $this->view('dashboard/payment', [
            'user_name' => Session::get('user_name'),
            'csrf_token' => Security::generateCsrfToken(),
            'team' => $team,
            'payment' => $payment,
        ]);
    }

    public function home()
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
                    'original_student_card' => $r['original_student_card'] ?? null,
                    'original_ig_follow' => $r['original_ig_follow'] ?? null,
                    'original_twibbon' => $r['original_twibbon'] ?? null,
                ];
            }
        }

        $this->view('dashboard/home', [
            'user_name' => Session::get('user_name'),
            'csrf_token' => Security::generateCsrfToken(),
            'team' => $team,
            'payment' => $payment,
            'submission' => $submission,
            'uploads' => $uploads,
        ]);
    }

    public function profile()
    {
        $this->requireAuth();
        if (Session::get('role') === 'admin') {
            $this->redirect('/admin/dashboard');
        }

        $user = (new User())->findById((int) Session::get('user_id'));

        $this->view('dashboard/profile', [
            'user_name' => Session::get('user_name'),
            'user_email' => $user['email'] ?? '',
            'csrf_token' => Security::generateCsrfToken(),
            'success' => Session::flash('success'),
            'error' => Session::flash('error'),
            'old_name' => Session::flash('old_name'),
            'errors' => Session::flash('errors') ?? [],
        ]);
    }

    public function updateProfile()
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/profile');
        }

        $name = Security::sanitizeString($_POST['name'] ?? '');

        if (empty($name)) {
            Session::flash('errors', ['name_error' => 'nama wajib diisi!']);
            Session::flash('old_name', $name);
            $this->redirect('/profile');
        }

        $userId = (int) Session::get('user_id');
        $userModel = new User();
        $userModel->updateName($userId, $name);
        Session::set('user_name', $name);

        Session::flash('success', 'Profil berhasil diperbarui.');
        $this->redirect('/profile');
    }

    public function updatePassword()
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/profile');
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['new_password_confirmation'] ?? '';

        $errors = [];
        if (empty($currentPassword)) {
            $errors['current_password_error'] = 'password saat ini wajib diisi!';
        }
        if (empty($newPassword)) {
            $errors['new_password_error'] = 'password baru wajib diisi!';
        } elseif (strlen($newPassword) < 8) {
            $errors['new_password_error'] = 'password minimal 8 karakter!';
        }
        if ($newPassword !== $confirmPassword) {
            $errors['new_password_error'] = 'konfirmasi password tidak cocok!';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            $this->redirect('/profile');
        }

        $userModel = new User();
        $hash = $userModel->getPasswordHash((int) Session::get('user_id'));

        if (!$hash || !password_verify($currentPassword, $hash)) {
            Session::flash('errors', ['current_password_error' => 'password saat ini salah!']);
            $this->redirect('/profile');
        }

        $userModel->updatePassword((int) Session::get('user_id'), $newPassword);
        Session::flash('success', 'Password berhasil diubah.');
        $this->redirect('/profile');
    }
}
