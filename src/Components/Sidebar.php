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
        $html = '<aside data-state="expanded" data-collapsible="icon" class="group peer hidden md:block text-sidebar-foreground">';
        $html .= '<div class="relative w-[--sidebar-width] bg-transparent transition-[width] duration-200 ease-linear group-data-[state=collapsed]:w-[--sidebar-width-icon]"></div>';
        $html .= '<div class="fixed inset-y-0 z-10 hidden h-svh w-[--sidebar-width] transition-[width] duration-200 ease-linear md:flex left-0 group-data-[state=collapsed]:w-[--sidebar-width-icon] border-r border-sidebar-border bg-sidebar text-sidebar-foreground flex-col">';

        $html .= '<div class="flex items-center gap-2 p-2">';
        $html .= Icon::make()->name('panel-left')->class('size-4 shrink-0 text-sidebar-foreground');
        $html .= '<span data-sidebar-title class="truncate text-sm font-semibold flex-1">' . htmlspecialchars($this->title) . '</span>';
        $html .= '<button type="button" data-sidebar="trigger" class="inline-flex size-7 items-center justify-center rounded-md text-sidebar-foreground/60 transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground" aria-label="Toggle Sidebar">';
        $html .= Icon::make()->name('chevron-left')->class('size-4 transition-transform duration-200 group-data-[state=collapsed]:rotate-180');
        $html .= '</button>';
        $html .= '</div>';

        $html .= '<div class="flex min-h-0 flex-1 flex-col gap-2 overflow-auto group-data-[state=collapsed]:overflow-hidden">';
        $html .= '<div class="relative flex w-full min-w-0 flex-col p-2">';
        $html .= '<div class="w-full text-sm">';
        $html .= '<ul class="flex w-full min-w-0 flex-col gap-1">';

        foreach ($this->items as $item) {
            $isActive = $item['active'] ?? false;
            $isHeader = $item['header'] ?? false;

            if ($isHeader) {
                $html .= '</ul></div></div>';
                $html .= '<div class="relative flex w-full min-w-0 flex-col p-2">';
                $html .= '<div class="flex h-8 shrink-0 items-center rounded-md px-2 text-xs font-medium text-sidebar-foreground/70 group-data-[state=collapsed]:-mt-8 group-data-[state=collapsed]:opacity-0">';
                $html .= htmlspecialchars($item['label']);
                $html .= '</div>';
                $html .= '<div class="w-full text-sm">';
                $html .= '<ul class="flex w-full min-w-0 flex-col gap-1">';
                continue;
            }

            $activeClass = $isActive
                ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium'
                : 'text-sidebar-foreground/60 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground';

            $html .= '<li>';
            $html .= '<a href="' . htmlspecialchars($item['route'] ?? '#') . '" class="flex w-full items-center gap-2 overflow-hidden rounded-md p-2 text-left text-sm outline-none transition-colors ' . $activeClass . '">';
            if (!empty($item['icon'])) {
                $html .= Icon::make()->name($item['icon'])->class('size-4 shrink-0');
            }
            $html .= '<span class="truncate group-data-[state=collapsed]:hidden">' . htmlspecialchars($item['label']) . '</span>';
            $html .= '</a></li>';
        }

        $html .= '</ul></div></div>';
        $html .= '</div>';

        $html .= '<div class="flex flex-col gap-2 p-2">';
        $html .= '<form action="/logout" method="POST">';
        $csrf = \App\Utils\Security::generateCsrfToken();
        $html .= '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf) . '">';
        $html .= '<button type="submit" class="flex w-full items-center gap-2 overflow-hidden rounded-md p-2 text-left text-sm text-sidebar-foreground/60 transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">';
        $html .= Icon::make()->name('log-out')->class('size-4 shrink-0');
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
