<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceAccountsTables extends Migration
{
    public function up()
    {
        Schema::create('finance_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->enum('type', ['cash', 'bank']);
            $table->decimal('balance', 12, 2)->default(0);
            $table->string('currency', 10)->default('JOD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('finance_account_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id');
            $table->enum('direction', ['inflow', 'outflow']);
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->string('description', 255)->nullable();
            $table->string('source_type', 60);
            $table->unsignedBigInteger('source_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on('finance_accounts');
            $table->foreign('created_by')->references('id')->on('users');
            $table->unique(['source_type', 'source_id'], 'finance_tx_source_unique');
            $table->index(['date', 'direction']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('finance_account_transactions');
        Schema::dropIfExists('finance_accounts');
    }
}
