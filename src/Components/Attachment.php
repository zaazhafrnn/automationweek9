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
    private string $titleClass = '';
    private bool $clearable = false;
    private string $fileUrl = '';
    private string $originalMedia = '';
    private string $originalSrc = '';
    private string $inputName = '';
    private string $idleTitle = '';

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

    public function titleClass(string $class): static
    {
        $this->titleClass = $class;
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

    public function clearable(bool $v = true): static
    {
        $this->clearable = $v;
        return $this;
    }

    public function fileUrl(string $url): static
    {
        $this->fileUrl = $url;
        return $this;
    }

    public function originalMedia(string $html): static
    {
        $this->originalMedia = $html;
        return $this;
    }

    public function originalSrc(string $src): static
    {
        $this->originalSrc = $src;
        return $this;
    }

    public function fileInput(string $name, array $attrs = []): static
    {
        $this->inputName = $name;
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

    public function idleTitle(string $title): static
    {
        $this->idleTitle = $title;
        return $this;
    }

    private function rootClasses(): string
    {
        $base = 'rounded-xl border transition-colors relative flex max-w-full min-w-0 group/attachment';
        $base .= ' bg-white';

        if ($this->state === 'idle') {
            $base .= ' border-dashed border-gray-300 hover:border-brand/50 hover:bg-brand/5 cursor-pointer';
        } elseif ($this->state === 'error') {
            $base .= ' border-red-500 bg-red-50/30';
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
        $base = 'text-sm font-medium text-gray-700 truncate';
        if ($this->size === 'sm') $base .= ' text-xs';
        if ($this->state === 'uploading' || $this->state === 'processing') {
            $base .= ' animate-pulse bg-gray-200 text-transparent rounded inline-block';
        }
        if ($this->titleClass) $base .= ' ' . $this->titleClass;
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
            $this->trigger = '<input type="file" class="absolute inset-0 opacity-0 cursor-pointer z-10">';
        }

        $isLink = $this->state === 'done' && $this->fileUrl;
        $tag = $isLink ? 'a' : 'div';
        $extraAttrs = '';

        if ($isLink) {
            $extraAttrs .= ' href="' . htmlspecialchars($this->fileUrl) . '" target="_blank" rel="noopener noreferrer"';
        }

        $html = '<' . $tag . ' data-slot="attachment" data-state="' . htmlspecialchars($this->state) . '"'
            . ' data-size="' . htmlspecialchars($this->size) . '"'
            . ' data-orientation="' . htmlspecialchars($this->orientation) . '"'
            . ' data-original-title="' . htmlspecialchars($this->title) . '"'
            . ' data-idle-title="' . htmlspecialchars($this->idleTitle) . '"'
            . ' data-original-media="' . htmlspecialchars($this->originalMedia) . '"'
            . ' data-original-src="' . htmlspecialchars($this->originalSrc) . '"'
            . ' data-input-name="' . htmlspecialchars($this->inputName) . '"'
            . ' class="' . $this->rootClasses() . '"'
            . $extraAttrs;

        if (!$isLink && $this->state === 'idle') {
            $html .= ' tabindex="0" role="button"';
        }
        $html .= '>';

        if ($this->media) {
            $html .= '<div data-slot="attachment-media" class="' . $this->mediaClasses() . '">';
            $html .= $this->media;
            $html .= '</div>';
        }

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

        $html .= $this->renderActions();

        if ($this->clearable) {
            $hidden = $this->state !== 'done' ? ' hidden' : '';
            $html .= '<button type="button" aria-label="Hapus" data-clear-attachment'
                . ' onclick="event.preventDefault();"'
                . ' class="absolute right-2 top-1/2 -translate-y-1/2 z-20 flex items-center justify-center w-10 h-10 bg-white rounded-full shadow-lg border border-gray-200 hover:bg-red-50 hover:border-red-300 transition-colors' . $hidden . '">'
                . '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>'
                . '</button>';
        }

        if ($this->trigger) {
            $hidden = $this->state !== 'idle' ? ' hidden' : '';
            $html .= str_replace('class="absolute', 'class="absolute' . $hidden, $this->trigger);
        }

        $html .= '</' . $tag . '>';
        return $html;
    }
}
