<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('tenancy.landlord_connection', 'landlord');

        if (
            Schema::connection($connection)->hasTable('tenants') &&
            !Schema::connection($connection)->hasColumn('tenants', 'last_login_username')
        ) {
            Schema::connection($connection)->table('tenants', function (Blueprint $table): void {
                $table->string('last_login_username')->nullable()->after('last_login_host');
            });
        }
    }

    public function down(): void
    {
        $connection = config('tenancy.landlord_connection', 'landlord');

        if (
            Schema::connection($connection)->hasTable('tenants') &&
            Schema::connection($connection)->hasColumn('tenants', 'last_login_username')
        ) {
            Schema::connection($connection)->table('tenants', function (Blueprint $table): void {
                $table->dropColumn('last_login_username');
            });
        }
    }
};
