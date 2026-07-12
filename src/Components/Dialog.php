<?php

namespace App\Components;

class Dialog extends Component
{
    private string $id;
    private string $title = '';
    private string $width = 'max-w-3xl';

    public function __construct()
    {
        $this->id = 'dialog-' . uniqid();
    }

    public function id(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function title(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function width(string $width): static
    {
        $this->width = $width;
        return $this;
    }

    public function render(): string
    {
        $html = '<div id="' . $this->id . '" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm" role="dialog" aria-modal="true">';
        $html .= '<div class="relative mx-4 w-full ' . $this->width . '">';
        $html .= '<div class="bg-white rounded-xl shadow-2xl overflow-hidden">';

        if ($this->title) {
            $html .= '<div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">';
            $html .= '<h3 class="text-lg font-semibold text-gray-900">' . htmlspecialchars($this->title) . '</h3>';
            $html .= '<button onclick="closeDialog(\'' . $this->id . '\')" class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">&times;</button>';
            $html .= '</div>';
        }

        $html .= '<div class="p-6">' . $this->content . '</div>';
        $html .= '</div></div></div>';

        $html .= $this->initScript();

        return $html;
    }

    public function getOpenAttr(): string
    {
        return 'onclick="openDialog(\'' . $this->id . '\')"';
    }

    private function initScript(): string
    {
        return <<<JS
<script>
if (typeof window.__dialogInit === 'undefined') {
    window.__dialogInit = true;
    window.openDialog = function(id) {
        var d = document.getElementById(id);
        if (!d) return;
        d.classList.remove('hidden');
        d.classList.add('flex');
        document.body.style.overflow = 'hidden';
        d.addEventListener('click', function(e) {
            if (e.target === d) closeDialog(id);
        });
    };
    window.closeDialog = function(id) {
        var d = document.getElementById(id);
        if (!d) return;
        d.classList.add('hidden');
        d.classList.remove('flex');
        document.body.style.overflow = '';
    };
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[role="dialog"]').forEach(function(d) {
                if (!d.classList.contains('hidden')) closeDialog(d.id);
            });
        }
    });
}
</script>
JS;
    }
}
