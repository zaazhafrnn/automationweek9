<?php

/** @var int $round */
/** @var array $round_meta */
/** @var array $rounds */
/** @var array $sessions */
/** @var array $states */
/** @var array $teams */
/** @var array $submissions */
/** @var bool $is_open */
/** @var string $csrf_token */

use App\Components\Dialog;
use App\Components\Icon;
use App\Components\Toast;

$flashSuccess = \App\Utils\Session::flash('plc_state_success');
$flashError = \App\Utils\Session::flash('plc_state_error');
?>
<div class="mx-auto space-y-6">
    <?php if ($flashSuccess): ?>
        <?= Toast::make()->variant('success')->message($flashSuccess)->render() ?>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <?= Toast::make()->variant('error')->message($flashError)->render() ?>
    <?php endif; ?>

    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6">
            <div class="space-y-1">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">PLC <?= htmlspecialchars($round_meta['label']) ?></h3>
                <p class="text-sm text-muted-foreground"><?= count($sessions) > 1 ? 'Atur status pengumpulan tiap sesi.' : ($is_open ? 'Pengumpulan sedang dibuka.' : 'Pengumpulan sedang ditutup.') ?></p>
            </div>
            <div class="flex flex-wrap items-center gap-5">
                <a href="/admin/plc-submissions/export?round=<?= (int) $round ?>" download
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-border bg-green-700 px-3 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:bg-green-800">
                    <?= Icon::make()->name('download')->class('w-4 h-4') ?>
                    Export CSV
                </a>
                <?php foreach ($sessions as $session):
                    $stateType = $session['type'];
                    $stateOpen = !empty($states[$stateType]);
                ?>
                    <div class="flex items-center gap-3">
                        <?php if (count($sessions) > 1): ?>
                            <span class="text-xs font-semibold text-muted-foreground"><?= htmlspecialchars($session['label']) ?></span>
                        <?php endif; ?>
                        <button type="button" id="plc-round-switch-<?= $stateType ?>" onclick="togglePlcRound('<?= $stateType ?>', <?= (int) $round ?>)" role="switch" aria-checked="<?= $stateOpen ? 'true' : 'false' ?>"
                            class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors <?= $stateOpen ? 'bg-emerald-500' : 'bg-gray-300' ?> focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
                            <span id="plc-round-switch-knob-<?= $stateType ?>" class="pointer-events-none block h-5 w-5 rounded-full bg-white shadow transition-transform <?= $stateOpen ? 'translate-x-5' : 'translate-x-0.5' ?>"></span>
                        </button>
                        <span id="plc-round-state-label-<?= $stateType ?>" class="text-sm font-semibold <?= $stateOpen ? 'text-emerald-700' : 'text-gray-600' ?>"><?= $stateOpen ? 'Terbuka' : 'Ditutup' ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div id="plc-live" class="space-y-6 p-6">
            <?php $dialogs = ''; ?>
            <?php foreach ($sessions as $session):
                $sessionType = $session['type'];
                $sessionTitle = $round_meta['label'] . (count($sessions) > 1 ? ' ' . $session['label'] : '');
                $decisionLabel = count($sessions) > 1
                    ? $round_meta['label'] . ' (Sesi 1 & Sesi 2)'
                    : $sessionTitle;
                $sessionTeams = array_values(array_filter(
                    $teams,
                    fn($t) => !empty($submissions[(int) $t['id']][$sessionType]['value'])
                ));
                usort($sessionTeams, function ($a, $b) use ($submissions, $sessionType) {
                    $left = strtotime($submissions[(int) $a['id']][$sessionType]['updated_at'] ?? '') ?: 0;
                    $right = strtotime($submissions[(int) $b['id']][$sessionType]['updated_at'] ?? '') ?: 0;
                    return $left <=> $right;
                });
            ?>
                <div>
                    <?php if (count($sessions) > 1): ?>
                        <h3 class="mb-3 text-sm font-semibold text-foreground">Babak <?= (int) $round ?> &mdash; <?= htmlspecialchars($session['label']) ?></h3>
                    <?php endif; ?>
                    <div class="relative w-full overflow-x-auto border border-border rounded-xl bg-card shadow-sm">
                        <table class="w-full caption-bottom text-xs border-collapse">
                            <thead class="bg-accent text-white border-b border-accent/30">
                                <tr>
                                    <th class="h-10 px-4 text-left align-middle font-semibold whitespace-nowrap">No</th>
                                    <th class="h-10 px-4 text-left align-middle font-semibold whitespace-nowrap">Nama Tim</th>
                                    <th class="h-10 px-4 text-left align-middle font-semibold whitespace-nowrap">Asal Sekolah</th>
                                    <th class="h-10 px-4 text-left align-middle font-semibold whitespace-nowrap">Waktu di Upload</th>
                                    <th class="h-10 px-4 text-left align-middle font-semibold whitespace-nowrap">Link</th>
                                    <th class="h-10 px-4 text-left align-middle font-semibold whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$sessionTeams): ?>
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-muted-foreground">
                                            Belum ada tim yang mengumpulkan link.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php foreach ($sessionTeams as $index => $team):
                                    $teamId = (int) $team['id'];
                                    $sub = $submissions[$teamId][$sessionType] ?? null;
                                    $uid = $teamId . '-' . $sessionType;
                                    $dialogId = 'plc-dialog-' . $uid;
                                    $allLinks = '';
                                    foreach ($rounds as $number => $meta) {
                                        foreach (\App\Models\Submission::plcSessions($number) as $linkMeta) {
                                            $row = $submissions[$teamId][$linkMeta['type']] ?? null;
                                            $value = $row['value'] ?? '';
                                            $linkHtml = $value
                                                ? '<a href="' . htmlspecialchars($value, ENT_QUOTES) . '" target="_blank" rel="noopener" class="text-primary underline-offset-4 hover:underline break-all">' . htmlspecialchars($value) . '</a>'
                                                : '<span class="text-muted-foreground italic">Belum submit</span>';
                                            $linkLabel = $number === 1 ? 'Babak 1 ' . $linkMeta['label'] : $linkMeta['label'];
                                            $allLinks .= '<div class="py-2 border-b border-border last:border-0">'
                                                . '<div class="flex items-center justify-between gap-3">'
                                                . '<span class="text-sm font-semibold">' . htmlspecialchars($linkLabel) . '</span>'
                                                . '<span class="text-xs">' . $linkHtml . '</span>'
                                                . '</div>'
                                                . '</div>';
                                        }
                                    }

                                    $isPending = ($sub['status'] ?? '') === 'submitted';
                                    $approveDialog = Dialog::make()->id('plc-approve-' . $uid)->title('Loloskan Karya?')->width('max-w-md');
                                    $approveForm = '<form action="/admin/submissions/process" method="POST">'
                                        . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
                                        . '<input type="hidden" name="back" value="/admin/plc-submissions?round=' . (int) $round . '">'
                                        . '<input type="hidden" name="submission_id" value="' . (int) ($sub['id'] ?? 0) . '">'
                                        . '<input type="hidden" name="action" value="approve">'
                                        . '<p class="text-sm text-gray-600 mb-6 break-words whitespace-normal w-full">Apakah Anda yakin ingin meloloskan ' . htmlspecialchars($decisionLabel) . ' "<b>' . htmlspecialchars((string) ($sub['value'] ?? '')) . '</b>" dari tim <b>' . htmlspecialchars($team['name']) . '</b> ke tahap berikutnya?</p>'
                                        . '<div class="flex justify-end space-x-2">'
                                        . '<button type="button" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors" onclick="closeDialog(\'plc-approve-' . $uid . '\')">Batal</button>'
                                        . '<button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700">Ya, Loloskan</button>'
                                        . '</div>'
                                        . '</form>';
                                    $approveDialog->content($approveForm);

                                    $rejectDialog = Dialog::make()->id('plc-reject-' . $uid)->title('Tolak Karya?')->width('max-w-md');
                                    $rejectForm = '<form action="/admin/submissions/process" method="POST">'
                                        . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
                                        . '<input type="hidden" name="back" value="/admin/plc-submissions?round=' . (int) $round . '">'
                                        . '<input type="hidden" name="submission_id" value="' . (int) ($sub['id'] ?? 0) . '">'
                                        . '<input type="hidden" name="action" value="reject">'
                                        . '<p class="text-sm text-gray-600 mb-6 break-words whitespace-normal w-full">Apakah Anda yakin ingin menolak ' . htmlspecialchars($decisionLabel) . ' "<b>' . htmlspecialchars((string) ($sub['value'] ?? '')) . '</b>" dari tim <b>' . htmlspecialchars($team['name']) . '</b>?</p>'
                                        . '<div class="flex justify-end space-x-2">'
                                        . '<button type="button" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors" onclick="closeDialog(\'plc-reject-' . $uid . '\')">Batal</button>'
                                        . '<button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-red-700 rounded-lg hover:bg-red-800">Ya, Tolak</button>'
                                        . '</div>'
                                        . '</form>';
                                    $rejectDialog->content($rejectForm);

                                    $status = $sub['status'] ?? '';
                                    $btnBase = 'rounded p-1.5 transition-colors cursor-pointer';
                                    $dis = 'opacity-40 cursor-not-allowed pointer-events-none';
                                    $approveClass = $status === 'approved'
                                        ? 'bg-green-600 text-white'
                                        : 'text-green-600 bg-gray-100 hover:bg-gray-200';
                                    $rejectClass = $status === 'rejected'
                                        ? 'bg-red-600 text-white'
                                        : 'text-red-600 bg-gray-100 hover:bg-gray-200';
                                    $approveBtn = '<button type="button" ' . ($isPending ? $approveDialog->getOpenAttr() : '') . ' ' . ($isPending ? '' : 'disabled') . ' class="' . $approveClass . ' ' . $btnBase . ($isPending ? '' : ' ' . $dis) . '" data-tooltip="Loloskan karya">' . Icon::make()->name('check')->class('w-5 h-5') . '</button>';
                                    $rejectBtn = '<button type="button" ' . ($isPending ? $rejectDialog->getOpenAttr() : '') . ' ' . ($isPending ? '' : 'disabled') . ' class="' . $rejectClass . ' ' . $btnBase . ($isPending ? '' : ' ' . $dis) . '" data-tooltip="Tolak karya">' . Icon::make()->name('x')->class('w-5 h-5') . '</button>';

                                    $resetDialogId = 'plc-reset-' . $uid;
                                    $resetDialog = Dialog::make()->id($resetDialogId)->title('Reset Status')->width('max-w-md');
                                    $resetForm = '<form action="/admin/submissions/process" method="POST">'
                                        . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">'
                                        . '<input type="hidden" name="back" value="/admin/plc-submissions?round=' . (int) $round . '">'
                                        . '<input type="hidden" name="submission_id" value="' . (int) ($sub['id'] ?? 0) . '">'
                                        . '<input type="hidden" name="action" value="reset">'
                                        . '<p class="text-sm text-gray-600 mb-6 break-words whitespace-normal w-full">Reset status karya tim <b>' . htmlspecialchars($team['name']) . '</b> menjadi menunggu?</p>'
                                        . '<div class="flex justify-end space-x-2">'
                                        . '<button type="button" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors" onclick="closeDialog(\'plc-reset-' . $uid . '\')">Batal</button>'
                                        . '<button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-amber-600 rounded-lg hover:bg-amber-700">Ya, Reset</button>'
                                        . '</div>'
                                        . '</form>';
                                    $resetDialog->content($resetForm);

                                    $resetBtn = '<button type="button" ' . ($isPending ? '' : $resetDialog->getOpenAttr()) . ' ' . ($isPending ? 'disabled' : '') . ' class="text-amber-600 bg-gray-100 hover:bg-gray-200 ' . $btnBase . ($isPending ? ' ' . $dis : '') . '" data-tooltip="Reset Status">' . Icon::make()->name('rotate-cw')->class('w-5 h-5') . '</button>';

                                    $content = '<div class="space-y-1">'
                                        . '<p class="text-lg font-bold">' . htmlspecialchars($team['name']) . '</p>'
                                        . '<p class="text-sm text-muted-foreground">' . htmlspecialchars($team['teamSchool']) . '</p>'
                                        . '</div>'
                                        . '<div class="mt-4">' . $allLinks . '</div>';

                                    $dialogs .= Dialog::make()->id($dialogId)->title('Detail Tim')->width('max-w-lg')->content($content)->render();
                                    $dialogs .= $approveDialog->render();
                                    $dialogs .= $rejectDialog->render();
                                    $dialogs .= $resetDialog->render();
                                ?>
                                    <tr class="border-b border-border hover:bg-foreground/[0.02] last:border-0">
                                        <td class="px-4 py-3 align-middle whitespace-nowrap"><?= $index + 1 ?></td>
                                        <td class="px-4 py-3 align-middle whitespace-nowrap font-semibold"><?= htmlspecialchars($team['name']) ?></td>
                                        <td class="px-4 py-3 align-middle whitespace-nowrap"><?= htmlspecialchars($team['teamSchool']) ?></td>
                                        <td class="px-4 py-3 align-middle whitespace-nowrap text-muted-foreground"><?= $sub ? date('d M Y H:i:s', strtotime($sub['updated_at'] ?: $sub['created_at'])) : '-' ?></td>
                                        <td class="px-4 py-3 align-middle whitespace-nowrap max-w-[260px] truncate">
                                            <?php if ($sub && $sub['value']): ?>
                                                <a href="<?= htmlspecialchars($sub['value'], ENT_QUOTES) ?>" target="_blank" rel="noopener" class="text-primary underline-offset-4 hover:underline"><?= htmlspecialchars($sub['value']) ?></a>
                                            <?php else: ?>
                                                <span class="italic text-muted-foreground">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 align-middle whitespace-nowrap">
                                            <div class="flex gap-1 items-center">
                                                <?= $approveBtn ?>
                                                <?= $rejectBtn ?>
                                                <?= $resetBtn ?>
                                                <button type="button" onclick="openDialog('<?= $dialogId ?>')" class="text-gray-700 bg-gray-100 hover:bg-gray-200 rounded p-1.5 transition-colors" data-tooltip="Detail">
                                                    <?= Icon::make()->name('more-horizontal')->class('w-5 h-5 rotate-90') ?>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
            <?= $dialogs ?>
        </div>
    </div>
