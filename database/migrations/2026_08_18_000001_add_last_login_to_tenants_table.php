<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('tenancy.landlord_connection', 'landlord');

        if (!Schema::connection($connection)->hasTable('tenants')) {
            return;
        }

        Schema::connection($connection)->table('tenants', function (Blueprint $table): void {
            if (!Schema::connection(config('tenancy.landlord_connection', 'landlord'))->hasColumn('tenants', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('activated_at');
            }

            if (!Schema::connection(config('tenancy.landlord_connection', 'landlord'))->hasColumn('tenants', 'last_login_host')) {
                $table->string('last_login_host')->nullable()->after('last_login_at');
            }
        });
    }

    public function down(): void
    {
        $connection = config('tenancy.landlord_connection', 'landlord');

        if (!Schema::connection($connection)->hasTable('tenants')) {
            return;
        }

        Schema::connection($connection)->table('tenants', function (Blueprint $table) use ($connection): void {
            if (Schema::connection($connection)->hasColumn('tenants', 'last_login_host')) {
                $table->dropColumn('last_login_host');
            }

            if (Schema::connection($connection)->hasColumn('tenants', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }
        });
    }
};
