<?php

/** @var array $payments */
/** @var string $csrf_token */
/** @var string $page_title */

use App\Components\Button;
use App\Components\DataTable;
use App\Components\Dialog;
use App\Components\Icon;
use App\Components\Toast;
use App\Utils\Session;

$previewDialog = Dialog::make()->id('previewDialog')->title('Bukti Pembayaran');
$toastSuccess = $toast_success ?? Session::flash('invoice_success');
$toastError = $toast_error ?? Session::flash('invoice_error');

$rows = [];
$allDialogs = '';
foreach ($payments as $p) {
    $statusMap = [
        'verified' => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800">Terverifikasi</span>',
        'rejected' => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-red-100 text-red-800">Ditolak</span>',
        'pending' => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800">Pending</span>',
    ];

    $bukti = !empty($p['proofImage'])
        ? (string) Button::make()->label('Lihat')->variant('link')->size('sm')
            ->attr('onclick', "document.getElementById('previewImg').src='/uploads/payments/" . htmlspecialchars($p['proofImage']) . "';openDialog('previewDialog')")
        : '<span class="text-muted-foreground">-</span>';

    $isPending = $p['status'] === 'pending';
    $isDone = in_array($p['status'], ['verified', 'rejected'], true);
    $isApproved = $p['status'] === 'verified';

    $verifyId = 'dialog-verify-' . $p['id'];
    $rejectId = 'dialog-reject-' . $p['id'];
    $resetId = 'dialog-reset-' . $p['id'];
    $invoiceId = 'dialog-invoice-' . $p['id'];

    $verifyForm = '<form action="/admin/payments/process" method="POST">'
        . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
        . '<input type="hidden" name="payment_id" value="' . $p['id'] . '">'
        . '<input type="hidden" name="action" value="verify">'
        . '<p class="text-sm text-gray-600 mb-6 break-words [overflow-wrap:anywhere]">Apakah Anda yakin ingin memverifikasi pembayaran dari tim <b>' . htmlspecialchars($p['team_name']) . '</b>?</p>'
        . '<div class="flex justify-end space-x-2">'
        . '<button type="button" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300" onclick="closeDialog(\'' . $verifyId . '\')">Batal</button>'
        . '<button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700">Ya, Verifikasi</button>'
        . '</div></form>';

    $rejectForm = '<form action="/admin/payments/process" method="POST">'
        . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
        . '<input type="hidden" name="payment_id" value="' . $p['id'] . '">'
        . '<input type="hidden" name="action" value="reject">'
        . '<p class="text-sm text-gray-600 mb-4 break-words [overflow-wrap:anywhere]">Apakah Anda yakin ingin menolak pembayaran dari tim <b>' . htmlspecialchars($p['team_name']) . '</b>?</p>'
        . '<div class="mb-2"><label class="block text-sm font-medium mb-1">Catatan (opsional)</label><textarea name="note" class="w-full border rounded p-1 min-h-[120px] text-base" placeholder="Opsional: beri catatan penolakan" rows="5"></textarea></div>'
        . '<div class="flex justify-end space-x-2">'
        . '<button type="button" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300" onclick="closeDialog(\'' . $rejectId . '\')">Batal</button>'
        . '<button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-red-700 rounded-lg hover:bg-red-800">Ya, Tolak</button>'
        . '</div></form>';

    $resetForm = '<form action="/admin/payments/process" method="POST">'
        . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
        . '<input type="hidden" name="payment_id" value="' . $p['id'] . '">'
        . '<input type="hidden" name="action" value="cancel">'
        . '<p class="text-sm text-gray-600 mb-6">Reset status pembayaran tim <b>' . htmlspecialchars($p['team_name']) . '</b> menjadi pending?</p>'
        . (!empty($p['note']) ? '<p class="text-xs text-gray-500 mb-4">Alasan sebelumnya: ' . htmlspecialchars($p['note']) . '</p>' : '')
        . '<div class="flex justify-end space-x-2">'
        . '<button type="button" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300" onclick="closeDialog(\'' . $resetId . '\')">Batal</button>'
        . '<button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-amber-600 rounded-lg hover:bg-amber-700">Ya, Reset</button>'
        . '</div></form>';

    $hasInvoice = !empty($p['invoice_file']);
    $invoiceUrl = $hasInvoice ? '/uploads/invoices/' . rawurlencode($p['invoice_file']) : '';
    $invoiceName = htmlspecialchars($p['invoice_original_name'] ?? $p['invoice_file'] ?? '');
    $invoiceDate = !empty($p['invoice_uploaded_at']) ? date('d M Y H:i', strtotime($p['invoice_uploaded_at'])) : '';
    $invoiceExt = strtolower(pathinfo($p['invoice_file'] ?? '', PATHINFO_EXTENSION));
    $isPdf = $invoiceExt === 'pdf';

    $existingBlock = '';
    if ($hasInvoice) {
        $existingBlock = '<div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">'
            . '<div class="w-9 h-9 rounded-lg bg-white border border-green-200 flex items-center justify-center shrink-0">' . Icon::make()->name('file-text')->class('w-5 h-5 text-green-600') . '</div>'
            . '<div class="flex-1 min-w-0">'
            . '<a href="' . htmlspecialchars($invoiceUrl) . '" target="_blank" class="text-sm font-medium text-green-700 hover:underline truncate block">' . $invoiceName . '</a>'
            . '<p class="text-xs text-green-600">Terkirim' . ($invoiceDate ? ' • ' . htmlspecialchars($invoiceDate) : '') . '</p>'
            . '</div>'
            . '<a href="' . htmlspecialchars($invoiceUrl) . '" target="_blank" class="shrink-0 w-8 h-8 rounded-lg bg-white border border-green-200 flex items-center justify-center hover:bg-green-100">' . Icon::make()->name('eye')->class('w-4 h-4 text-green-600') . '</a>'
            . '</div>';
    }

    $dropzoneDisabled = $isApproved ? '' : 'opacity-50 pointer-events-none';
    $dropzone = '<div data-slot="attachment" class="w-full ' . $dropzoneDisabled . '">'
        . '<label class="dropzone relative block w-full h-48 rounded-2xl border-2 border-dashed border-gray-300 bg-transparent hover:border-brand transition-colors overflow-hidden ' . ($isApproved ? 'cursor-pointer' : 'cursor-not-allowed') . '">'
        . '<div data-slot="attachment-idle" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">'
        . Icon::make()->name('upload')->class('size-10 mb-2 text-gray-400')
        . '<p class="text-sm font-semibold text-gray-800">' . ($isApproved ? 'Seret & lepas file ke sini' : 'Menunggu verifikasi') . '</p>'
        . '<p class="text-xs text-gray-500 mt-1">' . ($isApproved ? 'atau klik untuk memilih (PDF, PNG, JPG, maks 5MB)' : 'Invoice hanya untuk pembayaran terverifikasi') . '</p>'
        . '</div>'
        . '<div data-slot="attachment-preview" class="absolute inset-0 hidden bg-white rounded-2xl overflow-hidden">'
        . '<img class="w-full h-full object-contain hidden" alt="Preview invoice">'
        . '<div data-pdf-fallback class="hidden w-full h-full flex-col items-center justify-center p-6 text-center">'
        . Icon::make()->name('file-text')->class('w-12 h-12 text-gray-400 mb-2')
        . '<p class="text-sm font-medium text-gray-700 truncate max-w-[90%]" data-pdf-name></p>'
        . '</div>'
        . '</div>'
        . '<button type="button" data-clear-attachment class="absolute top-3 right-3 z-20 hidden items-center justify-center w-8 h-8 bg-white rounded-full shadow-md border border-gray-200 hover:bg-red-50 hover:border-red-300">' . Icon::make()->name('trash-2')->class('w-4 h-4 text-red-500') . '</button>'
        . '<input type="file" name="invoice" accept="image/*,application/pdf" ' . ($isApproved ? 'required' : 'disabled') . ' data-preview="true" data-max-size="' . (5 * 1024 * 1024) . '" class="absolute inset-0 opacity-0 z-10 ' . ($isApproved ? 'cursor-pointer' : 'cursor-not-allowed') . '">'
        . '</label>'
        . '<div data-slot="attachment-fileinfo" class="hidden mt-2">'
        . '<p class="text-sm font-medium text-gray-900 truncate" data-slot="file-name"></p>'
        . '<p class="text-xs text-gray-500" data-slot="file-size"></p>'
        . '</div>'
        . '<p id="err-invoice-' . $p['id'] . '" class="text-xs text-red-500 mt-1 hidden">Invoice wajib diupload (PDF/gambar, maks 5MB)</p>'
        . '</div>';

    $invoiceConfirmId = 'dialog-confirm-invoice-' . $p['id'];
    $confirmHtml = '<p class="text-sm text-gray-600 mb-4 break-words [overflow-wrap:anywhere]">Apakah Anda yakin ingin mengirim invoice <b class="confirm-filename">-</b> ke tim <b>' . htmlspecialchars($p['team_name']) . '</b> dengan ketua <b>' . htmlspecialchars($p['leaderName']) . '</b>?</p>'
        . '<div class="flex justify-end gap-2">'
        . '<button type="button" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300" onclick="closeDialog(\'' . $invoiceConfirmId . '\')">Batal</button>'
        . '<button type="button" class="px-4 py-2 text-sm font-semibold text-white bg-brand rounded-lg hover:bg-red-800" onclick="confirmInvoice(' . $p['id'] . ')">Ya, Kirim</button>'
        . '</div>';
    $invoiceForm = '<form id="invoice-form-' . $p['id'] . '" action="/admin/payments/invoice" method="POST" enctype="multipart/form-data" onsubmit="return handleInvoiceSubmit(event,' . $p['id'] . ')">'
        . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
        . '<input type="hidden" name="payment_id" value="' . $p['id'] . '">'
        . '<p class="text-sm text-gray-600 mb-4 break-words [overflow-wrap:anywhere]">Kirim invoice kepada peserta, invoice akan dikirim otomatis melalui email mereka</p>'
        . $existingBlock
        . ($isApproved ? '' : '<div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700">Invoice hanya dapat dikirim setelah pembayaran diverifikasi.</div>')
        . '<p class="text-sm font-semibold mb-2">' . ($hasInvoice ? 'Ganti invoice' : 'Upload invoice') . '</p>'
        . '<div class="mb-1">' . $dropzone . '</div>'
        . '<p class="text-xs text-amber-600 mb-4 flex items-center gap-1">' . Icon::make()->name('alert-circle')->class('w-3.5 h-3.5 shrink-0') . ' Pastikan format nama file sesuai</p>'
        . '<div class="flex justify-end space-x-2">'
        . '<button type="button" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300" onclick="closeDialog(\'' . $invoiceId . '\')">Batal</button>'
        . '<button type="submit" ' . ($isApproved ? '' : 'disabled') . ' class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-brand rounded-lg hover:bg-red-800 disabled:opacity-50 disabled:cursor-not-allowed">Kirim Invoice</button>'
        . '</div></form>';

    $dialogHtml = Dialog::make()->id($verifyId)->title('Konfirmasi Verifikasi')->width('max-w-md')->content($verifyForm)->render()
        . Dialog::make()->id($rejectId)->title('Konfirmasi Tolak')->width('max-w-md')->content($rejectForm)->render()
        . Dialog::make()->id($resetId)->title('Reset Status')->width('max-w-md')->content($resetForm)->render()
        . Dialog::make()->id($invoiceId)->title('Kirim Invoice')->width('max-w-md')->content($invoiceForm)->render()
        . Dialog::make()->id($invoiceConfirmId)->title('Konfirmasi Kirim Invoice')->width('max-w-md')->content($confirmHtml)->render();
    $allDialogs .= $dialogHtml;

    $dis = 'opacity-40 cursor-not-allowed pointer-events-none';
    $btnBase = 'rounded p-1.5 transition-colors disabled:opacity-40 disabled:cursor-not-allowed';

    $aksi = '<div class="flex gap-1 items-center">'
        . '<button type="button" ' . ($isPending ? Dialog::make()->id($verifyId)->getOpenAttr() : '') . ' ' . ($isPending ? '' : 'disabled') . ' class="text-green-600 bg-gray-100 hover:bg-gray-200 ' . $btnBase . ' ' . ($isPending ? '' : $dis) . '" data-tooltip="Verifikasi">' . Icon::make()->name('check')->class('w-5 h-5') . '</button>'
        . '<button type="button" ' . ($isPending ? Dialog::make()->id($rejectId)->getOpenAttr() : '') . ' ' . ($isPending ? '' : 'disabled') . ' class="text-red-600 bg-gray-100 hover:bg-gray-200 ' . $btnBase . ' ' . ($isPending ? '' : $dis) . '" data-tooltip="Tolak">' . Icon::make()->name('x')->class('w-5 h-5') . '</button>'
        . '<button type="button" ' . ($isDone ? Dialog::make()->id($resetId)->getOpenAttr() : '') . ' ' . ($isDone ? '' : 'disabled') . ' class="text-amber-600 bg-gray-100 hover:bg-gray-200 ' . $btnBase . ' ' . ($isDone ? '' : $dis) . '" data-tooltip="Reset Status">' . Icon::make()->name('rotate-cw')->class('w-5 h-5') . '</button>'
        . '<button type="button" ' . ($isApproved ? Dialog::make()->id($invoiceId)->getOpenAttr() : '') . ' ' . ($isApproved ? '' : 'disabled') . ' class="text-gray-700 bg-gray-100 hover:bg-gray-200 ' . $btnBase . ($hasInvoice ? ' ring-2 ring-green-400' : '') . ' ' . ($isApproved ? '' : $dis) . '" data-tooltip="' . ($isApproved ? 'Atur Invoice' : 'Menunggu verifikasi') . '">' . Icon::make()->name('more-horizontal')->class('w-5 h-5 rotate-90') . '</button>'
        . '</div>';

    $rows[] = [
        'team_name' => htmlspecialchars($p['team_name']),
        'school' => htmlspecialchars($p['teamSchool'] ?? '-'),
        'divisi' => htmlspecialchars($p['division']),
        'leader' => htmlspecialchars($p['leaderName']),
        'leaderPhone' => htmlspecialchars($p['leaderPhoneNumber']),
        'status' => $statusMap[$p['status']] ?? $p['status'],
        'bukti' => $bukti,
        'submitted' => date('d M Y H:i', strtotime($p['submittedAt'])),
        'aksi' => $aksi,
    ];
}
?>

