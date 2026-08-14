<?php

namespace App\Components;

class Toast extends Component
{
    private string $title = '';
    private string $description = '';
    private string $variant = 'info';
    private bool $dismissible = true;

    public function title(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function message(string $message): static
    {
        return $this->description($message);
    }

    public function variant(string $variant): static
    {
        $this->variant = in_array($variant, ['success', 'error', 'warning', 'info']) ? $variant : 'info';
        return $this;
    }

    public function dismissible(bool $dismissible = true): static
    {
        $this->dismissible = $dismissible;
        return $this;
    }

    public function render(): string
    {
        $styles = [
            'success' => ['check', 'text-green-400', 'border-green-500/50'],
            'error' => ['alert-circle', 'text-red-400', 'border-red-500/50'],
            'warning' => ['alert-circle', 'text-amber-400', 'border-amber-500/50'],
            'info' => ['alert-circle', 'text-blue-400', 'border-blue-500/50'],
        ];
        [$icon, $iconColor, $borderColor] = $styles[$this->variant];

        $iconSvg = Icon::make()->name($icon)->class('w-4 h-4 absolute left-4 top-4 ' . $iconColor);

        $close = $this->dismissible
            ? '<button type="button" data-toast-close aria-label="Tutup" '
            . 'class="absolute right-3 top-3 text-gray-500 hover:text-white cursor-pointer transition-colors">'
            . "\u{2715}</button>"
            : '';

        $this->class('pointer-events-auto relative w-full rounded-xl border bg-[#1e1d1a] px-4 py-3 pl-10 text-sm text-white shadow-lg '
            . 'opacity-0 -translate-x-2 transition-all duration-300 ' . $borderColor);

        $html = '<div id="flash-toast" role="alert" aria-live="assertive" data-variant="' . htmlspecialchars($this->variant) . '"'
            . ' ' . $this->renderAttrs() . '>';
        $html .= $iconSvg;

        if ($this->title !== '') {
            $html .= '<p class="pr-6 font-semibold">' . htmlspecialchars($this->title) . '</p>';
        }
        if ($this->description !== '') {
            $html .= '<p class="mt-0.5 pr-6 text-xs text-gray-400">' . htmlspecialchars($this->description) . '</p>';
        }
        $html .= $close . '</div>';

        return $html;
    }
}
