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

    public function upload()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/payments');
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::flash('payment_error', 'Invalid session. Silakan coba lagi.');
            $this->redirect('/payments');
            return;
        }

        $team = $this->teamModel->findByUserId(Session::get('user_id'));
        if (!$team) {
            $this->redirect('/application/team-register');
        }

        $existingPayment = $this->paymentModel->findByTeamId($team['id']);
        if ($existingPayment && $existingPayment['status'] === 'verified') {
            $this->redirect('/payments');
        }

        if (!isset($_FILES['proofImage']) || $_FILES['proofImage']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('payment_error', 'Pilih file untuk diupload.');
            $this->redirect('/payments');
            return;
        }

        $file = $_FILES['proofImage'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024;

        if ($file['size'] > $maxSize) {
            Session::flash('payment_error', 'File terlalu besar. Maksimal 2MB.');
            $this->redirect('/payments');
            return;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);

        if (!in_array($mime, $allowedTypes)) {
            Session::flash('payment_error', 'Hanya gambar (JPEG, PNG, GIF, WebP) yang diizinkan.');
            $this->redirect('/payments');
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
            Session::flash('payment_error', 'Gagal menyimpan file.');
            $this->redirect('/payments');
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

        $this->redirect('/payments');
    }
}
