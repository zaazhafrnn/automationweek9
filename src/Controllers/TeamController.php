<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Team;
use App\Models\TeamDocumentationUpload;
use App\Utils\Session;
use App\Utils\Security;

class TeamController extends Controller
{
    private Team $teamModel;
    private TeamDocumentationUpload $uploadModel;

    public function __construct()
    {
        $this->teamModel = new Team();
        $this->uploadModel = new TeamDocumentationUpload();
    }

    public function register()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/application/team-register');
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::flash('team_register_error', 'Invalid session. Silakan coba lagi.');
            $this->redirect('/application/team-register');
            return;
        }

        if ($this->teamModel->findByUserId(Session::get('user_id'))) {
            $this->redirect('/application/team-register');
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

        if (empty($teamName) || empty($division)) {
            Session::flash('team_register_error', 'Harap isi nama tim dan pilih divisi.');
            $this->redirect('/application/team-register');
            return;
        }

        $allowedDivisions = ['LF', 'PLC', 'FFR', 'LKTI', 'PROG'];
        if (!in_array($division, $allowedDivisions)) {
            Session::flash('team_register_error', 'Divisi yang dipilih tidak valid.');
            $this->redirect('/application/team-register');
            return;
        }

        if (in_array($division, ['LF', 'PLC', 'PROG'])) {
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
            $this->redirect('/application/' . ($_POST['next_tab'] ?? 'members'));
        } catch (\Exception $e) {
            Session::flash('team_register_error', 'Registrasi gagal: ' . $e->getMessage());
            $this->redirect('/application/team-register');
        }
    }

    public function update()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/application/team-register');
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::flash('team_update_error', 'Invalid session. Silakan coba lagi.');
            $this->redirect('/application/' . ($_POST['current_tab'] ?? 'members'));
            return;
        }

        $team = $this->teamModel->findByUserId(Session::get('user_id'));
        if (!$team) {
            $this->redirect('/application/team-register');
        }

        $data = [
            'name' => trim($_POST['name'] ?? $team['name']),
            'teamSchool' => trim($_POST['teamSchool'] ?? $team['teamSchool'] ?? ''),
            'division' => $_POST['division'] ?? $team['division'],
            'leaderName' => trim($_POST['leaderName'] ?? $team['leaderName'] ?? ''),
            'leaderPhoneNumber' => trim($_POST['leaderPhoneNumber'] ?? $team['leaderPhoneNumber'] ?? ''),
            'firstMemberName' => trim($_POST['firstMemberName'] ?? $team['firstMemberName'] ?? ''),
            'firstMemberPhoneNumber' => trim($_POST['firstMemberPhoneNumber'] ?? $team['firstMemberPhoneNumber'] ?? ''),
            'secondMemberName' => trim($_POST['secondMemberName'] ?? $team['secondMemberName'] ?? ''),
            'secondMemberPhoneNumber' => trim($_POST['secondMemberPhoneNumber'] ?? $team['secondMemberPhoneNumber'] ?? ''),
        ];

        if (empty($data['name']) || empty($data['leaderName'])) {
            Session::flash('team_update_error', 'Nama tim dan ketua harus diisi.');
            $this->redirect('/application/' . ($_POST['current_tab'] ?? 'members'));
            return;
        }

        try {
            $this->teamModel->update($team['id'], $data);

            $uploadDir = BASE_PATH . '/public/uploads/teams/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $columnMap = ['studentCard' => 'student_card', 'igFollow' => 'ig_follow', 'twibbon' => 'twibbon'];
            $members = [1, 2, 3];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            foreach ($members as $m) {
                foreach ($columnMap as $inputName => $column) {
                    $key = $inputName . '_' . $m;

                    if (!empty($_POST['delete_' . $key])) {
                        $existing = $this->uploadModel->findOne($team['id'], $m);
                        if ($existing && $existing[$column]) {
                            $oldFile = $existing[$column];
                            if (file_exists($uploadDir . $oldFile)) unlink($uploadDir . $oldFile);
                            $this->uploadModel->upsertColumn($team['id'], $m, $column, null, null);
                        }
                    }

                    if (empty($_FILES[$key]) || $_FILES[$key]['error'] !== UPLOAD_ERR_OK) continue;

                    $file = $_FILES[$key];
                    if (!in_array($file['type'], $allowedTypes)) continue;

                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $slug = preg_replace('/[^a-z0-9]/i', '-', $team['name']);
                    $fileName = $slug . '_' . $column . '_' . $m . '_' . date('Ymd_His') . '.' . $ext;
                    $dest = $uploadDir . $fileName;

                    if (move_uploaded_file($file['tmp_name'], $dest)) {
                        $existing = $this->uploadModel->findOne($team['id'], $m);
                        $oldFile = $existing && $existing[$column] ? $existing[$column] : null;
                        if ($oldFile && file_exists($uploadDir . $oldFile)) {
                            unlink($uploadDir . $oldFile);
                        }
                        $this->uploadModel->upsertColumn($team['id'], $m, $column, $fileName, $file['name']);
                    }
                }
            }

            Session::flash('team_update_success', 'Data tim berhasil diperbarui!');
        } catch (\Exception $e) {
            Session::flash('team_update_error', 'Gagal memperbarui: ' . $e->getMessage());
        }
        $this->redirect('/application/' . ($_POST['next_tab'] ?? 'members'));
    }
}
