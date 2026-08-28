<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_board_action_requests', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->uuid('request_key')->unique();
            $table->unsignedBigInteger('user_id');
            $table->string('action', 32);
            $table->unsignedBigInteger('case_id');
            $table->unsignedTinyInteger('stage')->nullable();
            $table->char('payload_hash', 64);
            $table->longText('response_payload')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['case_id', 'stage']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_board_action_requests');
    }
};
