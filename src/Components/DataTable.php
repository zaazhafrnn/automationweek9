<?php

namespace App\Components;

class DataTable extends Component
{
    private array $columns = [];
    private array $rows = [];
    private string $emptyText = 'No results.';
    private bool $searchable = false;
    private bool $columnSelectable = false;
    private int $perPage = 0;

    public function columns(array $columns): static
    {
        $this->columns = $columns;
        return $this;
    }

    public function rows(array $rows): static
    {
        $this->rows = $rows;
        return $this;
    }

    public function emptyText(string $text): static
    {
        $this->emptyText = $text;
        return $this;
    }

    public function searchable(): static
    {
        $this->searchable = true;
        return $this;
    }

    public function columnSelectable(): static
    {
        $this->columnSelectable = true;
        return $this;
    }

    public function pageable(int $perPage = 10): static
    {
        $this->perPage = $perPage;
        return $this;
    }

    public function render(): string
    {
        $id = 'dt-' . uniqid();
        $colspan = count($this->columns) ?: 1;
        $perPage = $this->perPage;

        $html = '<div class="flex flex-col gap-4" data-datatable="' . $id . '" data-per-page="' . $perPage . '">';

        if ($this->searchable || $this->columnSelectable) {
            $html .= '<div class="flex items-center justify-between gap-2">';
            if ($this->searchable) {
                $html .= '<div class="relative flex-1 max-w-sm">';
                $html .= Icon::make()->name('search')->class('absolute left-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground');
                $html .= '<input type="text" placeholder="Filter..." class="flex h-9 w-full rounded-md border border-input bg-transparent pl-8 pr-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" data-dt-search>';
                $html .= '</div>';
            }
            if ($this->columnSelectable) {
                $html .= '<div class="relative">';
                $html .= '<details class="group">';
                $html .= '<summary class="inline-flex items-center justify-center gap-2 rounded-md border border-input bg-background px-3 py-1 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground cursor-pointer list-none [&::-webkit-details-marker]:hidden">';
                $html .= 'Kolom';
                $html .= Icon::make()->name('chevron-down')->class('h-4 w-4 transition-transform group-open:rotate-180');
                $html .= '</summary>';
                $html .= '<div class="absolute right-0 z-50 mt-1 min-w-[150px] overflow-hidden rounded-md border bg-popover p-1 text-popover-foreground shadow-md">';
                foreach ($this->columns as $col) {
                    $key = htmlspecialchars($col['key']);
                    $html .= '<label class="flex items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground cursor-pointer">';
                    $html .= '<input type="checkbox" checked data-dt-col-toggle="' . $key . '" class="accent-primary">';
                    $html .= '<span>' . htmlspecialchars($col['label']) . '</span>';
                    $html .= '</label>';
                }
                $html .= '</div>';
                $html .= '</details>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }

        $html .= '<div class="relative w-full overflow-x-auto">';
        $html .= '<table data-slot="table" class="w-full caption-bottom text-sm">';
        $html .= '<thead data-slot="table-header" class="[&_tr]:border-b">';
        $html .= '<tr data-slot="table-row" class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">';
        foreach ($this->columns as $col) {
            $sortable = $col['sortable'] ?? true;
            $html .= '<th data-slot="table-head" data-dt-col-head="' . htmlspecialchars($col['key']) . '" class="h-10 px-2 text-left align-middle font-medium whitespace-nowrap text-muted-foreground [&:has([role=checkbox])]:pr-0 [&>[role=checkbox]]:translate-y-[2px]';
            if (isset($col['width'])) {
                $html .= ' ' . $col['width'];
            }
            $html .= $sortable ? ' cursor-pointer select-none" data-dt-sort="' . htmlspecialchars($col['key']) . '"' : '"';
            $html .= '>';
            $html .= '<div class="flex items-center">';
            $html .= htmlspecialchars($col['label']);
            if ($sortable) {
                $html .= Icon::make()->name('arrow-up-down')->class('ml-2 h-4 w-4');
            }
            $html .= '</div></th>';
        }
        $html .= '</tr></thead>';
        $html .= '<tbody data-slot="table-body" class="[&_tr:last-child]:border-0">';

        if (empty($this->rows)) {
            $html .= '<tr data-slot="table-row" class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">';
            $html .= '<td data-slot="table-cell" colspan="' . $colspan . '" class="p-2 align-middle whitespace-nowrap text-center text-muted-foreground">';
            $html .= htmlspecialchars($this->emptyText);
            $html .= '</td></tr>';
        } else {
            foreach ($this->rows as $row) {
                $html .= '<tr data-slot="table-row" class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted" data-dt-row>';
                foreach ($this->columns as $col) {
                    $render = $col['render'] ?? null;
                    $tdClass = 'p-2 align-middle whitespace-nowrap [&:has([role=checkbox])]:pr-0 [&>[role=checkbox]]:translate-y-[2px]';
                    if (isset($col['tdClass'])) {
                        $tdClass .= ' ' . $col['tdClass'];
                    }
                    $html .= '<td data-slot="table-cell" data-dt-col-body="' . htmlspecialchars($col['key']) . '" class="' . $tdClass . '">';
                    if ($render) {
                        $html .= $render($row);
                    } else {
                        $val = $row[$col['key']] ?? '-';
                        $html .= htmlspecialchars((string) $val);
                    }
                    $html .= '</td>';
                }
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table></div>';

        if ($this->perPage > 0 && count($this->rows) > $this->perPage) {
            $html .= '<div class="flex items-center justify-between text-sm text-muted-foreground">';
            $html .= '<span data-dt-info>Showing 1-' . min($this->perPage, count($this->rows)) . ' of ' . count($this->rows) . '</span>';
            $html .= '<div class="flex items-center gap-1">';
            $html .= '<button type="button" class="inline-flex items-center justify-center gap-1 rounded-md border border-input bg-background px-3 py-1 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground disabled:pointer-events-none disabled:opacity-50" data-dt-prev disabled>';
            $html .= Icon::make()->name('chevron-left')->class('h-4 w-4');
            $html .= 'Previous</button>';
            $html .= '<span class="px-2 font-medium" data-dt-page>1</span>';
            $html .= '<button type="button" class="inline-flex items-center justify-center gap-1 rounded-md border border-input bg-background px-3 py-1 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground disabled:pointer-events-none disabled:opacity-50" data-dt-next>';
            $html .= 'Next' . Icon::make()->name('chevron-right')->class('h-4 w-4');
            $html .= '</button>';
            $html .= '</div></div>';
        }

        $html .= '</div>';
        $html .= $this->initScript();
        return $html;
    }

    private function initScript(): string
    {
        return <<<JS
<script>
if (typeof window.__dtInit === 'undefined') {
  window.__dtInit = true;

  function dtApply(tbl) {
    var perPage = parseInt(tbl.dataset.perPage) || 0;
    var rows = Array.from(tbl.querySelectorAll('[data-dt-row]'));
    var query = (tbl.querySelector('[data-dt-search]') || {}).value || '';
    var page = parseInt((tbl.querySelector('[data-dt-page]') || {}).textContent) || 1;
    var matched = rows;

    if (query) {
      var lq = query.toLowerCase();
      matched = rows.filter(function (r) {
        return Array.from(r.querySelectorAll('td')).some(function (c) {
          return c.textContent.toLowerCase().includes(lq);
        });
      });
    }

    rows.forEach(function (r) { r.style.display = 'none'; });
    matched.forEach(function (r) { r.style.display = ''; });

    if (perPage > 0 && matched.length > perPage) {
      var totalPages = Math.ceil(matched.length / perPage);
      if (page > totalPages) page = totalPages;
      var start = (page - 1) * perPage;
      var end = Math.min(start + perPage, matched.length);

      matched.forEach(function (r, i) {
        r.style.display = i >= start && i < end ? '' : 'none';
      });

      var info = tbl.querySelector('[data-dt-info]');
      if (info) info.textContent = 'Showing ' + (start + 1) + '-' + end + ' of ' + matched.length;

      var prev = tbl.querySelector('[data-dt-prev]');
      var next = tbl.querySelector('[data-dt-next]');
      if (prev) prev.disabled = page <= 1;
      if (next) next.disabled = page >= totalPages;

      var pg = tbl.querySelector('[data-dt-page]');
      if (pg) pg.textContent = page;
    }
  }

  function dtToggleCol(tbl, key, show) {
    var th = tbl.querySelector('[data-dt-col-head="' + key + '"]');
    var tds = tbl.querySelectorAll('[data-dt-col-body="' + key + '"]');
    var disp = show ? '' : 'none';
    if (th) th.style.display = disp;
    tds.forEach(function (td) { td.style.display = disp; });
  }

  document.addEventListener('input', function (e) {
    var inp = e.target.closest('[data-dt-search]');
    if (!inp) return;
    var tbl = inp.closest('[data-datatable]');
    if (!tbl) return;
    var p = tbl.querySelector('[data-dt-page]');
    if (p) p.textContent = 1;
    dtApply(tbl);
  });

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-dt-prev],[data-dt-next]');
    if (!btn) return;
    var tbl = btn.closest('[data-datatable]');
    if (!tbl) return;
    var p = tbl.querySelector('[data-dt-page]');
    if (!p) return;
    var pg = parseInt(p.textContent);
    p.textContent = btn.hasAttribute('data-dt-next') ? pg + 1 : pg - 1;
    dtApply(tbl);
  });

  document.addEventListener('click', function (e) {
    var th = e.target.closest('[data-dt-sort]');
    if (!th) return;
    var tbl = th.closest('[data-datatable]');
    if (!tbl) return;
    var tbody = tbl.querySelector('tbody');
    var rows = Array.from(tbody.querySelectorAll('[data-dt-row]'));
    var dir = th.dataset.dtDir || '';
    dir = dir === 'asc' ? 'desc' : 'asc';

    tbl.querySelectorAll('[data-dt-sort]').forEach(function (h) {
      delete h.dataset.dtDir;
    });
    th.dataset.dtDir = dir;

    var idx = Array.from(th.parentElement.children).indexOf(th);
    var sorted = rows.slice().sort(function (a, b) {
      var av = a.querySelectorAll('td')[idx].textContent.trim();
      var bv = b.querySelectorAll('td')[idx].textContent.trim();
      var an = parseFloat(av);
      var bn = parseFloat(bv);
      if (!isNaN(an) && !isNaN(bn)) { av = an; bv = bn; }
      return av < bv ? (dir === 'asc' ? -1 : 1) : av > bv ? (dir === 'asc' ? 1 : -1) : 0;
    });

    sorted.forEach(function (r) { tbody.appendChild(r); });
    var p = tbl.querySelector('[data-dt-page]');
    if (p) p.textContent = 1;
    dtApply(tbl);
  });

  document.addEventListener('change', function (e) {
    var cb = e.target.closest('[data-dt-col-toggle]');
    if (!cb) return;
    var tbl = cb.closest('[data-datatable]');
    if (!tbl) return;
    dtToggleCol(tbl, cb.dataset.dtColToggle, cb.checked);
  });
}
</script>
JS;
    }
}
