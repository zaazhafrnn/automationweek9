<?php

/** @var int $round */
/** @var array $round_meta */
/** @var array $rounds */
/** @var array $teams */
/** @var array $submissions */
/** @var bool $is_open */
/** @var string $csrf_token */

use App\Components\Dialog;
use App\Components\Icon;
use App\Components\Toast;

$flashSuccess = \App\Utils\Session::flash('plc_state_success');
$currentType = $round_meta['type'];
?>
<div class="mx-auto space-y-6">
    <?php if ($flashSuccess): ?>
        <?= Toast::make()->variant('success')->message($flashSuccess)->render() ?>
    <?php endif; ?>

    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6">
            <div class="space-y-1">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">PLC <?= htmlspecialchars($round_meta['label']) ?></h3>
                <p class="text-sm text-muted-foreground"><?= $is_open ? 'Pengumpulan sedang dibuka.' : 'Pengumpulan sedang ditutup.' ?></p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" id="plc-round-switch" onclick="togglePlcRound()" role="switch" aria-checked="<?= $is_open ? 'true' : 'false' ?>"
                    class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors <?= $is_open ? 'bg-emerald-500' : 'bg-gray-300' ?> focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
                    <span id="plc-round-switch-knob" class="pointer-events-none block h-5 w-5 rounded-full bg-white shadow transition-transform <?= $is_open ? 'translate-x-5' : 'translate-x-0.5' ?>"></span>
                </button>
                <span id="plc-round-state-label" class="text-sm font-semibold <?= $is_open ? 'text-emerald-700' : 'text-gray-600' ?>"><?= $is_open ? 'Terbuka' : 'Ditutup' ?></span>
            </div>
        </div>
    </div>

    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div id="plc-live" class="p-6">
            <?php if ($round === 3): ?>
                <p class="text-xs text-muted-foreground mb-4">Hanya 20 tim terbaik berdasarkan total nilai Round 1 &amp; Round 2.</p>
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
                        <?php if (!$teams): ?>
                            <tr>
                                <td colspan="6" class="p-8 text-center text-muted-foreground">
                                    <?= $round === 3 ? 'Belum ada pengumpulan dari 20 tim besar.' : 'Belum ada tim yang mengumpulkan link.' ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($teams as $index => $team):
                            $teamId = (int) $team['id'];
                            $sub = $submissions[$teamId][$currentType] ?? null;
                            $dialogId = 'plc-dialog-' . $teamId;
                            $allLinks = '';
                            foreach ($rounds as $number => $meta) {
                                $row = $submissions[$teamId][$meta['type']] ?? null;
                                $value = $row['value'] ?? '';
                                $linkHtml = $value
                                    ? '<a href="' . htmlspecialchars($value, ENT_QUOTES) . '" target="_blank" rel="noopener" class="text-primary underline-offset-4 hover:underline break-all">' . htmlspecialchars($value) . '</a>'
                                    : '<span class="text-muted-foreground italic">Belum submit</span>';
                                $scoreValue = $row['score'] ?? '';
                                $scoreHtml = '';
                                if ($number < 3) {
                                    $inputId = 'plc-score-' . $teamId . '-' . $number;
                                    $scoreHtml = '<div class="flex items-center gap-1.5 mt-2">'
                                        . '<span class="text-xs font-semibold text-muted-foreground w-14">Nilai</span>'
                                        . '<button type="button" onclick="plcScoreStep(\'' . $inputId . '\', -1)" class="h-7 w-7 rounded-md border border-border bg-secondary/40 hover:bg-secondary text-sm font-bold">−</button>'
                                        . '<input id="' . $inputId . '" data-score="' . $number . '" type="number" inputmode="numeric" value="' . htmlspecialchars((string) $scoreValue) . '" class="h-7 w-16 rounded-md border border-border bg-card px-2 text-center text-xs font-semibold [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/20 focus-visible:border-accent">'
                                        . '<button type="button" onclick="plcScoreStep(\'' . $inputId . '\', 1)" class="h-7 w-7 rounded-md border border-border bg-secondary/40 hover:bg-secondary text-sm font-bold">+</button>'
                                        . '</div>';
                                }
                                $allLinks .= '<div class="py-2 border-b border-border last:border-0">'
                                    . '<div class="flex items-center justify-between gap-3">'
                                    . '<span class="text-sm font-semibold">' . htmlspecialchars($meta['label']) . '</span>'
                                    . '<span class="text-xs">' . $linkHtml . '</span>'
                                    . '</div>' . $scoreHtml . '</div>';
                            }

                            $content = '<div class="space-y-1">'
                                . '<p class="text-lg font-bold">' . htmlspecialchars($team['name']) . '</p>'
                                . '<p class="text-sm text-muted-foreground">' . htmlspecialchars($team['teamSchool']) . '</p>'
                                . '</div>'
                                . '<div class="mt-4">' . $allLinks . '</div>'
                                . '<div class="mt-5 flex items-center justify-end gap-3">'
                                . '<span class="text-xs text-muted-foreground" data-save-status></span>'
                                . '<button type="button" onclick="savePlcScores(' . $teamId . ', \'' . $dialogId . '\')" class="px-4 py-2 text-sm font-semibold text-white bg-accent rounded-lg hover:bg-accent/90 transition-colors">Simpan Nilai</button>'
                                . '</div>';
                        ?>
                            <tr class="border-b border-border hover:bg-foreground/[0.02] last:border-0">
                                <td class="px-4 py-3 align-middle whitespace-nowrap"><?= $index + 1 ?></td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap font-semibold"><?= htmlspecialchars($team['name']) ?></td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap"><?= htmlspecialchars($team['teamSchool']) ?></td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap text-muted-foreground"><?= $sub ? date('d M Y H:i', strtotime($sub['updated_at'] ?: $sub['created_at'])) : '-' ?></td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap max-w-[260px] truncate">
                                    <?php if ($sub && $sub['value']): ?>
                                        <a href="<?= htmlspecialchars($sub['value'], ENT_QUOTES) ?>" target="_blank" rel="noopener" class="text-primary underline-offset-4 hover:underline"><?= htmlspecialchars($sub['value']) ?></a>
                                    <?php else: ?>
                                        <span class="italic text-muted-foreground">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap">
                                    <button type="button" onclick="openDialog('<?= $dialogId ?>')" class="text-gray-700 bg-gray-100 hover:bg-gray-200 rounded p-1.5 transition-colors" data-tooltip="Detail &amp; nilai">
                                        <?= Icon::make()->name('more-horizontal')->class('w-5 h-5 rotate-90') ?>
                                    </button>
                                    <?= Dialog::make()->id($dialogId)->title('Detail Tim')->width('max-w-lg')->content($content)->render() ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= Dialog::make()->id('plc-dialog-bootstrap')->title('')->content('')->render() ?>

