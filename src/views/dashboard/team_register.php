<?php /** @var string $csrf_token */ /** @var string|null $error */ ?>
<div class="bg-white shadow-xl rounded-2xl overflow-hidden max-w-4xl mx-auto mt-8">
    <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Registrasi Tim Lomba</h1>
        <a href="/dashboard" class="text-sm font-medium text-blue-600 hover:text-blue-500">Kembali ke Dashboard</a>
    </div>

    <div class="px-6 py-8">
        <?php if (isset($error)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
                <p class="text-sm"><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <form action="/dashboard/team/register" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-6">
                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Informasi Tim</h3>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Tim <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out sm:text-sm">
                    </div>

                    <div>
                        <label for="teamSchool" class="block text-sm font-medium text-gray-700 mb-1">Asal Sekolah</label>
                        <input type="text" id="teamSchool" name="teamSchool"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out sm:text-sm">
                    </div>

                    <div>
                        <label for="division" class="block text-sm font-medium text-gray-700 mb-1">Divisi Lomba <span class="text-red-500">*</span></label>
                        <select id="division" name="division" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out sm:text-sm">
                            <option value="">-- Pilih Divisi --</option>
                            <option value="LF">Line Follower (LF)</option>
                            <option value="PLC">Programmable Logic Controller (PLC)</option>
                            <option value="FFR">Fire Fighting Robot (FFR)</option>
                            <option value="LKTI">Lomba Karya Tulis Ilmiah (LKTI)</option>
                        </select>
                        <p id="division_hint" class="mt-1 text-xs text-gray-500 hidden"></p>
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Anggota Tim</h3>

                    <div>
                        <label for="leaderName" class="block text-sm font-medium text-gray-700 mb-1">Nama Ketua <span class="text-red-500">*</span></label>
                        <input type="text" id="leaderName" name="leaderName" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out sm:text-sm">
                    </div>

                    <div>
                        <label for="leaderPhoneNumber" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon/WA Ketua <span class="text-red-500">*</span></label>
                        <input type="text" id="leaderPhoneNumber" name="leaderPhoneNumber" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out sm:text-sm">
                    </div>

                    <div id="member_1_container">
                        <label for="firstMemberName" class="block text-sm font-medium text-gray-700 mb-1">Nama Anggota 1</label>
                        <input type="text" id="firstMemberName" name="firstMemberName"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out sm:text-sm">
                        
                        <label for="firstMemberPhoneNumber" class="block text-sm font-medium text-gray-700 mt-4 mb-1">Nomor Telepon/WA Anggota 1</label>
                        <input type="text" id="firstMemberPhoneNumber" name="firstMemberPhoneNumber"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out sm:text-sm">
                    </div>

                    <div id="member_2_container" class="hidden">
                        <label for="secondMemberName" class="block text-sm font-medium text-gray-700 mt-6 mb-1">Nama Anggota 2</label>
                        <input type="text" id="secondMemberName" name="secondMemberName"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out sm:text-sm">
                        
                        <label for="secondMemberPhoneNumber" class="block text-sm font-medium text-gray-700 mt-4 mb-1">Nomor Telepon/WA Anggota 2</label>
                        <input type="text" id="secondMemberPhoneNumber" name="secondMemberPhoneNumber"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200">
                <button type="submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#bc0301] hover:bg-[#bc0301]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out transform scale-95 hover:scale-100">
                    Daftar Tim
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const divisionSelect = document.getElementById('division');
        const member2Container = document.getElementById('member_2_container');
        const member2Input = document.getElementById('secondMemberName');
        const hintText = document.getElementById('division_hint');

        function updateFormFields() {
            const selected = divisionSelect.value;

            if (!selected) {
                hintText.classList.add('hidden');
                member2Container.classList.add('hidden');
                return;
            }

            hintText.classList.remove('hidden');

            if (selected === 'LF' || selected === 'PLC') {
                hintText.textContent = '* Maksimal 2 anggota (1 Ketua + 1 Anggota)';
                member2Container.classList.add('hidden');
                member2Input.value = '';
            } else if (selected === 'FFR' || selected === 'LKTI') {
                hintText.textContent = '* Maksimal 3 anggota (1 Ketua + 2 Anggota)';
                member2Container.classList.remove('hidden');
            }
        }

        divisionSelect.addEventListener('change', updateFormFields);
        updateFormFields();
    });
</script>