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

        $html = '<div class="flex flex-col gap-4 w-full" data-datatable="' . $id . '" data-per-page="' . $perPage . '">';

        // Top Toolbar
        if ($this->searchable || $this->columnSelectable) {
            $html .= '<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 p-1">';
            
            // Search Input
            if ($this->searchable) {
                $html .= '<div class="relative flex-1 max-w-sm">';
                $html .= Icon::make()->name('search')->class('absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground');
                $html .= '<input type="text" placeholder="Cari data..." class="flex h-9 w-full rounded-lg border border-border bg-card pl-9 pr-8 py-1.5 text-xs shadow-sm transition-all placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/20 focus-visible:border-accent" data-dt-search>';
                $html .= '<button type="button" class="absolute right-2.5 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground hover:text-foreground hidden flex items-center justify-center rounded transition-colors" data-dt-search-clear>';
                $html .= Icon::make()->name('x')->class('h-3.5 w-3.5');
                $html .= '</button>';
                $html .= '</div>';
            }
            
            // Columns selector
            if ($this->columnSelectable) {
                $html .= '<div class="relative self-end sm:self-auto">';
                $html .= '<details class="group">';
                $html .= '<summary class="inline-flex items-center justify-center gap-2 rounded-lg border border-border bg-card px-3 py-2 text-xs font-semibold shadow-sm transition-all hover:bg-secondary/40 hover:text-foreground cursor-pointer list-none [&::-webkit-details-marker]:hidden">';
                $html .= 'Tampilkan Kolom';
                $html .= Icon::make()->name('chevron-down')->class('h-3.5 w-3.5 transition-transform group-open:rotate-180 text-muted-foreground');
                $html .= '</summary>';
                $html .= '<div class="absolute right-0 z-50 mt-1.5 min-w-[160px] overflow-hidden rounded-xl border border-border bg-card p-1.5 text-foreground shadow-lg animate-in fade-in slide-in-from-top-1 duration-150">';
                foreach ($this->columns as $col) {
                    $key = htmlspecialchars($col['key']);
                    $html .= '<label class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-xs outline-none hover:bg-secondary/50 hover:text-foreground cursor-pointer select-none transition-colors">';
                    $html .= '<input type="checkbox" checked data-dt-col-toggle="' . $key . '" class="w-3.5 h-3.5 accent-accent rounded border-border text-accent focus:ring-accent/20">';
                    $html .= '<span class="font-medium">' . htmlspecialchars($col['label']) . '</span>';
                    $html .= '</label>';
                }
                $html .= '</div>';
                $html .= '</details>';
                $html .= '</div>';
            }
            
            $html .= '</div>';
        }

        // Table container
        $html .= '<div class="relative w-full overflow-x-auto border border-border rounded-xl bg-card shadow-sm">';
        $html .= '<table data-slot="table" class="w-full caption-bottom text-xs border-collapse">';
        $html .= '<thead data-slot="table-header" class="bg-accent text-white border-b border-accent/30">';
        $html .= '<tr data-slot="table-row" class="transition-colors">';
        
        foreach ($this->columns as $col) {
            $sortable = $col['sortable'] ?? true;
            $html .= '<th data-slot="table-head" data-dt-col-head="' . htmlspecialchars($col['key']) . '" class="h-10 px-4 text-left align-middle font-semibold whitespace-nowrap text-white/90 [&:has([role=checkbox])]:pr-0 [&>[role=checkbox]]:translate-y-[2px]';
            if (isset($col['width'])) {
                $html .= ' ' . $col['width'];
            }
            $html .= $sortable ? ' cursor-pointer select-none hover:bg-white/10 hover:text-white transition-colors" data-dt-sort="' . htmlspecialchars($col['key']) . '"' : '"';
            $html .= '>';
            $html .= '<div class="flex items-center gap-1.5">';
            $html .= '<span>' . htmlspecialchars($col['label']) . '</span>';
            if ($sortable) {
                $html .= '<span class="text-white/60 transition-colors flex items-center shrink-0" data-dt-sort-icon>';
                $html .= Icon::make()->name('arrow-up-down')->class('h-3.5 w-3.5');
                $html .= '</span>';
            }
            $html .= '</div></th>';
        }
        $html .= '</tr></thead>';
        
        $html .= '<tbody data-slot="table-body" class="[&_tr:last-child]:border-0">';

        // Client-side empty results placeholder
        $html .= '<tr data-dt-no-results class="hidden border-b border-border last:border-0">';
        $html .= '<td data-slot="table-cell" colspan="' . $colspan . '" class="p-8 align-middle text-center text-muted">';
        $html .= '<div class="flex flex-col items-center justify-center gap-2 py-4">';
        $html .= Icon::make()->name('alert-circle')->class('h-8 w-8 text-muted/60');
        $html .= '<span class="font-medium text-sm text-muted-foreground">Tidak ada data yang cocok dengan pencarian Anda.</span>';
        $html .= '</div>';
        $html .= '</td></tr>';

        if (empty($this->rows)) {
            $html .= '<tr data-slot="table-row" class="border-b border-border last:border-0">';
            $html .= '<td data-slot="table-cell" colspan="' . $colspan . '" class="p-8 align-middle text-center text-muted">';
            $html .= '<div class="flex flex-col items-center justify-center gap-2 py-4">';
            $html .= Icon::make()->name('alert-circle')->class('h-8 w-8 text-muted/60');
            $html .= '<span class="font-medium text-sm text-muted-foreground">' . htmlspecialchars($this->emptyText) . '</span>';
            $html .= '</div>';
            $html .= '</td></tr>';
        } else {
            foreach ($this->rows as $row) {
                $html .= '<tr data-slot="table-row" class="border-b border-border transition-colors hover:bg-foreground/[0.02] hover:text-foreground last:border-0" data-dt-row>';
                foreach ($this->columns as $col) {
                    $render = $col['render'] ?? null;
                    $tdClass = 'px-4 py-3 align-middle whitespace-nowrap [&:has([role=checkbox])]:pr-0 [&>[role=checkbox]]:translate-y-[2px]';
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

        // Footer Pagination and Counters
        $html .= '<div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-muted-foreground p-1 mt-1">';
        
        $totalCount = count($this->rows);
        $html .= '<div class="flex items-center gap-2 font-medium">';
        $html .= '<span data-dt-info>Menampilkan 1-' . min($perPage > 0 ? $perPage : $totalCount, $totalCount) . ' dari ' . $totalCount . ' data</span>';
        $html .= '</div>';

        // Dynamic page limit selector
        if ($this->perPage > 0) {
            $html .= '<div class="flex items-center gap-2 text-xs">';
            $html .= '<span>Baris per halaman:</span>';
            $html .= '<select class="h-8 w-[72px] rounded-lg border border-border bg-card px-2 py-1 text-xs font-semibold shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/20 focus-visible:border-accent cursor-pointer" data-dt-limit>';
            foreach ([10, 25, 50, 100] as $opt) {
                $selected = $opt === $this->perPage ? 'selected' : '';
                $html .= '<option value="' . $opt . '" ' . $selected . '>' . $opt . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';
        }

        // Prev, Index, Next controls
        if ($this->perPage > 0) {
            $html .= '<div class="flex items-center gap-1.5">';
            
            // First page button
            $html .= '<button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-border bg-card text-foreground hover:bg-secondary/40 disabled:pointer-events-none disabled:opacity-40 transition-all cursor-pointer shadow-sm" data-dt-first disabled>';
            $html .= Icon::make()->name('chevrons-left')->class('h-3.5 w-3.5');
            $html .= '</button>';
            
            // Previous page button
            $html .= '<button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-border bg-card text-foreground hover:bg-secondary/40 disabled:pointer-events-none disabled:opacity-40 transition-all cursor-pointer shadow-sm" data-dt-prev disabled>';
            $html .= Icon::make()->name('chevron-left')->class('h-3.5 w-3.5');
            $html .= '</button>';
            
            // Page state details
            $html .= '<div class="px-3 py-1 bg-secondary/20 border border-border rounded-lg font-semibold text-[10px] text-foreground min-w-[80px] text-center select-none shadow-sm flex items-center justify-center gap-1">';
            $html .= '<span>Hal</span>';
            $html .= '<span data-dt-page>1</span>';
            $html .= '<span>dari</span>';
            $html .= '<span data-dt-total-pages>1</span>';
            $html .= '</div>';
            
            // Next page button
            $html .= '<button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-border bg-card text-foreground hover:bg-secondary/40 disabled:pointer-events-none disabled:opacity-40 transition-all cursor-pointer shadow-sm" data-dt-next>';
            $html .= Icon::make()->name('chevron-right')->class('h-3.5 w-3.5');
            $html .= '</button>';

            // Last page button
            $html .= '<button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-border bg-card text-foreground hover:bg-secondary/40 disabled:pointer-events-none disabled:opacity-40 transition-all cursor-pointer shadow-sm" data-dt-last>';
            $html .= Icon::make()->name('chevrons-right')->class('h-3.5 w-3.5');
            $html .= '</button>';
            
            $html .= '</div>';
        }

        $html .= '</div>';
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
    var limitEl = tbl.querySelector('[data-dt-limit]');
    var perPage = limitEl ? parseInt(limitEl.value) : (parseInt(tbl.dataset.perPage) || 0);
    var rows = Array.from(tbl.querySelectorAll('[data-dt-row]'));
    var query = (tbl.querySelector('[data-dt-search]') || {}).value || '';
    var pageSpan = tbl.querySelector('[data-dt-page]');
    var page = pageSpan ? parseInt(pageSpan.textContent) || 1 : 1;
    var matched = rows;

    // Filter by query
    if (query) {
      var lq = query.toLowerCase();
      matched = rows.filter(function (r) {
        return Array.from(r.querySelectorAll('td')).some(function (c) {
          if (c.style.display === 'none') return false;
          return c.textContent.toLowerCase().includes(lq);
        });
      });
    }

    // Toggle search clear button
    var clearBtn = tbl.querySelector('[data-dt-search-clear]');
    if (clearBtn) {
      if (query) {
        clearBtn.classList.remove('hidden');
      } else {
        clearBtn.classList.add('hidden');
      }
    }

    // Default display settings
    rows.forEach(function (r) { r.style.display = 'none'; });
    matched.forEach(function (r) { r.style.display = ''; });

    // Client-side empty results view
    var noResultsRow = tbl.querySelector('[data-dt-no-results]');
    if (noResultsRow) {
      noResultsRow.style.display = (matched.length === 0 && rows.length > 0) ? '' : 'none';
    }

    // Calculate pagination totals
    var totalPages = perPage > 0 ? Math.ceil(matched.length / perPage) : 1;
    if (totalPages < 1) totalPages = 1;
    if (page > totalPages) page = totalPages;
    if (page < 1) page = 1;

    var start = 0;
    var end = matched.length;

    if (perPage > 0) {
      start = (page - 1) * perPage;
      end = Math.min(start + perPage, matched.length);

      matched.forEach(function (r, i) {
        r.style.display = i >= start && i < end ? '' : 'none';
      });
    }

    // Update pagination labels
    var info = tbl.querySelector('[data-dt-info]');
    if (info) {
      if (matched.length === 0) {
        info.textContent = 'Menampilkan 0 dari 0 data';
      } else {
        info.textContent = 'Menampilkan ' + (start + 1) + '-' + end + ' dari ' + matched.length + ' data';
      }
    }

    // Update action button states
    var first = tbl.querySelector('[data-dt-first]');
    var prev = tbl.querySelector('[data-dt-prev]');
    var next = tbl.querySelector('[data-dt-next]');
    var last = tbl.querySelector('[data-dt-last]');
    var tot = tbl.querySelector('[data-dt-total-pages]');

    if (first) first.disabled = page <= 1;
    if (prev) prev.disabled = page <= 1;
    if (next) next.disabled = page >= totalPages;
    if (last) last.disabled = page >= totalPages;
    if (pageSpan) pageSpan.textContent = page;
    if (tot) tot.textContent = totalPages;
  }

  function dtInitAll() {
    document.querySelectorAll('[data-datatable]').forEach(function (tbl) {
      if (!tbl.dataset.dtApplied) {
        tbl.dataset.dtApplied = 'true';
        dtApply(tbl);
      }
    });
  }

  // Hook elements
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
    var btn = e.target.closest('[data-dt-search-clear]');
    if (!btn) return;
    var tbl = btn.closest('[data-datatable]');
    if (!tbl) return;
    var inp = tbl.querySelector('[data-dt-search]');
    if (inp) {
      inp.value = '';
      var p = tbl.querySelector('[data-dt-page]');
      if (p) p.textContent = 1;
      dtApply(tbl);
    }
  });

  document.addEventListener('change', function (e) {
    var sel = e.target.closest('[data-dt-limit]');
    if (!sel) return;
    var tbl = sel.closest('[data-datatable]');
    if (!tbl) return;
    var p = tbl.querySelector('[data-dt-page]');
    if (p) p.textContent = 1;
    dtApply(tbl);
  });

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-dt-first],[data-dt-prev],[data-dt-next],[data-dt-last]');
    if (!btn) return;
    var tbl = btn.closest('[data-datatable]');
    if (!tbl) return;
    var p = tbl.querySelector('[data-dt-page]');
    if (!p) return;
    var pg = parseInt(p.textContent) || 1;

    var limitEl = tbl.querySelector('[data-dt-limit]');
    var perPage = limitEl ? parseInt(limitEl.value) : (parseInt(tbl.dataset.perPage) || 0);
    var rows = Array.from(tbl.querySelectorAll('[data-dt-row]'));
    var query = (tbl.querySelector('[data-dt-search]') || {}).value || '';
    var matched = rows;
    if (query) {
      var lq = query.toLowerCase();
      matched = rows.filter(function (r) {
        return Array.from(r.querySelectorAll('td')).some(function (c) {
          if (c.style.display === 'none') return false;
          return c.textContent.toLowerCase().includes(lq);
        });
      });
    }
    var totalPages = perPage > 0 ? Math.ceil(matched.length / perPage) : 1;

    if (btn.hasAttribute('data-dt-first')) {
      pg = 1;
    } else if (btn.hasAttribute('data-dt-prev')) {
      pg = Math.max(1, pg - 1);
    } else if (btn.hasAttribute('data-dt-next')) {
      pg = Math.min(totalPages, pg + 1);
    } else if (btn.hasAttribute('data-dt-last')) {
      pg = totalPages;
    }

    p.textContent = pg;
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

    var icons = {
      'unsorted': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="m21 16-4 4-4-4M17 20V4M3 8l4-4 4 4M7 4v16"/></svg>',
      'asc': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 text-white"><path d="m18 15-6-6-6 6"/></svg>',
      'desc': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 text-white"><path d="m6 9 6 6 6-6"/></svg>'
    };

    tbl.querySelectorAll('[data-dt-sort]').forEach(function (h) {
      delete h.dataset.dtDir;
      var iconContainer = h.querySelector('[data-dt-sort-icon]');
      if (iconContainer) iconContainer.innerHTML = icons['unsorted'];
    });

    th.dataset.dtDir = dir;
    var activeIcon = th.querySelector('[data-dt-sort-icon]');
    if (activeIcon) activeIcon.innerHTML = icons[dir];

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
    
    var key = cb.dataset.dtColToggle;
    var th = tbl.querySelector('[data-dt-col-head="' + key + '"]');
    var tds = tbl.querySelectorAll('[data-dt-col-body="' + key + '"]');
    var disp = cb.checked ? '' : 'none';
    if (th) th.style.display = disp;
    tds.forEach(function (td) { td.style.display = disp; });
    
    dtApply(tbl);
  });

  // Run on startup
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', dtInitAll);
  } else {
    dtInitAll();
  }
}
</script>
JS;
    }
}
