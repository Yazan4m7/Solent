<?php

namespace App\Modules\Financing\Database\Seeders;

use App\Modules\Financing\Models\ExpenseCategory;
use App\Modules\Financing\Models\Setting;
use Illuminate\Database\Seeder;

class FinancingSeeder extends Seeder
{
    public function run()
    {
        $setting = Setting::where('key', 'module_financing')->first();

        if (! $setting) {
            $setting = new Setting();
            $setting->key = 'module_financing';
            $setting->value = '0';
            $setting->save();
        }

        $categories = [
            'Materials',
            'Utilities',
            'Rent',
            'Maintenance',
            'External Lab',
            'Other',
            'Payroll',
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(['name' => $category]);
        }
    }
}
