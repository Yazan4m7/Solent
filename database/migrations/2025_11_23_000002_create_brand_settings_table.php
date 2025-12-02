<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandSettingsTable extends Migration
{
    public function up(): void
    {
        Schema::create('brand_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('tenant')->default('default')->unique();
            $table->string('name')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('accent_color')->nullable();
            $table->string('background_color')->nullable();
            $table->json('copy')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_settings');
    }
}
