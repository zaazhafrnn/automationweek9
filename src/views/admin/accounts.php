<?php

/** @var array $members */
/** @var array $progress */
/** @var string $page_title */

use App\Components\DataTable;
use App\Components\Dialog;
use App\Components\Icon;

$badge = function (string $status): string {
    $map = [
        'submitted' => ['Menunggu', 'bg-yellow-100 text-yellow-800'],
        'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
        'approved' => ['Disetujui', 'bg-green-100 text-green-800'],
        'verified' => ['Terverifikasi', 'bg-green-100 text-green-800'],
        'qualified' => ['Lolos', 'bg-green-100 text-green-800'],
        'rejected' => ['Ditolak', 'bg-red-100 text-red-800'],
        'not_qualified' => ['Tidak Lolos', 'bg-red-100 text-red-800'],
    ];
    [$label, $class] = $map[$status] ?? [$status, 'bg-secondary text-secondary-foreground'];
    return '<span class="inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-semibold whitespace-nowrap ' . $class . '">' . $label . '</span>';
};

$done = fn(bool $ok): string => $ok
    ? '<span class="text-green-600 font-bold shrink-0">✓</span>'
    : '<span class="text-red-400 font-bold shrink-0">✗</span>';

$typeLabels = [
    'abstract' => 'Abstrak (LKTI)',
    'full_paper' => 'Full Paper (LKTI)',
    'youtube_link' => 'Video YouTube',
];

$divisionMeta = [
    'LF'   => ['Line Follower', '/image/lf_icon.png'],
    'PLC'  => ['Programmable Logic Controller', '/image/plc_icon.png'],
    'FFR'  => ['Fire Fighting Robot', '/image/ffr_icon.png'],
    'LKTI' => ['Lomba Karya Tulis Ilmiah', '/image/lkti_icon.png'],
    'PROG' => ['Algoritma Program', '/image/program_icon.png'],
];

$sectionTitle = fn(string $t): string => '<h4 class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-2">' . $t . '</h4>';
$sectionWrap = fn(string $inner): string => '<div class="mb-5">' . $inner . '</div>';
$cardOpen = '<div class="rounded-lg border bg-secondary/20 divide-y divide-border">';
$cardClose = '</div>';

$field = function (string $label, string $valueHtml, bool $ok = false, bool $showCheck = true, ?string $badgeHtml = null) use ($done): string {
    return '<div class="flex items-start justify-between gap-3 px-3 py-2.5">'
        . '<div class="min-w-0">'
        . '<div class="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">' . htmlspecialchars($label) . '</div>'
        . '<div class="text-sm font-medium mt-0.5">' . ($valueHtml !== '' ? $valueHtml : '<span class="text-muted-foreground italic font-normal">Belum diisi</span>') . '</div>'
        . '</div>'
        . ($showCheck || $badgeHtml ? '<div class="flex items-center gap-2 shrink-0 pt-2">' . $badgeHtml . ($showCheck ? $done($ok) : '') . '</div>' : '')
        . '</div>';
};

