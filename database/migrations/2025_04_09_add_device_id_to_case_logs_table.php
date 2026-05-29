<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('case_logs')) {
            return;
        }

        Schema::table('case_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('case_logs', 'device_id')) {
                $table->bigInteger('device_id')->nullable()->after('stage');
            }

            if (!Schema::hasColumn('case_logs', 'action_type')) {
                // 1=set, 2=start, 3=complete
                $table->tinyInteger('action_type')->nullable()->after('device_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('case_logs')) {
            return;
        }

        Schema::table('case_logs', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('case_logs', 'device_id') ? 'device_id' : null,
                Schema::hasColumn('case_logs', 'action_type') ? 'action_type' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
