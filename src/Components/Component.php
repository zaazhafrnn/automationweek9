<?php
namespace App\Components;

abstract class Component
{
    protected array $attrs = [];
    protected string $content = '';

    public static function make(): static
    {
        return new static;
    }

    public function attr(string $name, string $value): static
    {
        $this->attrs[$name] = $value;
        return $this;
    }

    public function class(string $class): static
    {
        $existing = explode(' ', $this->attrs['class'] ?? '');
        $merged = array_unique(array_merge($existing, explode(' ', $class)));
        $this->attrs['class'] = implode(' ', $merged);
        return $this;
    }

    public function content(string $html): static
    {
        $this->content = $html;
        return $this;
    }

    abstract public function render(): string;

    public function __toString(): string
    {
        return $this->render();
    }

    protected function renderAttrs(array $extra = []): string
    {
        $all = array_merge($this->attrs, $extra);
        $parts = [];
        foreach ($all as $name => $value) {
            $parts[] = $name . '="' . htmlspecialchars($value) . '"';
        }
        return implode(' ', $parts);
    }
}
