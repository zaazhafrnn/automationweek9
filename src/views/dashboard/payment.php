<?php /** @var string $csrf_token */ /** @var array $team */ /** @var array|null $payment */ /** @var string|null $error */ ?>
<div class="bg-white shadow-xl rounded-2xl overflow-hidden max-w-4xl mx-auto mt-8">
    <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Upload Bukti Pembayaran</h1>
        <a href="/dashboard" class="text-sm font-medium text-blue-600 hover:text-blue-500">Kembali ke Dashboard</a>
    </div>

    <div class="px-6 py-8">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800">
                <strong>Tim:</strong> <?= htmlspecialchars($team['name']) ?> —
                <strong>Divisi:</strong> <?= htmlspecialchars($team['division']) ?>
            </p>
        </div>

        <?php if (isset($error) && $error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
                <p class="text-sm"><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($payment) && $payment && $payment['status'] === 'pending'): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 flex items-center">
                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-yellow-800">Pembayaran sebelumnya sedang menunggu verifikasi. Anda dapat mengunggah ulang bukti pembayaran.</p>
            </div>
        <?php endif; ?>

        <form action="/dashboard/payment" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

            <div>
                <label for="proofImage" class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Bukti Transfer <span class="text-red-500">*</span>
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-red-400 transition cursor-pointer" id="dropzone">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="proofImage" class="relative cursor-pointer rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none">
                                <span>Pilih file</span>
                                <input id="proofImage" name="proofImage" type="file" class="sr-only" accept="image/*" required>
                            </label>
                            <p class="pl-1">atau seret dan lepas</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF, WebP — maks 2MB</p>
                    </div>
                </div>
                <div id="filePreview" class="mt-2 hidden">
                    <p class="text-sm text-gray-600" id="fileName"></p>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200">
                <button type="submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#bc0301] hover:bg-[#bc0301]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                    Upload Bukti Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('proofImage');
        const dropzone = document.getElementById('dropzone');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileName.textContent = 'File: ' + this.files[0].name + ' (' + (this.files[0].size / 1024).toFixed(1) + ' KB)';
                filePreview.classList.remove('hidden');
            } else {
                filePreview.classList.add('hidden');
            }
        });

        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-red-400', 'bg-red-50');
        });

        dropzone.addEventListener('dragleave', function() {
            this.classList.remove('border-red-400', 'bg-red-50');
        });

        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-red-400', 'bg-red-50');
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                fileName.textContent = 'File: ' + e.dataTransfer.files[0].name + ' (' + (e.dataTransfer.files[0].size / 1024).toFixed(1) + ' KB)';
                filePreview.classList.remove('hidden');
            }
        });
    });
</script>
