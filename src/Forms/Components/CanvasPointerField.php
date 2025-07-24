<?php

namespace RuelLuna\CanvasPointer\Forms\Components;

use Filament\Forms\Components\Field;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CanvasPointerField extends Field
{
    protected string $view = 'canvas-pointer::forms.components.image-pointer';

    protected int | \Closure | null $width = null;

    protected int | \Closure | null $height = null;

    protected int | \Closure | null $pointRadius = null;

    protected string | \Closure | null $imageUrl = null;

    protected string | \Closure | null $storageDisk = 'public';

    protected string | \Closure | null $storageDirectory = 'canvas-pointer';

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

    public function storageDisk(string | \Closure | null $disk): static
    {
        $this->storageDisk = $disk;

        return $this;
    }

    public function storageDirectory(string | \Closure | null $directory): static
    {
        $this->storageDirectory = $directory;

        return $this;
    }

    public function getStorageDisk(): string
    {
        return $this->evaluate($this->storageDisk) ?? 'public';
    }

    public function getStorageDirectory(): string
    {
        return $this->evaluate($this->storageDirectory) ?? 'canvas-pointer';
    }

    public function saveBase64Image(string $base64Image): string
    {
        // Remove the data:image/png;base64, part
        $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);

        // Decode the base64 string
        $imageData = base64_decode($base64Image);

        // Generate a unique filename
        $filename = Str::uuid() . '.png';

        // Get the storage path
        $path = $this->getStorageDirectory() . '/' . $filename;

        // Save the file
        Storage::disk($this->getStorageDisk())->put($path, $imageData);

        // Return the public URL
        return Storage::disk($this->getStorageDisk())->url($path);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (Field $component, $state) {
            // If the state is already a URL, keep it as is
            if (is_string($state) && (Str::startsWith($state, 'http://') || Str::startsWith($state, 'https://') || Str::startsWith($state, '/storage/'))) {
                return;
            }
        });

        $this->dehydrateStateUsing(function (Field $component, $state) {
            // If the state is a base64 image, save it and return the URL
            if (is_string($state) && Str::startsWith($state, 'data:image/')) {
                return $this->saveBase64Image($state);
            }

            return $state;
        });
    }
}
