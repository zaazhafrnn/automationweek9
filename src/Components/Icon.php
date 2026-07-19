<?php

namespace App\Components;

class Icon extends Component
{
    private string $name = '';

    public function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function render(): string
    {
        $path = __DIR__ . '/../../public/icons/' . $this->name . '.svg';
        if (!file_exists($path)) {
            return '';
        }
        $svg = file_get_contents($path);
        $svg = preg_replace('/class="lucide[^"]*"/', '', $svg);
        $class = trim($this->attrs['class'] ?? '');
        $svg = preg_replace('/<svg/', '<svg class="' . htmlspecialchars($class) . '"', $svg);
        return str_replace(["\r", "\n"], '', $svg);
    }
}
