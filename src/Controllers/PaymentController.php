<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Team;
use App\Models\Payment;
use App\Utils\Session;
use App\Utils\Security;

class PaymentController extends Controller
{
    private $teamModel;
    private $paymentModel;

    public function __construct()
    {
        $this->teamModel = new Team();
        $this->paymentModel = new Payment();
    }

    public function uploadForm()
    {
        $this->requireAuth();

        $team = $this->teamModel->findByUserId(Session::get('user_id'));
        if (!$team) {
            $this->redirect('/dashboard/team/register');
        }

        $payment = $this->paymentModel->findByTeamId($team['id']);
        if ($payment && $payment['status'] === 'verified') {
            $this->redirect('/dashboard');
        }

        $this->view('dashboard/payment', [
            'csrf_token' => Security::generateCsrfToken(),
            'team' => $team,
            'payment' => $payment,
            'error' => null
        ]);
    }

    public function upload()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard/payment');
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->view('dashboard/payment', ['error' => 'Invalid CSRF token', 'csrf_token' => Security::generateCsrfToken()]);
            return;
        }

        $team = $this->teamModel->findByUserId(Session::get('user_id'));
        if (!$team) {
            $this->redirect('/dashboard/team/register');
        }

        $existingPayment = $this->paymentModel->findByTeamId($team['id']);
        if ($existingPayment && $existingPayment['status'] === 'verified') {
            $this->redirect('/dashboard');
        }

        if (!isset($_FILES['proofImage']) || $_FILES['proofImage']['error'] !== UPLOAD_ERR_OK) {
            $this->view('dashboard/payment', ['error' => 'Please select a file to upload.', 'csrf_token' => Security::generateCsrfToken(), 'team' => $team, 'payment' => $this->paymentModel->findByTeamId($team['id'])]);
            return;
        }

        $file = $_FILES['proofImage'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024;

        if ($file['size'] > $maxSize) {
            $this->view('dashboard/payment', ['error' => 'File too large. Max 2MB.', 'csrf_token' => Security::generateCsrfToken(), 'team' => $team, 'payment' => $this->paymentModel->findByTeamId($team['id'])]);
            return;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);

        if (!in_array($mime, $allowedTypes)) {
            $this->view('dashboard/payment', ['error' => 'Only images (JPEG, PNG, GIF, WebP) are allowed.', 'csrf_token' => Security::generateCsrfToken(), 'team' => $team, 'payment' => $this->paymentModel->findByTeamId($team['id'])]);
            return;
        }

        $uploadDir = BASE_PATH . '/public/uploads/payments';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $team['name']), '-'));
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $slug . '_' . date('Ymd_Hi') . '.' . $ext;
        $dest = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $this->view('dashboard/payment', ['error' => 'Failed to save file.', 'csrf_token' => Security::generateCsrfToken(), 'team' => $team, 'payment' => $this->paymentModel->findByTeamId($team['id'])]);
            return;
        }

        if ($existingPayment) {
            $oldFile = $uploadDir . '/' . basename($existingPayment['proofImage']);
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            $stmt = \App\Core\Database::getInstance()->getConnection()->prepare("UPDATE payments SET proofImage = :proofImage, status = 'pending', note = NULL, submittedAt = NOW() WHERE id = :id");
            $stmt->execute([':proofImage' => $filename, ':id' => $existingPayment['id']]);
        } else {
            $this->paymentModel->create($team['id'], $filename);
        }

        $this->redirect('/dashboard');
    }
}