<?php include BASE_PATH . '/src/Components/page-loading.php'; ?>
<?php if (!empty($toastSuccess)): ?>
    <div class="fixed bottom-4 right-4 z-[60] w-full max-w-sm"><?= Toast::make()->variant('success')->title('Berhasil')->message($toastSuccess)->render() ?></div>
<?php elseif (!empty($toastError)): ?>
    <div class="fixed bottom-4 right-4 z-[60] w-full max-w-sm"><?= Toast::make()->variant('error')->title('Gagal')->message($toastError)->render() ?></div>
<?php endif; ?>
<div class="rounded-xl border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="text-2xl font-semibold leading-none tracking-tight">Status Pembayaran Tim</h3>
        <p class="text-sm text-muted-foreground">Kelola verifikasi pembayaran dari setiap tim.</p>
    </div>
    <div class="p-6 pt-0">
        <?= DataTable::make()
            ->columns([
                ['key' => 'team', 'label' => 'Tim', 'render' => fn($row) => '<div class="font-medium">' . $row['team_name'] . '</div>'],
                ['key' => 'school', 'label' => 'Sekolah', 'render' => fn($row) => $row['school'] ?? '-'],
                ['key' => 'divisi', 'label' => 'Kategori', 'render' => fn($row) => '<span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-secondary text-secondary-foreground">' . $row['divisi'] . '</span>'],
                ['key' => 'ketua', 'label' => 'Anggota 1 (Ketua)', 'render' => fn($row) => '<div class="font-medium">' . $row['leader'] . '</div>'],
                ['key' => 'leaderPhone', 'label' => 'No. HP', 'render' => fn($row) => $row['leaderPhone']],
                ['key' => 'status', 'label' => 'Status', 'sortable' => false, 'render' => fn($row) => $row['status']],
                ['key' => 'bukti', 'label' => 'Bukti', 'sortable' => false, 'render' => fn($row) => $row['bukti']],
                ['key' => 'submitted', 'label' => 'Dikirim', 'tdClass' => 'text-muted-foreground'],
                ['key' => 'aksi', 'label' => 'Aksi', 'sortable' => false, 'render' => fn($row) => $row['aksi']],
            ])
            ->searchable()
            ->columnSelectable()
            ->pageable()
            ->rows($rows)
            ->emptyText('Belum ada pembayaran.')
            ->render() ?>
    </div>
