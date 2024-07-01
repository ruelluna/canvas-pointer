<?php

namespace RuelLuna\CanvasPointer\Forms\Components;

use Filament\Forms\Components\Field;

class CanvasPointerField extends Field
{
    protected string $view = 'canvas-pointer::forms.components.image-pointer';

    protected int | \Closure | null $width = null;

    protected int | \Closure | null $height = null;

    protected int | \Closure | null $pointRadius = null;

    protected string | \Closure | null $imageUrl = null;

    public function width(int | \Closure | null $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function height(int | \Closure | null $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function imageUrl(string | \Closure | null $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function pointRadius(int | \Closure | null $pointRadius): static
    {
        $this->pointRadius = $pointRadius;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->evaluate($this->width);
    }

    public function getHeight(): ?int
    {
        return $this->evaluate($this->height);
    }

    public function getPointRadius(): ?int
    {
        return $this->evaluate($this->pointRadius);
    }

    public function getImageUrl()
    {
        return $this->evaluate($this->imageUrl);
    }
}
