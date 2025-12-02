<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeviceIdToCaseLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('case_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('device_id')->nullable()->after('stage');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('set null');
            // Add a new column to store the action type (1=set, 2=start, 3=complete)
            $table->tinyInteger('action_type')->nullable()->after('device_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('case_logs', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropColumn(['device_id', 'action_type']);
        });
    }
}
