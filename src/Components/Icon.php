<?php

namespace App\Components;

class Icon extends Component
{
    private string $name = '';
    private string $iconClass = '';

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
        // Remove the comment and class attribute, add passed classes
        $svg = preg_replace('/<!--.*?-->\s*/s', '', $svg);
        $svg = preg_replace('/class="lucide[^"]*"/', '', $svg);
        // Inject custom classes after <svg
        $class = $this->attrs['class'] ?? '';
        $svg = preg_replace('/<svg/', '<svg class="' . htmlspecialchars($class) . '"', $svg);
        return $svg;
    }
}
