<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Submission;
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
        $leaderGender = $_POST['leaderGender'] ?? null;
        $firstMemberName = trim($_POST['firstMemberName'] ?? '');
        $firstMemberPhoneNumber = trim($_POST['firstMemberPhoneNumber'] ?? '');
        $firstMemberGender = $_POST['firstMemberGender'] ?? null;
        $secondMemberName = trim($_POST['secondMemberName'] ?? '');
        $secondMemberPhoneNumber = trim($_POST['secondMemberPhoneNumber'] ?? '');
        $secondMemberGender = $_POST['secondMemberGender'] ?? null;

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
                'leaderGender' => $leaderGender,
                'firstMemberName' => $firstMemberName,
                'firstMemberPhoneNumber' => $firstMemberPhoneNumber,
                'firstMemberGender' => $firstMemberGender,
                'secondMemberName' => $secondMemberName,
                'secondMemberPhoneNumber' => $secondMemberPhoneNumber,
                'secondMemberGender' => $secondMemberGender,
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

        $this->persist($team, $_POST, $_FILES);

        Session::flash('team_update_success', 'Data tim berhasil diperbarui!');
        $this->redirect('/application/' . ($_POST['next_tab'] ?? 'members'));
    }

    public function submit()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/application/review');
            return;
        }

        $team = $this->teamModel->findByUserId(Session::get('user_id'));
        if (!$team) {
            $this->redirect('/application/team-register');
            return;
        }

        $missing = $this->missingData($team);
        if ($missing) {
            Session::flash('team_update_error', 'Masih ada data yang belum lengkap: ' . implode(', ', $missing));
            $this->redirect('/application/review');
            return;
        }

        $this->persist($team, $_POST, $_FILES);

        (new Submission())->upsert($team['id'], 'application', 'submitted');
        Session::flash('team_update_success', 'Data tim berhasil dikirim!');
        $this->redirect('/application/review');
    }

    private function persist(array $team, array $post, array $files): void
    {
        $data = [
            'name' => trim($post['name'] ?? $team['name']),
            'teamSchool' => trim($post['teamSchool'] ?? $team['teamSchool'] ?? ''),
            'division' => $post['division'] ?? $team['division'],
            'leaderName' => trim($post['leaderName'] ?? $team['leaderName'] ?? ''),
            'leaderPhoneNumber' => trim($post['leaderPhoneNumber'] ?? $team['leaderPhoneNumber'] ?? ''),
            'leaderGender' => array_key_exists('leaderGender', $post) ? $post['leaderGender'] : ($team['leaderGender'] ?? null),
            'firstMemberName' => trim($post['firstMemberName'] ?? $team['firstMemberName'] ?? ''),
            'firstMemberPhoneNumber' => trim($post['firstMemberPhoneNumber'] ?? $team['firstMemberPhoneNumber'] ?? ''),
            'firstMemberGender' => array_key_exists('firstMemberGender', $post) ? $post['firstMemberGender'] : ($team['firstMemberGender'] ?? null),
            'secondMemberName' => trim($post['secondMemberName'] ?? $team['secondMemberName'] ?? ''),
            'secondMemberPhoneNumber' => trim($post['secondMemberPhoneNumber'] ?? $team['secondMemberPhoneNumber'] ?? ''),
            'secondMemberGender' => array_key_exists('secondMemberGender', $post) ? $post['secondMemberGender'] : ($team['secondMemberGender'] ?? null),
        ];

        if (empty($data['firstMemberName'])) $data['firstMemberGender'] = null;
        if (empty($data['secondMemberName'])) $data['secondMemberGender'] = null;

        if (empty($data['name']) || empty($data['leaderName'])) {
            Session::flash('team_update_error', 'Nama tim dan ketua harus diisi.');
            $this->redirect('/application/' . ($post['current_tab'] ?? 'members'));
        }

        $this->teamModel->update($team['id'], $data);

        $uploadDir = BASE_PATH . '/public/uploads/teams/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $columnMap = ['studentCard' => 'student_card', 'igFollow' => 'ig_follow', 'twibbon' => 'twibbon'];
        $members = [1, 2, 3];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
        $maxSize = 10 * 1024 * 1024;
        $errors = [];

        foreach ($members as $to) {
            for ($from = 2; $from <= 3; $from++) {
                if ($from <= $to) continue;
                if (!empty($post['move_member_' . $from . '_to_' . $to])) {
                    $this->uploadModel->moveToMember($team['id'], $from, $to, $uploadDir);
                }
            }
        }

        foreach ($members as $m) {
            foreach ($columnMap as $inputName => $column) {
                $key = $inputName . '_' . $m;

                if (!empty($post['delete_' . $key])) {
                    $existing = $this->uploadModel->findOne($team['id'], $m);
                    if ($existing && $existing[$column]) {
                        $oldFile = $existing[$column];
                        if (file_exists($uploadDir . $oldFile)) unlink($uploadDir . $oldFile);
                        $this->uploadModel->upsertColumn($team['id'], $m, $column, null, null);
                    }
                }

                if (empty($files[$key])) continue;
                $fileErr = $files[$key]['error'];
                if ($fileErr === UPLOAD_ERR_NO_FILE) continue;
                if ($fileErr === UPLOAD_ERR_INI_SIZE || $fileErr === UPLOAD_ERR_FORM_SIZE) {
                    $errors[] = 'File ' . $key . ' terlalu besar. Maksimal 10MB.';
                    continue;
                }
                if ($fileErr !== UPLOAD_ERR_OK) continue;

                $file = $files[$key];
                if ($file['size'] > $maxSize) {
                    $errors[] = 'File ' . $key . ' terlalu besar. Maksimal 10MB.';
                    continue;
                }
                if (!in_array($file['type'], $allowedTypes)) {
                    $errors[] = 'File ' . $key . ' harus berupa gambar (JPEG, PNG, GIF, WebP).';
                    continue;
                }

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

        if ($errors) {
            Session::flash('team_update_error', implode('<br>', $errors));
            $this->redirect('/application/' . ($post['next_tab'] ?? 'members'));
        }
    }

    private function missingData(array $team): array
    {
        $labels = [
            'name' => 'Nama tim',
            'teamSchool' => 'Asal sekolah',
            'division' => 'Divisi',
            'leaderName' => 'Nama ketua',
            'leaderGender' => 'Jenis kelamin ketua',
        ];
        $missing = [];
        foreach ($labels as $key => $label) {
            if (empty($team[$key])) $missing[] = $label;
        }

        $need = in_array($team['division'] ?? '', ['FFR', 'LKTI']) ? 3 : 2;
        $nameKeys = [1 => 'leaderName', 2 => 'firstMemberName', 3 => 'secondMemberName'];
        $phoneKeys = [1 => 'leaderPhoneNumber', 2 => 'firstMemberPhoneNumber', 3 => 'secondMemberPhoneNumber'];
        $uploads = [];
        foreach ($this->uploadModel->findByTeam($team['id']) as $r) {
            $uploads[$r['member_number']] = $r;
        }
        $uploadLabels = ['student_card' => 'Kartu pelajar', 'ig_follow' => 'Bukti IG', 'twibbon' => 'Twibbon'];
        for ($i = 1; $i <= $need; $i++) {
            $memberLabel = $i === 1 ? 'ketua' : 'anggota ' . $i;
        if (empty($team[$nameKeys[$i]])) {
            $missing[] = 'Data ' . $memberLabel;
            continue;
        }
        if (empty($team[$phoneKeys[$i]])) $missing[] = 'No. telepon ' . $memberLabel;
        foreach (['student_card', 'ig_follow', 'twibbon'] as $col) {
                if (empty($uploads[$i][$col])) $missing[] = $uploadLabels[$col] . ' ' . $memberLabel;
            }
        }
        return $missing;
    }
}
