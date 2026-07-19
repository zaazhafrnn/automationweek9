<?php

namespace App\Components;

class Sidebar extends Component
{
    private array $items = [];
    private string $title = '';

    public function items(array $items): static
    {
        $this->items = $items;
        return $this;
    }

    public function title(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function render(): string
    {
        $html = '<aside data-state="expanded" data-collapsible="icon" class="group peer hidden md:block text-sidebar-foreground bg-sidebar">';
        $html .= '<div class="relative w-[var(--sidebar-width)] bg-transparent transition-[width] duration-200 ease-linear group-data-[state=collapsed]:w-[var(--sidebar-width-icon)]"></div>';
        $html .= '<div class="fixed inset-y-0 z-20 hidden h-svh w-[var(--sidebar-width)] transition-[width] duration-200 ease-linear md:flex left-0 group-data-[state=collapsed]:w-[var(--sidebar-width-icon)] border-r border-sidebar-border bg-sidebar text-sidebar-foreground flex-col">';

        // Header Area (Logo, title, and trigger button)
        $html .= '<div class="flex items-center justify-between gap-2 p-3 border-b border-white/15 mb-2 group-data-[state=collapsed]:p-2 group-data-[state=collapsed]:flex-col group-data-[state=collapsed]:gap-3">';
        $html .= '<div class="flex items-center gap-2 overflow-hidden flex-grow group-data-[state=collapsed]:justify-center">';
        $html .= '<img src="/image/logo-aw.png" alt="AW Logo" class="w-8 h-8 object-contain bg-white rounded-full border border-sidebar-border shrink-0">';
        $html .= '<span data-sidebar-title class="truncate text-xs font-bold font-display tracking-tight text-white flex-1 group-data-[state=collapsed]:hidden">' . htmlspecialchars($this->title) . '</span>';
        $html .= '</div>';
        
        $html .= '<button type="button" data-sidebar="trigger" class="inline-flex size-7 shrink-0 items-center justify-center rounded-lg border border-sidebar-border text-sidebar-foreground/50 transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground cursor-pointer shadow-sm" aria-label="Toggle Sidebar">';
        $html .= Icon::make()->name('panel-left')->class('h-3.5 w-3.5 group-data-[state=collapsed]:rotate-180 transition-transform');
        $html .= '</button>';
        $html .= '</div>';

        // Nav List Area
        $html .= '<div class="flex min-h-0 flex-1 flex-col gap-1 overflow-y-auto px-2 py-3 group-data-[state=collapsed]:px-1">';
        $html .= '<div class="w-full text-xs">';
        $html .= '<ul class="flex w-full min-w-0 flex-col gap-1">';

        foreach ($this->items as $item) {
            $isActive = $item['active'] ?? false;
            $isHeader = $item['header'] ?? false;

            if ($isHeader) {
                $html .= '</ul></div>';
                $html .= '<div class="w-full text-sm mt-4">';
                $html .= '<div class="flex h-8 shrink-0 items-center px-3 text-xs font-semibold text-white/50 group-data-[state=collapsed]:hidden">';
                $html .= htmlspecialchars($item['label']);
                $html .= '</div>';
                $html .= '<ul class="flex w-full min-w-0 flex-col gap-1">';
                continue;
            }

            $activeClass = $isActive
                ? 'bg-white/15 text-white font-bold'
                : 'text-white/80 hover:bg-white/10 hover:text-white font-semibold';

            $html .= '<li>';
            $html .= '<a href="' . htmlspecialchars($item['route'] ?? '#') . '" class="flex w-full items-center gap-3.5 rounded-lg px-3.5 py-2.5 text-[15px] outline-none transition-all duration-150 group-data-[state=collapsed]:px-2 group-data-[state=collapsed]:justify-center ' . $activeClass . '">';
            if (!empty($item['icon'])) {
                $html .= Icon::make()->name($item['icon'])->class('size-5 shrink-0 transition-transform');
            }
            $html .= '<span class="truncate group-data-[state=collapsed]:hidden">' . htmlspecialchars($item['label']) . '</span>';
            $html .= '</a></li>';
        }

        $html .= '</ul></div>';
        $html .= '</div>';

        // Footer Area (Logout button)
        $html .= '<div class="flex flex-col gap-2 p-2 border-t border-white/15 group-data-[state=collapsed]:p-1">';
        $html .= '<form action="/logout" method="POST" class="m-0">';
        $csrf = \App\Utils\Security::generateCsrfToken();
        $html .= '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf) . '">';
        $html .= '<button type="submit" class="flex w-full items-center gap-3.5 rounded-lg px-3.5 py-2.5 text-sm font-semibold text-white/70 transition-colors hover:bg-white/10 hover:text-white cursor-pointer group-data-[state=collapsed]:px-2 group-data-[state=collapsed]:justify-center">';
        $html .= Icon::make()->name('log-out')->class('size-5 shrink-0');
        $html .= '<span class="truncate group-data-[state=collapsed]:hidden">Keluar</span>';
        $html .= '</button></form></div>';

        $html .= '</div>';
        $html .= '</aside>';
        $html .= $this->initScript();
        return $html;
    }

    private function initScript(): string
    {
        return <<<JS
<script>
(function () {
    var el = document.querySelector('[data-collapsible]');
    if (!el) return;
    var btn = el.querySelector('[data-sidebar="trigger"]');
    var KEY = 'sidebar_state';

    function getCookie() {
        var m = document.cookie.match('(^|;)\\s*' + KEY + '=([^;]*)');
        return m ? m[2] : '';
    }

    function setCookie(v) {
        document.cookie = KEY + '=' + v + ';path=/;max-age=604800';
    }

    function apply() {
        el.dataset.state = getCookie() === 'collapsed' ? 'collapsed' : 'expanded';
    }

    btn.addEventListener('click', function () {
        setCookie(el.dataset.state === 'expanded' ? 'collapsed' : 'expanded');
        apply();
    });

    apply();
})();
</script>
JS;
    }
}