<script>
    var PLC_CSRF = <?= json_encode($csrf_token) ?>;

    function plcScoreStep(inputId, delta) {
        var input = document.getElementById(inputId);
        if (!input) return;
        var value = parseInt(input.value, 10);
        if (isNaN(value)) value = 0;
        input.value = Math.max(0, value + delta);
    }

    function setPlcRoundState(open) {
        var btn = document.getElementById('plc-round-switch');
        var knob = document.getElementById('plc-round-switch-knob');
        var label = document.getElementById('plc-round-state-label');
        if (!btn) return;
        btn.classList.toggle('bg-emerald-500', !!open);
        btn.classList.toggle('bg-gray-300', !open);
        knob.classList.toggle('translate-x-5', !!open);
        knob.classList.toggle('translate-x-0.5', !open);
        btn.setAttribute('aria-checked', open ? 'true' : 'false');
        if (label) label.textContent = open ? 'Terbuka' : 'Ditutup';
    }

    function togglePlcRound() {
        var btn = document.getElementById('plc-round-switch');
        if (!btn) return;
        var open = btn.getAttribute('aria-checked') !== 'true';
        var body = new URLSearchParams();
        body.set('csrf_token', PLC_CSRF);
        body.set('round', <?= (int) $round ?>);
        body.set('is_open', open ? 1 : 0);
        fetch('/admin/plc-submissions/state', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'Cache-Control': 'no-store' },
            body: body.toString()
        }).then(function (r) { return r.json(); }).then(function (d) {
            if (d && typeof d.is_open !== 'undefined') setPlcRoundState(!!d.is_open);
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

    function savePlcScores(teamId, dialogId) {
        var dialog = document.getElementById(dialogId);
        if (!dialog) return;
        var body = new URLSearchParams();
        body.set('csrf_token', PLC_CSRF);
        body.set('team_id', teamId);
        dialog.querySelectorAll('[data-score]').forEach(function (input) {
            body.set('score_' + input.dataset.score, input.value);
        });

        fetch('/admin/plc-submissions/score', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: body.toString()
        }).then(function (r) { return r.json(); }).then(function () {
            var status = dialog.querySelector('[data-save-status]');
            if (status) {
                status.textContent = 'Tersimpan';
                setTimeout(function () { status.textContent = ''; }, 2000);
            }
            plcRefresh();
        }).catch(function () {
            var status = dialog.querySelector('[data-save-status]');
            if (status) status.textContent = 'Gagal menyimpan';
        });
    }

    setInterval(plcRefresh, 5000);
</script>
