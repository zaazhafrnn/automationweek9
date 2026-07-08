<?php

/** @var array $submissions */
/** @var string $division */
/** @var string $page_title */ ?>
<div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center">
        <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-800 mr-3"><?= $division ?></span>
        <h2 class="text-lg font-bold text-gray-800">Karya Divisi <?= $division ?></h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full table-fixed divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-16 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">No</th>
                    <th class="w-1/5 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tim</th>
                    <th class="w-1/5 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Email</th>
                    <th class="w-1/4 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Karya</th>
                    <th class="w-24 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Diupload</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                <?php if (empty($submissions)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada karya untuk divisi <?= $division ?>.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($submissions as $i => $s): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4 align-top text-sm text-gray-600"><?= $i + 1 ?></td>
                            <td class="px-4 py-4 align-top">
                                <div class="font-semibold text-gray-900 break-words"><?= htmlspecialchars($s['team_name']) ?></div>
                                <div class="text-sm text-gray-500">Ketua: <?= htmlspecialchars($s['leaderName']) ?></div>
                            </td>
                            <td class="px-4 py-4 align-top text-gray-600 break-all"><?= htmlspecialchars($s['email']) ?></td>
                            <td class="px-4 py-4 align-top">
                                <?php if ($s['type'] === 'youtube_link'): ?>
                                    <a href="<?= htmlspecialchars($s['value']) ?>" target="_blank" class="text-blue-600 underline hover:text-blue-800 break-all"><?= htmlspecialchars($s['value']) ?></a>
                                <?php else: ?>
                                    <a href="/uploads/submissions/<?= htmlspecialchars($s['value']) ?>" target="_blank" class="text-blue-600 underline hover:text-blue-800"><?= htmlspecialchars($s['value']) ?></a>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 align-top text-sm text-gray-500"><?= htmlspecialchars($s['updated_at'] ?: $s['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>