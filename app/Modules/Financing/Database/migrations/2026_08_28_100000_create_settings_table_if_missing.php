<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTableIfMissing extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('key', 100)->unique();
                $table->text('value')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        $values = ['value' => '0', 'updated_at' => now(), 'created_at' => now()];

        if (Schema::hasColumn('settings', 'deleted_at')) {
            $values['deleted_at'] = null;
        }

        DB::table('settings')->updateOrInsert(
            ['key' => 'module_financing'],
            $values
        );
    }

    public function down()
    {
        // Safe rollback: do not drop a pre-existing shared settings table.
        if (Schema::hasTable('settings')) {
            DB::table('settings')->where('key', 'module_financing')->delete();
        }
    }
}
