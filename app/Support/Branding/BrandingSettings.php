<?php

namespace App\Support\Branding;

class BrandingSettings
{
    public string $tenant;
    public string $name;
    public ?string $logoPath;
    public ?string $faviconPath;
    public string $primaryColor;
    public string $secondaryColor;
    public string $accentColor;
    public string $backgroundColor;
    public array $copy;
    public array $extra;

    public function __construct(
        string $tenant,
        string $name,
        ?string $logoPath,
        ?string $faviconPath,
        string $primaryColor,
        string $secondaryColor,
        string $accentColor,
        string $backgroundColor,
        array $copy = [],
        array $extra = []
    ) {
        $this->tenant = $tenant;
        $this->name = $name;
        $this->logoPath = $logoPath;
        $this->faviconPath = $faviconPath;
        $this->primaryColor = $primaryColor;
        $this->secondaryColor = $secondaryColor;
        $this->accentColor = $accentColor;
        $this->backgroundColor = $backgroundColor;
        $this->copy = $copy;
        $this->extra = $extra;
    }

    public static function fromConfig(string $tenant): self
    {
        $defaults = config('branding.defaults', []);

        return new self(
            $tenant,
            $defaults['name'] ?? 'App',
            $defaults['logo_path'] ?? null,
            $defaults['favicon_path'] ?? null,
            $defaults['primary_color'] ?? '#000000',
            $defaults['secondary_color'] ?? '#000000',
            $defaults['accent_color'] ?? '#000000',
            $defaults['background_color'] ?? '#ffffff',
            $defaults['copy'] ?? [],
            []
        );
    }

    public function cssVariables(): array
    {
        return [
            '--brand-primary' => $this->primaryColor,
            '--brand-secondary' => $this->secondaryColor,
            '--brand-accent' => $this->accentColor,
            '--brand-background' => $this->backgroundColor,
        ];
    }
}