</div>

<?= $previewDialog->content('<img id="previewImg" src="" alt="Preview Bukti Pembayaran" class="w-full h-auto max-h-[75vh] object-contain rounded-md">')->render() ?>
<?= $allDialogs ?>

<script>
    window.handleInvoiceSubmit = function(e, id) {
        var form = document.getElementById('invoice-form-' + id);
        if (form && form.dataset.confirmed === '1') {
            if (form.dataset.submitting === '1') return false;
            form.dataset.submitting = '1';
            if (window.__showLoading) window.__showLoading();
            var b = form.querySelector('[type=submit]');
            if (b) b.disabled = true;
            return true;
        }
        e.preventDefault();
        var input = form ? form.querySelector('input[name="invoice"]') : null;
        if (!input || !input.files || !input.files[0]) {
            var err = document.getElementById('err-invoice-' + id);
            if (err) {
                err.textContent = 'Pilih file invoice terlebih dahulu';
                err.classList.remove('hidden');
            }
            return false;
        }
        var dlg = document.getElementById('dialog-confirm-invoice-' + id);
        if (dlg) {
            var fn = dlg.querySelector('.confirm-filename');
            if (fn) fn.textContent = input.files[0].name;
        }
        openDialog('dialog-confirm-invoice-' + id);
        return false;
    };
    window.confirmInvoice = function(id) {
        closeDialog('dialog-confirm-invoice-' + id);
        var form = document.getElementById('invoice-form-' + id);
        if (!form) return;
        form.dataset.confirmed = '1';
        if (form.dataset.submitting === '1') return;
        form.dataset.submitting = '1';
        if (window.__showLoading) window.__showLoading();
        var b = form.querySelector('[type=submit]');
        if (b) b.disabled = true;
        form.submit();
    };
    (function() {
        var _origClose = window.closeDialog;
        window.closeDialog = function(id) {
            if (id && id.indexOf('dialog-invoice-') === 0) {
                var pid = id.replace('dialog-invoice-', '');
                var form = document.getElementById('invoice-form-' + pid);
                if (form) {
                    var input = form.querySelector('input[name="invoice"]');
                    if (input) input.value = '';
                    var att = form.querySelector('[data-slot="attachment"]');
                    if (att) {
                        var idle = att.querySelector('[data-slot="attachment-idle"]'),
                            preview = att.querySelector('[data-slot="attachment-preview"]'),
                            img = preview && preview.querySelector('img'),
                            pdf = att.querySelector('[data-pdf-fallback]'),
                            info = att.querySelector('[data-slot="attachment-fileinfo"]'),
                            clear = att.querySelector('[data-clear-attachment]'),
                            err = document.getElementById('err-invoice-' + pid);
                        if (idle) idle.classList.remove('hidden');
                        if (preview) preview.classList.add('hidden');
                        if (img) {
                            img.src = '';
                            img.classList.add('hidden');
                        }
                        if (pdf) {
                            pdf.classList.add('hidden');
                            pdf.classList.remove('flex');
                        }
                        if (info) info.classList.add('hidden');
                        if (clear) {
                            clear.classList.add('hidden');
                            clear.classList.remove('flex');
                        }
                        if (err) err.classList.add('hidden');
                    }
                    form.dataset.confirmed = '';
                    form.dataset.submitting = '';
                }
            }
            if (_origClose) return _origClose(id);
        };
    })();
    document.addEventListener('DOMContentLoaded', function() {
        var toast = document.getElementById('flash-toast');
        if (toast) {
            requestAnimationFrame(function() {
                toast.classList.remove('opacity-0', '-translate-x-2');
            });
            var close = function() {
                toast.classList.add('opacity-0', '-translate-x-2');
                setTimeout(function() {
                    toast.remove();
                }, 300);
            };
            var btn = toast.querySelector('[data-toast-close]');
            if (btn) btn.addEventListener('click', close);
            setTimeout(close, 15000);
        }

        function fmt(b) {
            if (!b) return '';
            if (b < 1024) return b + ' B';
            if (b < 1024 * 1024) return (b / 1024).toFixed(1) + ' KB';
            return (b / (1024 * 1024)).toFixed(2) + ' MB';
        }
        document.addEventListener('change', function(e) {
            var input = e.target;
            if (!input.matches('[data-preview][name="invoice"]')) return;
            var file = input.files && input.files[0];
            if (!file) return;
            var att = input.closest('[data-slot="attachment"]');
            if (!att) return;
            var max = input.dataset.maxSize;
            if (max && file.size > parseInt(max)) {
                input.value = '';
                var err = att.querySelector('[id^="err-invoice"]') || document.getElementById('err-invoice-' + (input.closest('form').querySelector('[name="payment_id"]')?.value || ''));
                if (err) {
                    err.textContent = 'File terlalu besar. Maksimal ' + fmt(parseInt(max));
                    err.classList.remove('hidden');
                }
                return;
            }
            var idle = att.querySelector('[data-slot="attachment-idle"]'),
                preview = att.querySelector('[data-slot="attachment-preview"]'),
                img = preview.querySelector('img'),
                pdf = preview.querySelector('[data-pdf-fallback]'),
                pdfName = preview.querySelector('[data-pdf-name]'),
                clear = att.querySelector('[data-clear-attachment]'),
                info = att.querySelector('[data-slot="attachment-fileinfo"]');
            if (idle) idle.classList.add('hidden');
            if (preview) preview.classList.remove('hidden');
            if (clear) {
                clear.classList.remove('hidden');
                clear.classList.add('flex');
            }
            if (info) {
                info.classList.remove('hidden');
                var fn = info.querySelector('[data-slot="file-name"]');
                if (fn) fn.textContent = file.name;
                var fs = info.querySelector('[data-slot="file-size"]');
                if (fs) fs.textContent = file.name.split('.').pop().toUpperCase() + ' • ' + fmt(file.size);
            }
            var isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
            if (isPdf) {
                if (img) img.classList.add('hidden');
                if (pdf) {
                    pdf.classList.remove('hidden');
                    pdf.classList.add('flex');
                    if (pdfName) pdfName.textContent = file.name;
                }
            } else {
                if (pdf) {
                    pdf.classList.add('hidden');
                    pdf.classList.remove('flex');
                }
                if (img) {
                    img.classList.remove('hidden');
                    var r = new FileReader();
                    r.onload = function(ev) {
                        img.src = ev.target.result;
                    };
                    r.readAsDataURL(file);
                }
            }
        });
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('[data-clear-attachment]');
            if (!btn) return;
            e.preventDefault();
            var att = btn.closest('[data-slot="attachment"]');
            if (!att) return;
            var input = att.querySelector('input[type="file"]');
            if (input) input.value = '';
            var idle = att.querySelector('[data-slot="attachment-idle"]'),
                preview = att.querySelector('[data-slot="attachment-preview"]'),
                img = preview.querySelector('img'),
                pdf = preview.querySelector('[data-pdf-fallback]'),
                info = att.querySelector('[data-slot="attachment-fileinfo"]');
            if (idle) idle.classList.remove('hidden');
            if (preview) preview.classList.add('hidden');
            if (img) {
                img.src = '';
                img.classList.add('hidden');
            }
            if (pdf) {
                pdf.classList.add('hidden');
                pdf.classList.remove('flex');
            }
            if (info) info.classList.add('hidden');
            btn.classList.add('hidden');
            btn.classList.remove('flex');
        });
        document.querySelectorAll('.dropzone').forEach(function(z) {
            z.addEventListener('dragover', function(e) {
                e.preventDefault();
                z.classList.add('border-brand', 'bg-red-50');
            });
            z.addEventListener('dragleave', function() {
                z.classList.remove('border-brand', 'bg-red-50');
            });
            z.addEventListener('drop', function(e) {
                e.preventDefault();
                z.classList.remove('border-brand', 'bg-red-50');
                var input = z.querySelector('input[type="file"]');
                if (input && e.dataTransfer.files.length) {
                    input.files = e.dataTransfer.files;
                    input.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                }
            });
        });
    });
</script>