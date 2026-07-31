<?php

namespace App\Components;

class Attachment extends Component
{
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
    private string $idleDescription = '';
    private string $fileSize = '';

    public function mediaVariant(string $variant): static
    {
        $this->mediaVariant = $variant;
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
        $maxSizeAttr = !empty($attrs['max-size']) ? ' data-max-size="' . ((int) $attrs['max-size']) . '"' : '';

        $input = '<input type="file" name="' . htmlspecialchars($name) . '" accept="' . htmlspecialchars($accept) . '"'
            . ($required ? ' required' : '')
            . $extraAttr
            . $maxSizeAttr
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

    public function idleDescription(string $desc): static
    {
        $this->idleDescription = $desc;
        return $this;
    }

    public function fileSize(string $size): static
    {
        $this->fileSize = $size;
        return $this;
    }

    public function render(): string
    {
        $hasFile = !empty($this->fileUrl);
        $dataState = $hasFile ? 'done' : 'idle';

        $rootClasses = 'rounded-xl border border-gray-200 transition-colors relative flex max-w-full min-w-0 group/attachment bg-white';
        if ($this->orientation === 'vertical') {
            $rootClasses .= ' flex-col w-28';
        } else {
            $rootClasses .= ' items-center gap-2.5';
        }
        if ($this->size === 'sm') {
            $rootClasses .= ' p-2 text-xs';
        } else {
            $rootClasses .= ' p-2.5';
        }

        $idleDesc = $this->idleDescription ?: $this->description;
        $html = '<div data-slot="attachment"'
            . ' data-state="' . $dataState . '"'
            . ' data-size="' . htmlspecialchars($this->size) . '"'
            . ' data-orientation="' . htmlspecialchars($this->orientation) . '"'
            . ' data-original-title="' . htmlspecialchars($this->title) . '"'
            . ' data-idle-title="' . htmlspecialchars($this->idleTitle) . '"'
            . ' data-idle-description="' . htmlspecialchars($idleDesc) . '"'
            . ' data-original-media="' . htmlspecialchars($this->originalMedia) . '"'
            . ' data-original-src="' . htmlspecialchars($this->originalSrc) . '"'
            . ' data-input-name="' . htmlspecialchars($this->inputName) . '"'
            . ' class="' . $rootClasses . '"'
            . ' tabindex="0" role="button"'
            . '>';

        if ($this->media) {
            $mediaClasses = 'rounded-lg overflow-hidden shrink-0';
            if ($this->mediaVariant !== 'image') {
                $mediaClasses = 'bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center shrink-0 overflow-hidden';
                if ($this->size === 'sm') {
                    $mediaClasses .= ' w-8 h-8 [&_svg]:size-4';
                } else {
                    $mediaClasses .= ' w-10 h-10 [&_svg]:size-5';
                }
                if ($this->orientation === 'vertical') {
                    $mediaClasses .= ' w-full [&_svg]:size-6';
                }
            } else {
                $mediaClasses .= $this->orientation === 'vertical' ? ' w-full aspect-square' : ' w-14 h-14';
            }
            $html .= '<div data-slot="attachment-media" class="' . $mediaClasses . '">';
            $html .= $this->media;
            $html .= '</div>';
        }

        $descText = $this->description;
        if ($hasFile) {
            if ($this->fileSize) {
                $descText = $this->fileSize;
            } elseif ($this->fileUrl) {
                $publicDir = dirname(__DIR__, 2) . '/public';
                $filePath = $publicDir . parse_url($this->fileUrl, PHP_URL_PATH);
                if (file_exists($filePath) && is_file($filePath)) {
                    $bytes = filesize($filePath);
                    if ($bytes < 1024 * 1024) {
                        $descText = round($bytes / 1024, 1) . ' KB';
                    } else {
                        $descText = round($bytes / (1024 * 1024), 2) . ' MB';
                    }
                }
            }
        }
        if ($this->title || $descText) {
            $titleClasses = 'text-sm font-medium truncate pr-10';
            if ($this->size === 'sm') $titleClasses .= ' text-xs';
            if ($this->titleClass) $titleClasses .= ' ' . $this->titleClass;

            $html .= '<div data-slot="attachment-content" class="leading-tight max-w-full min-w-0 flex-1">';
            if ($this->title) {
                $html .= '<p data-slot="attachment-title" class="' . $titleClasses . '">'
                    . htmlspecialchars($this->title) . '</p>';
            }
            if ($descText) {
                $html .= '<p data-slot="attachment-description" class="text-xs truncate text-gray-500">'
                    . htmlspecialchars($descText) . '</p>';
            }
            $html .= '</div>';
        }

        if (!empty($this->actions)) {
            $actionsClasses = 'flex shrink-0 items-center gap-0.5 z-20';
            if ($this->orientation === 'vertical') {
                $actionsClasses .= ' absolute top-2 right-2';
            }
            $html .= '<div data-slot="attachment-actions" class="' . $actionsClasses . '">';
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
        }

        if ($this->clearable) {
            $trashIcon = (new Icon)->name('trash-2')->class('w-5 h-5 text-red-500')->render();
            $html .= '<button type="button" aria-label="Hapus" data-clear-attachment'
                . ' onclick="event.preventDefault();"'
                . ' class="absolute right-2 top-1/2 -translate-y-1/2 z-20 flex items-center justify-center w-10 h-10 bg-white rounded-full shadow-lg border border-gray-200 hover:bg-red-50 hover:border-red-300 transition-colors cursor-pointer">'
                . $trashIcon
                . '</button>';
        }

        if ($this->trigger) {
            $html .= $this->trigger;
        }

        $html .= '</div>';
        return $html;
    }
}
