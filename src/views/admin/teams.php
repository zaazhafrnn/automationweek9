<div class="w-full bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800">
            Semua Tim Terdaftar
        </h2>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="w-full table-fixed divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="w-16 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        No
                    </th>
                    <th scope="col" class="w-1/6 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Nama Tim
                    </th>
                    <th scope="col" class="w-1/6 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Asal Sekolah
                    </th>
                    <th scope="col" class="w-28 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Kategori
                    </th>
                    <th scope="col" class="w-1/5 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Ketua
                    </th>
                    <th scope="col" class="w-1/5 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Anggota 1
                    </th>
                    <th scope="col" class="w-1/5 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Anggota 2
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">
                <?php if (empty($teams)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            Belum ada tim yang terdaftar.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($teams as $index => $team): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4 align-top text-sm text-gray-600">
                                <?= $index + 1 ?>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <div class="font-semibold text-gray-900 break-words">
                                    <?= htmlspecialchars($team['name']) ?>
                                </div>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <div class="text-gray-600 break-words">
                                    <?= htmlspecialchars($team['teamSchool']) ?>
                                </div>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-800">
                                    <?= htmlspecialchars($team['division']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <div class="font-medium text-gray-900 break-words">
                                    <?= htmlspecialchars($team['leaderName']) ?>
                                </div>

                                <div class="mt-1 text-sm text-gray-500 break-all">
                                    <?= htmlspecialchars($team['leaderPhoneNumber']) ?>
                                </div>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <?php if (!empty($team['firstMemberName'])): ?>
                                    <div class="font-medium text-gray-900 break-words">
                                        <?= htmlspecialchars($team['firstMemberName']) ?>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500 break-all">
                                        <?= htmlspecialchars($team['firstMemberPhoneNumber']) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <?php if (!empty($team['secondMemberName'])): ?>
                                    <div class="font-medium text-gray-900 break-words">
                                        <?= htmlspecialchars($team['secondMemberName']) ?>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500 break-all">
                                        <?= htmlspecialchars($team['secondMemberPhoneNumber']) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>