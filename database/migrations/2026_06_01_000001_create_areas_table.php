<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('tenancy.landlord_connection', 'landlord');

        if (Schema::connection($connection)->hasTable('areas')) {
            return;
        }

        Schema::connection($connection)->create('areas', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('city', 100);
            $table->decimal('latitude', 9, 6);
            $table->decimal('longitude', 9, 6);
            $table->unique(['name', 'city'], 'uq_areas_name_city');
        });
    }

    public function down(): void
    {
        // Shared landlord tables must not be dropped by a tenant rollback.
    }
};
