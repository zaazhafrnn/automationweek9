<?php

/** @var string $csrf_token */
/** @var string $user_name */
/** @var array|null $team */
/** @var array|null $payment */
/** @var array|null $submission */ ?>
<div class="bg-white shadow-xl rounded-2xl overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <form action="/logout" method="POST" class="m-0">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
            <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150">
                Logout
            </button>
        </form>
    </div>

    <div class="px-6 py-8">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Welcome, <?= htmlspecialchars($user_name ?? '') ?>! 👋</h2>

        <div class="mt-8">
            <?php if (isset($team) && $team): ?>
                <div class="bg-green-50 border border-green-200 rounded-xl p-6 shadow-sm">
                    <h3 class="text-xl font-bold text-green-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tim Anda Sudah Terdaftar!
                    </h3>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-green-100">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Nama Tim</p>
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($team['name']) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Asal Sekolah</p>
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($team['teamSchool'] ?? '-') ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Divisi</p>
                                <p class="font-semibold text-gray-800">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= htmlspecialchars($team['division']) ?>
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Ketua Tim</p>
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($team['leaderName']) ?></p>
                                <p class="text-xs text-gray-500"><?= htmlspecialchars($team['leaderPhoneNumber'] ?? '') ?></p>
                            </div>
                            <?php if (!empty($team['firstMemberName'])): ?>
                                <div>
                                    <p class="text-sm text-gray-500">Anggota 1</p>
                                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($team['firstMemberName']) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($team['firstMemberPhoneNumber'] ?? '') ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($team['secondMemberName'])): ?>
                                <div>
                                    <p class="text-sm text-gray-500">Anggota 2</p>
                                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($team['secondMemberName']) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($team['secondMemberPhoneNumber'] ?? '') ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if (!isset($payment) || !$payment): ?>
                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-yellow-900 mb-2">Upload Bukti Pembayaran</h3>
                        <p class="text-yellow-800 mb-4">Tim Anda sudah terdaftar. Silakan upload bukti pembayaran untuk menyelesaikan pendaftaran.</p>
                        <a href="/dashboard/payment" class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-brand hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150">
                            Upload Bukti Pembayaran
                        </a>
                    </div>
                <?php elseif ($payment['status'] === 'pending'): ?>
                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-xl p-6 shadow-sm flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-yellow-900 mb-1">Pembayaran Menunggu Verifikasi</h3>
                            <p class="text-sm text-yellow-800">Bukti pembayaran Anda sedang diperiksa oleh admin.</p>
                        </div>
                        <a href="/dashboard/payment" class="inline-flex items-center px-4 py-2 border border-yellow-300 text-sm font-medium rounded-lg text-yellow-800 bg-white hover:bg-yellow-50 transition">
                            Ganti File
                        </a>
                    </div>
                <?php elseif ($payment['status'] === 'verified'): ?>
                    <div class="mt-6 bg-green-50 border border-green-200 rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-green-900 mb-1 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pembayaran Terverifikasi
                        </h3>
                        <p class="text-sm text-green-800">Pembayaran Anda telah dikonfirmasi.</p>
                    </div>
                <?php elseif ($payment['status'] === 'rejected'): ?>
                    <div class="mt-6 bg-red-50 border border-red-200 rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-red-900 mb-1">Pembayaran Ditolak</h3>
                        <?php if (!empty($payment['note'])): ?>
                            <p class="text-sm text-red-800 mb-3">Alasan: <?= htmlspecialchars($payment['note']) ?></p>
                        <?php else: ?>
                            <p class="text-sm text-red-800 mb-3">Bukti pembayaran tidak valid. Silakan upload ulang.</p>
                        <?php endif; ?>
                        <a href="/dashboard/payment" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-brand hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150">
                            Upload Ulang
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($payment && $payment['status'] === 'verified'): ?>
                    <?php $submission_error = \App\Utils\Session::flash('submission_error');
                    $submission_success = \App\Utils\Session::flash('submission_success'); ?>
                    <?php if ($submission_success): ?>
                        <div class="mt-6 bg-green-50 border border-green-200 rounded-xl p-4 shadow-sm">
                            <p class="text-sm text-green-800"><?= htmlspecialchars($submission_success) ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($submission_error): ?>
                        <div class="mt-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md">
                            <p class="text-sm"><?= htmlspecialchars($submission_error) ?></p>
                        </div>
                    <?php endif; ?>
                    <div class="mt-6 bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-800 mb-1 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Upload Karya Lomba
                        </h3>
                        <p class="text-sm text-gray-500 mb-4">Upload karya untuk divisi <?= htmlspecialchars($team['division']) ?>.</p>

                        <?php if ($submission): ?>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-sm text-blue-800">
                                Sudah diupload:
                                <?php if ($submission['type'] === 'youtube_link'): ?>
                                    <a href="<?= htmlspecialchars($submission['value']) ?>" target="_blank" class="underline ml-1"><?= htmlspecialchars($submission['value']) ?></a>
                                <?php else: ?>
                                    <?= htmlspecialchars($submission['value']) ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <form action="/dashboard/submission" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                            <?php if ($team['division'] === 'FFR'): ?>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Link YouTube <span class="text-red-500">*</span></label>
                                <input type="url" name="youtube_link" required
                                    value="<?= $submission && $submission['type'] === 'youtube_link' ? htmlspecialchars($submission['value']) : '' ?>"
                                    placeholder="https://www.youtube.com/watch?v=..."
                                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out sm:text-sm">
                                <p class="mt-1 text-xs text-gray-500">Upload video robot ke YouTube, tempelkan linknya di sini.</p>
                            <?php else: ?>
                                <label class="block text-sm font-medium text-gray-700 mb-1">File Karya <span class="text-red-500">*</span></label>
                                <input type="file" name="submission_file" required
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand file:text-white hover:file:bg-brand/90 transition duration-150">
                                <p class="mt-1 text-xs text-gray-500">Maksimal 10MB.</p>
                            <?php endif; ?>
                            <button type="submit"
                                class="mt-4 w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-brand hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150">
                                <?= $submission ? 'Update' : 'Upload' ?> Karya
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 shadow-sm text-center">
                    <h3 class="text-xl font-bold text-yellow-900 mb-2">Anda Belum Mendaftarkan Tim</h3>
                    <p class="text-yellow-800 mb-6">Segera daftarkan tim Anda untuk mengikuti perlombaan Automation Week 9.</p>
                    <a href="/dashboard/team/register" class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-brand hover:bg-brand/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 transform hover:scale-105">
                        Daftar Tim Sekarang
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>