$renderProgress = function (array $m, array $p) use ($badge, $field, $sectionTitle, $sectionWrap, $cardOpen, $cardClose, $divisionMeta, $typeLabels): string {
    $html = '<div class="flex items-center gap-3 pb-4 mb-1 border-b border-border">'
        . '<div class="w-10 h-10 rounded-full bg-accent/10 text-accent flex items-center justify-center font-bold text-sm shrink-0">'
        . strtoupper(mb_substr($m['name'], 0, 1))
        . '</div>'
        . '<div class="min-w-0">'
        . '<div class="text-sm font-semibold truncate">' . htmlspecialchars($m['name']) . '</div>'
        . '<div class="text-xs text-gray-600 truncate">' . htmlspecialchars($m['email']) . '</div>'
        . '</div></div>';

    if (!$p['team']) {
        return $html . $sectionWrap('<div class="rounded-lg border bg-secondary/20 px-3 py-2.5 text-sm text-muted-foreground italic">Akun ini belum mendaftarkan tim.</div>');
    }
    $t = $p['team'];
    $metaKey = array_key_exists($t['division'], $divisionMeta) ? $t['division'] : strtoupper(trim($t['division']));
    [$divName, $divImg] = $divisionMeta[$metaKey] ?? [$t['division'], null];

    $divHtml = '<span class="inline-flex items-center gap-2">'
        . ($divImg ? '<img src="' . $divImg . '" alt="' . htmlspecialchars($t['division']) . '" style="width:1.5rem;height:1.5rem;object-fit:contain" class="shrink-0">' : '')
        . '<span>' . htmlspecialchars($divName) . '</span></span>';
    $html .= $sectionWrap($sectionTitle('Tim & Divisi')
        . $cardOpen
        . $field('Nama Tim', htmlspecialchars($t['name']), true)
        . $field('Asal Sekolah', htmlspecialchars($t['teamSchool'] ?? ''), !empty($t['teamSchool']))
        . $field('Divisi', $divHtml, true)
        . $cardClose);

    $membersList = [
        'Ketua' => [$t['leaderName'], $t['leaderPhoneNumber']],
        'Anggota 1' => [$t['firstMemberName'] ?? null, $t['firstMemberPhoneNumber'] ?? null],
        'Anggota 2' => [$t['secondMemberName'] ?? null, $t['secondMemberPhoneNumber'] ?? null],
    ];
    $rows = '';
    foreach ($membersList as $role => [$name, $phone]) {
        $valueHtml = ($name ? htmlspecialchars($name) : '')
            . ($phone ? '<div class="text-xs text-gray-700 font-normal mt-0.5">No. HP: ' . htmlspecialchars($phone) . '</div>' : '');
        $rows .= $field($role, $valueHtml, !empty($name));
    }
    $html .= $sectionWrap($sectionTitle('Anggota') . $cardOpen . $rows . $cardClose);

    $pay = $p['payment'];
    if ($pay) {
        $proof = !empty($pay['proofImage'])
            ? '<a href="/uploads/payments/' . htmlspecialchars($pay['proofImage']) . '" target="_blank" class="text-primary underline-offset-4 hover:underline break-all">' . htmlspecialchars($pay['proofImage']) . '</a>'
            : '<span class="text-gray-700">-</span>';
        $meta = 'Dikirim: ' . date('d M Y H:i', strtotime($pay['submittedAt']))
            . (!empty($pay['note']) ? '<br>Catatan: ' . htmlspecialchars($pay['note']) : '');
        $rows = $field('Status Pembayaran', $proof
            . '<div class="text-xs text-gray-700 font-normal mt-0.5">' . $meta . '</div>', $pay['status'] === 'verified', true, $badge($pay['status']));
    } else {
        $rows = $field('Status Pembayaran', '', false, true, null);
    }
    $html .= $sectionWrap($sectionTitle('Pembayaran') . $cardOpen . $rows . $cardClose);

    $subs = $p['submissions'];
    unset($subs['registration']);
    uksort($subs, fn($a, $b) => array_key_exists($a, $typeLabels) ? -1 : (array_key_exists($b, $typeLabels) ? 1 : strcasecmp($a, $b)));
    $rows = '';
    if (!$subs) {
        $rows .= $field('Pengumpulan', 'Belum ada berkas yang dikumpulkan', false);
    } else {
        foreach ($subs as $type => $s) {
            if (str_starts_with($type, 'plc_')) {
                $label = 'PLC ' . ucfirst(trim(str_replace(['plc_', '_'], ['', ' '], $type)));
            } else {
                $label = $typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type));
            }
            $link = $type === 'youtube_link'
                ? htmlspecialchars((string) $s['value'])
                : '/uploads/submissions/' . htmlspecialchars((string) $s['value']);
            $valueHtml = '<a href="' . $link . '" target="_blank" class="text-primary underline-offset-4 hover:underline break-all">' . htmlspecialchars((string) $s['value']) . '</a>'
                . '<div class="text-xs text-gray-700 font-normal mt-0.5">'
                . (!empty($s['category']) ? 'Kategori: ' . htmlspecialchars($s['category']) . ' · ' : '')
                . 'Diupload: ' . date('d M Y H:i', strtotime($s['updated_at'] ?: $s['created_at']))
                . '</div>';
            $rows .= $field($label, $valueHtml, in_array($s['status'], ['approved', 'qualified']), true, $badge($s['status']));
        }
    }
    $html .= $sectionWrap($sectionTitle('Berkas & Karya') . $cardOpen . $rows . $cardClose);

    return $html;
};
?>

