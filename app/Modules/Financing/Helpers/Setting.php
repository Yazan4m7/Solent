<?php

use App\Modules\Financing\Models\Setting;
use Illuminate\Support\Facades\Schema;

if (! function_exists('setting')) {
    function setting($key, $default = null)
    {
        if (! Schema::hasTable('settings')) {
            return $default;
        }

        $value = Setting::where('key', $key)->value('value');

        return $value !== null ? $value : $default;
    }
}
