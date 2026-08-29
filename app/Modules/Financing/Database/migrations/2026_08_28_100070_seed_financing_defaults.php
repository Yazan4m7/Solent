<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedFinancingDefaults extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('expense_categories')) {
            return;
        }

        foreach (['Materials', 'Utilities', 'Rent', 'Maintenance', 'External Lab', 'Other', 'Payroll'] as $name) {
            DB::table('expense_categories')->updateOrInsert(
                ['name' => $name],
                ['updated_at' => now(), 'created_at' => now(), 'deleted_at' => null]
            );
        }
    }

    public function down()
    {
        if (Schema::hasTable('expense_categories')) {
            DB::table('expense_categories')
                ->whereIn('name', ['Materials', 'Utilities', 'Rent', 'Maintenance', 'External Lab', 'Other', 'Payroll'])
                ->delete();
        }
    }
}
