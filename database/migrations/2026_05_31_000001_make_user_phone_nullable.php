<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'phone')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `users` MODIFY `phone` VARCHAR(191) NULL DEFAULT NULL');
        }
    }

    public function down(): void
    {
        // Kept nullable so users without retired contact details remain valid.
    }
};
