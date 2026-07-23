<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Team;
use App\Models\Payment;
use App\Models\Submission;
use App\Utils\Session;
use App\Utils\Security;

class SubmissionController extends Controller
{
    public function upload()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/application/review');
        }

        $team = (new Team())->findByUserId(Session::get('user_id'));
        if (!$team) {
            $this->redirect('/application/team-register');
        }

        $payment = (new Payment())->findByTeamId($team['id']);
        if (!$payment || $payment['status'] !== 'verified') {
            $this->redirect('/application/review');
        }

        $model = new Submission();
        $division = $team['division'];

        if ($division === 'FFR') {
            $link = trim($_POST['youtube_link'] ?? '');
            if (empty($link)) {
                Session::flash('submission_error', 'Link YouTube wajib diisi.');
                $this->redirect('/application/review');
                return;
            }
            $model->upsert($team['id'], 'youtube_link', $link);
        } else {
            if (!isset($_FILES['submission_file']) || $_FILES['submission_file']['error'] !== UPLOAD_ERR_OK) {
                Session::flash('submission_error', 'Pilih file untuk diupload.');
                $this->redirect('/application/review');
                return;
            }

            $file = $_FILES['submission_file'];
            if ($file['size'] > 1000 * 1024 * 1024) {
                Session::flash('submission_error', 'File terlalu besar. Maksimal 10MB.');
                $this->redirect('/application/review');
                return;
            }

            $uploadDir = BASE_PATH . '/public/uploads/submissions';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $team['name']), '-'));
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = $slug . '_' . date('Ymd_Hi') . '.' . $ext;

            if (!move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename)) {
                Session::flash('submission_error', 'Gagal menyimpan file.');
                $this->redirect('/application/review');
                return;
            }

            $model->upsert($team['id'], 'file', $filename);
        }

        Session::flash('submission_success', 'Karya berhasil diupload!');
        $this->redirect('/application/review');
    }
}
