<?php
namespace App\Components;

class Button extends Component
{
    private string $tag = 'button';
    private string $label = '';

    public function tag(string $tag): static
    {
        $this->tag = $tag;
        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function variant(string $variant): static
    {
        $map = [
            'primary' => 'bg-[#bc0301] text-white hover:bg-[#bc0301]/90',
            'secondary' => 'bg-gray-100 text-gray-700 hover:bg-gray-200',
            'ghost' => 'text-gray-600 hover:text-gray-900',
            'link' => 'text-blue-600 hover:text-blue-800 underline-offset-2',
            'danger' => 'bg-red-600 text-white hover:bg-red-700',
            'success' => 'bg-green-600 text-white hover:bg-green-700',
        ];
        return $this->class($map[$variant] ?? '');
    }

    public function size(string $size): static
    {
        $map = [
            'sm' => 'px-2.5 py-1 text-xs',
            'md' => 'px-4 py-2 text-sm',
            'lg' => 'px-6 py-3 text-base',
            'icon' => 'w-8 h-8 flex items-center justify-center',
        ];
        return $this->class($map[$size] ?? '');
    }

    public function render(): string
    {
        $this->class('inline-flex items-center justify-center rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 cursor-pointer');

        $btn = '<' . $this->tag;
        $btn .= ' ' . $this->renderAttrs();
        $btn .= '>' . htmlspecialchars($this->label) . $this->content . '</' . $this->tag . '>';

        return $btn;
    }
}
