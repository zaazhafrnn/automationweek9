<?php

namespace App\Components;

class Attachment extends Component
{
    private string $state = 'idle';
    private string $size = 'default';
    private string $orientation = 'horizontal';
    private string $media = '';
    private string $title = '';
    private string $description = '';
    private array $actions = [];
    private string $trigger = '';
    private string $mediaVariant = 'icon';
    private bool $preview = false;

    public function mediaVariant(string $variant): static
    {
        $this->mediaVariant = $variant;
        return $this;
    }

    public function state(string $state): static
    {
        $this->state = $state;
        return $this;
    }

    public function size(string $size): static
    {
        $this->size = $size;
        return $this;
    }

    public function orientation(string $orientation): static
    {
        $this->orientation = $orientation;
        return $this;
    }

    public function media(string $html): static
    {
        $this->media = $html;
        return $this;
    }

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

    public function actions(array $items): static
    {
        $this->actions = $items;
        return $this;
    }

    public function trigger(string $html): static
    {
        $this->trigger = $html;
        return $this;
    }

    public function withPreview(): static
    {
        $this->preview = true;
        return $this;
    }

    public function fileInput(string $name, array $attrs = []): static
    {
        $accept = $attrs['accept'] ?? 'image/*';
        $required = !empty($attrs['required']);
        $extraAttr = !empty($attrs['data-error']) ? ' data-error="' . htmlspecialchars($attrs['data-error']) . '"' : '';

        $extraClass = !empty($attrs['class']) ? ' ' . htmlspecialchars($attrs['class']) : '';

        $input = '<input type="file" name="' . htmlspecialchars($name) . '" accept="' . htmlspecialchars($accept) . '"'
            . ($required ? ' required' : '')
            . $extraAttr
            . ($this->preview ? ' data-preview="true"' : '')
            . ' class="absolute inset-0 opacity-0 cursor-pointer z-10' . $extraClass . '">';

        $this->trigger = $input;
        return $this;
    }

    private function rootClasses(): string
    {
        $base = 'rounded-xl border transition-colors relative flex max-w-full min-w-0 group/attachment';
        $base .= ' bg-white';

        if ($this->state === 'idle') {
            $base .= ' border-dashed border-gray-300 hover:border-brand/50 hover:bg-brand/5 cursor-pointer';
        } elseif ($this->state === 'error') {
            $base .= ' border-dashed border-red-300 bg-red-50/30';
        } else {
            $base .= ' border-gray-200';
        }

        if ($this->orientation === 'vertical') {
            $base .= ' flex-col w-28';
        } else {
            $base .= ' items-center gap-2.5';
        }

        if ($this->size === 'sm') {
            $base .= ' p-2 text-xs';
        } else {
            $base .= ' p-2.5';
        }

        return $base;
    }

    private function mediaClasses(): string
    {
        if ($this->mediaVariant === 'image') {
            $base = 'rounded-lg overflow-hidden shrink-0';
            $base .= $this->orientation === 'vertical' ? ' w-full aspect-square' : ' w-14 h-14';
            if ($this->state === 'done') {
                $base .= ' opacity-100';
            } else {
                $base .= ' opacity-60';
            }
            return $base;
        }

        $base = 'bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center shrink-0 overflow-hidden';

        if ($this->size === 'sm') {
            $base .= ' w-8 h-8 [&_svg]:size-4';
        } else {
            $base .= ' w-10 h-10 [&_svg]:size-5';
        }

        if ($this->orientation === 'vertical') {
            $base .= ' w-full [&_svg]:size-6';
        }

        if ($this->state === 'error') {
            $base .= ' bg-red-100 text-red-500';
        }

        return $base;
    }

    private function titleClasses(): string
    {
        $base = 'text-sm font-medium text-gray-900 truncate';
        if ($this->size === 'sm') $base .= ' text-xs';
        if ($this->state === 'uploading' || $this->state === 'processing') {
            $base .= ' animate-pulse bg-gray-200 text-transparent rounded inline-block';
        }
        return $base;
    }

    private function contentClasses(): string
    {
        return 'leading-tight max-w-full min-w-0 flex-1';
    }

    private function descriptionClasses(): string
    {
        $base = 'text-xs truncate';
        $base .= $this->state === 'error' ? ' text-red-500' : ' text-gray-500';
        return $base;
    }

    private function actionsClasses(): string
    {
        $base = 'flex shrink-0 items-center gap-0.5 z-20';
        if ($this->orientation === 'vertical') {
            $base .= ' absolute top-2 right-2';
        }
        return $base;
    }

    private function renderActions(): string
    {
        if (empty($this->actions)) return '';

        $html = '<div data-slot="attachment-actions" class="' . $this->actionsClasses() . '">';
        foreach ($this->actions as $action) {
            $icon = $action['icon'] ?? '';
            $label = $action['label'] ?? '';
            $onclick = $action['onclick'] ?? '';
            $extraAttrs = $action['attrs'] ?? '';

            $html .= '<button type="button" aria-label="' . htmlspecialchars($label) . '"'
                . ($onclick ? ' onclick="' . htmlspecialchars($onclick) . '"' : '')
                . ' class="inline-flex items-center justify-center w-7 h-7 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"'
                . $extraAttrs . '>';
            $html .= $icon;
            $html .= '</button>';
        }
        $html .= '</div>';
        return $html;
    }

    public function render(): string
    {
        if ($this->state === 'idle' && !$this->trigger) {
            // Auto-add file input with generic name if idle has no trigger
            $this->trigger = '<input type="file" class="absolute inset-0 opacity-0 cursor-pointer z-10">';
        }

        $html = '<div data-slot="attachment" data-state="' . htmlspecialchars($this->state) . '"'
            . ' data-size="' . htmlspecialchars($this->size) . '"'
            . ' data-orientation="' . htmlspecialchars($this->orientation) . '"'
            . ' class="' . $this->rootClasses() . '"';

        $extra = '';
        if ($this->state === 'idle') {
            $extra .= ' tabindex="0" role="button"';
        }
        $html .= $extra . '>';

        // Media
        if ($this->media) {
            $html .= '<div data-slot="attachment-media" class="' . $this->mediaClasses() . '">';
            $html .= $this->media;
            $html .= '</div>';
        }

        // Content
        if ($this->title || $this->description) {
            $html .= '<div data-slot="attachment-content" class="' . $this->contentClasses() . '">';
            if ($this->title) {
                $html .= '<p data-slot="attachment-title" class="' . $this->titleClasses() . '">'
                    . htmlspecialchars($this->title) . '</p>';
            }
            if ($this->description) {
                $html .= '<p data-slot="attachment-description" class="' . $this->descriptionClasses() . '">'
                    . htmlspecialchars($this->description) . '</p>';
            }
            $html .= '</div>';
        }

        // Actions
        $html .= $this->renderActions();

        // Trigger overlay (file input or button)
        if ($this->trigger) {
            $html .= $this->trigger;
        }

        $html .= '</div>';
        return $html;
    }
}