<div class="rounded-xl border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="text-2xl font-semibold leading-none tracking-tight">Semua Akun</h3>
        <p class="text-sm text-muted-foreground">Daftar semua akun yang terdaftar. Klik Progres untuk melihat kelengkapan tiap tahap.</p>
    </div>
    <div class="p-6 pt-0">
        <?= DataTable::make()
            ->columns([
                ['key' => 'name', 'label' => 'Nama', 'render' => function ($m) {
                    return htmlspecialchars($m['name'])
                        . ($m['role'] === 'dummy'
                            ? ' <span class="ml-1 inline-block rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700 align-middle">Dummy</span>'
                            : '');
                }],
                ['key' => 'email', 'label' => 'Email'],
                [
                    'key' => 'aksi',
                    'label' => 'Aksi',
                    'sortable' => false,
                    'render' => function ($m) use ($progress, $renderProgress) {
                        $p = $progress[$m['id']] ?? null;
                        if (!$p || !$p['team']) {
                            return '<span class="text-muted-foreground">-</span>';
                        }
                        return '<div id="account-progress-' . $m['id'] . '" class="hidden">' . $renderProgress($m, $p) . '</div>'
                            . '<details class="relative inline-block" data-kebab-menu>'
                            . '<summary class="list-none cursor-pointer inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-400 bg-card hover:bg-secondary/40 transition-colors [&::-webkit-details-marker]:hidden">'
                            . Icon::make()->name('more-horizontal')->class('w-4 h-4')
                            . '</summary>'
                            . '<div class="absolute right-0 z-50 mt-1 min-w-[170px] overflow-hidden rounded-xl border border-border bg-card p-1.5 shadow-lg text-sm">'
                            . '<button type="button" onclick="this.closest(\'details\').removeAttribute(\'open\'); showAccountProgress(' . $m['id'] . ')"'
                            . ' class="w-full flex items-center gap-2 px-2.5 py-2 rounded-lg hover:bg-secondary/50 text-left font-medium cursor-pointer whitespace-nowrap">'
                            . Icon::make()->name('eye')->class('w-4 h-4')
                            . 'Lihat Progres</button>'
                            . '</div></details>';
                    },
                ],
            ])
            ->rows(array_map(fn($m) => ['name' => $m['name'], 'email' => $m['email'], 'role' => $m['role'], 'id' => $m['id']], $members))
            ->searchable()
            ->columnSelectable()
            ->pageable()
            ->emptyText('Belum ada akun yang terdaftar.')
            ->render() ?>
    </div>
</div>

<?= Dialog::make()->id('accountProgressDialog')->title('Progres Akun')
    ->content('<div id="account-progress-body" class="max-h-[65vh] overflow-y-auto"></div>')
    ->render() ?>

<script>
    document.addEventListener('click', function(e) {
        document.querySelectorAll('details[data-kebab-menu][open]').forEach(function(d) {
            if (!d.contains(e.target)) d.removeAttribute('open');
        });
    });

    function showAccountProgress(id) {
        var src = document.getElementById('account-progress-' + id);
        document.getElementById('account-progress-body').innerHTML = src.innerHTML;
        openDialog('accountProgressDialog');
    }
</script>