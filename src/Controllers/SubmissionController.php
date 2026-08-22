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
    private const DOC_TYPES = ['abstract', 'full_paper'];
    private const ALLOWED_EXT = ['pdf'];
    private const MAX_SIZE = 100 * 1024 * 1024;

    public function abstractPage()
    {
        $this->requireAuth();
        if (Session::get('role') === 'admin') {
            $this->redirect('/admin/dashboard');
        }

        $team = $this->lktiTeam();
        if (!$team) return;

        $this->view('dashboard/submission', $this->lktiViewData($team, 'abstract'));
    }

    public function fullPaperPage()
    {
        $this->requireAuth();
        if (Session::get('role') === 'admin') {
            $this->redirect('/admin/dashboard');
        }

        $team = $this->lktiTeam();
        if (!$team) return;

        $this->view('dashboard/submission', $this->lktiViewData($team, 'full_paper'));
    }

    public function uploadAbstract()
    {
        $this->handleLktiUpload('abstract');
    }

    public function uploadFullPaper()
    {
        $this->handleLktiUpload('full_paper');
    }

    private function handleLktiUpload(string $type)
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->redirect('/home');
        }

        $team = $this->lktiTeam();
        if (!$team) return;

        $url = '/submission/' . ($type === 'abstract' ? 'abstract' : 'full-paper');

        $submissionModel = new Submission();
        $is_reviewed = $submissionModel->isReviewed($team['id']);
        if (!$is_reviewed) {
            Session::flash('submission_error', 'Selesaikan pendaftaran tim terlebih dahulu.');
            $this->redirect($url);
        }

        if ($type === 'full_paper') {
            $abstract = $submissionModel->findByTeamAndType($team['id'], 'abstract');
            $payment = (new Payment())->findByTeamId($team['id']);
            if (!$abstract || $abstract['status'] !== 'approved') {
                Session::flash('submission_error', 'Abstrak kamu belum disetujui admin.');
                $this->redirect($url);
            }
            if (!$payment || $payment['status'] !== 'verified') {
                Session::flash('submission_error', 'Selesaikan pembayaran terlebih dahulu.');
                $this->redirect($url);
            }
        }

        $category = $_POST['category'] ?? '';
        if (!in_array($category, ['gagasan', 'prototype'], true)) {
            Session::flash('submission_error', 'Pilih kategori karya terlebih dahulu (Gagasan atau Prototype).');
            $this->redirect($url);
        }

        $existing = $submissionModel->findByTeamAndType($team['id'], $type);
        $file = $_FILES['doc_file'] ?? [];
        $hasNewFile = isset($file['error']) && $file['error'] === UPLOAD_ERR_OK;

        if (!$hasNewFile) {
            if (!$existing || $existing['category'] === $category) {
                Session::flash('submission_error', 'Pilih file untuk diupload.');
                $this->redirect($url);
            }
            $submissionModel->upsert($team['id'], $type, $existing['value'], $existing['status'], $category);
            Session::flash('submission_success', 'Kategori karya berhasil diperbarui.');
            $this->redirect($url);
        }

        $filename = $this->storeDoc($file, $team, $type);
        if ($filename === null) {
            $this->redirect($url);
        }

        $submissionModel->upsert($team['id'], $type, $filename, 'submitted', $category);
        Session::flash('submission_success', ucfirst(str_replace('_', ' ', $type)) . ' berhasil diupload!');
        $this->redirect($url);
    }

    private function storeDoc(array $file, array $team, string $type): ?string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('submission_error', 'Pilih file untuk diupload.');
            return null;
        }
        if ($file['size'] > self::MAX_SIZE) {
            Session::flash('submission_error', 'File terlalu besar. Maksimal 100MB.');
            return null;
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            Session::flash('submission_error', 'Format file harus PDF.');
            return null;
        }

        $uploadDir = BASE_PATH . '/public/uploads/submissions';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $team['name']), '-'));

        $filename = trim(preg_replace('/[\x00-\x1F\/\\\\]+/', '', basename($file['name'])));
        if ($filename === '') {
            $filename = $slug . '_' . $type . '_' . date('Ymd_Hi') . '.' . $ext;
        } elseif (file_exists($uploadDir . '/' . $filename)) {
            $filename = $slug . '_' . $filename;
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename)) {
            Session::flash('submission_error', 'Gagal menyimpan file.');
            return null;
        }

        return $filename;
    }

    private function lktiTeam(): array|false
    {
        $team = (new Team())->findByUserId(Session::get('user_id'));
        if (!$team || $team['division'] !== 'LKTI') {
            $this->redirect('/home');
            return false;
        }
        return $team;
    }

    private function lktiViewData(array $team, string $type): array
    {
        $submissionModel = new Submission();
        $payment = (new Payment())->findByTeamId($team['id']);

        return [
            'user_name' => Session::get('user_name'),
            'csrf_token' => Security::generateCsrfToken(),
            'team' => $team,
            'payment' => $payment,
            'is_reviewed' => $submissionModel->isReviewed($team['id']),
            'type' => $type,
            'submission' => $submissionModel->findByTeamAndType($team['id'], $type),
            'abstract_status' => $submissionModel->findByTeamAndType($team['id'], 'abstract')['status'] ?? null,
            'success' => Session::flash('submission_success'),
            'error' => Session::flash('submission_error'),
        ];
    }

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

        Session::flash('submission_success', 'Karya berhasil diupload!');
        $this->redirect('/application/review');
    }
}