</div>

<?= Dialog::make()->id('plc-dialog-bootstrap')->title('')->content('')->render() ?>

<script>
    var PLC_CSRF = <?= json_encode($csrf_token) ?>;

    function setPlcRoundState(type, open) {
        var btn = document.getElementById('plc-round-switch-' + type);
        var knob = document.getElementById('plc-round-switch-knob-' + type);
        var label = document.getElementById('plc-round-state-label-' + type);
        if (!btn) return;
        btn.classList.toggle('bg-emerald-500', !!open);
        btn.classList.toggle('bg-gray-300', !open);
        knob.classList.toggle('translate-x-5', !!open);
        knob.classList.toggle('translate-x-0.5', !open);
        btn.setAttribute('aria-checked', open ? 'true' : 'false');
        if (label) label.textContent = open ? 'Terbuka' : 'Ditutup';
    }

    function togglePlcRound(type, round) {
        var btn = document.getElementById('plc-round-switch-' + type);
        if (!btn) return;
        var open = btn.getAttribute('aria-checked') !== 'true';
        var body = new URLSearchParams();
        body.set('csrf_token', PLC_CSRF);
        body.set('round', round);
        body.set('type', type);
        body.set('is_open', open ? 1 : 0);
        fetch('/admin/plc-submissions/state', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'Cache-Control': 'no-store' },
            body: body.toString()
        }).then(function (r) { return r.json(); }).then(function (d) {
            if (d && typeof d.is_open !== 'undefined') setPlcRoundState(type, !!d.is_open);
        });
    }

    function plcRefresh() {
        if (document.querySelector('[role="dialog"]:not(.hidden)')) return;
        fetch(location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Cache-Control': 'no-store' } })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var next = doc.getElementById('plc-live');
                var current = document.getElementById('plc-live');
                if (next && current) current.innerHTML = next.innerHTML;
            });
    }

    setInterval(plcRefresh, 5000);
</script>
