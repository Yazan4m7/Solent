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
            Schema::connection($connection)->create('tenants', function (Blueprint $table): void {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('slug')->unique();
                $table->string('name');
                $table->string('database_name')->unique();
                $table->string('status')->default('provisioning')->index();
                $table->string('currency_code', 8)->default('JOD');
                $table->string('branding_key')->nullable();
                $table->json('context')->nullable();
                $table->json('branding')->nullable();
                $table->timestamp('activated_at')->nullable();
                $table->timestamp('failed_at')->nullable();
                $table->string('failed_step')->nullable();
                $table->text('failure_message')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection($connection)->hasTable('tenant_domains')) {
            Schema::connection($connection)->create('tenant_domains', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('host')->unique();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::connection($connection)->hasTable('tenant_provisioning_events')) {
            Schema::connection($connection)->create('tenant_provisioning_events', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
                $table->string('step');
                $table->string('status')->index();
                $table->text('message')->nullable();
                $table->json('payload')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        $connection = config('tenancy.landlord_connection', 'landlord');

        Schema::connection($connection)->dropIfExists('tenant_provisioning_events');
        Schema::connection($connection)->dropIfExists('tenant_domains');
        Schema::connection($connection)->dropIfExists('tenants');
    }
};
