<?php

namespace App\Modules\Financing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financing\Models\Setting;
use Illuminate\Http\Request;

class AdminModuleSettingController extends Controller
{
    public function update(Request $request)
    {
        abort_unless(auth()->check() && (bool) auth()->user()->is_admin, 403);

        $data = $request->validate([
            'module' => 'required|in:financing',
            'enabled' => 'required|boolean',
        ]);

        $setting = Setting::where('key', 'module_' . $data['module'])->first();

        if (! $setting) {
            $setting = new Setting();
            $setting->key = 'module_' . $data['module'];
        }

        $setting->value = $data['enabled'] ? '1' : '0';
        $setting->save();

        session()->flash('success', __('financing::financing.module_updated'));

        return back();
    }
}
