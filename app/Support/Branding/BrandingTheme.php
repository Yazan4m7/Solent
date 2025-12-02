<?php

namespace App\Support\Branding;

class BrandingTheme
{
    public function renderStyleTag(BrandingSettings $settings): string
    {
        $lines = [];
        foreach ($settings->cssVariables() as $key => $value) {
            $lines[] = "    {$key}: {$value};";
        }

        $css = implode("\n", $lines);

        return "<style>\n:root {\n{$css}\n}\n</style>";
    }
}
