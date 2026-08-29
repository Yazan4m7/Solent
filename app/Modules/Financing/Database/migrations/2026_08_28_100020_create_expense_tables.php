<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseTables extends Migration
{
    public function up()
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('account_id');
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->date('date');
            $table->string('receipt_path', 255)->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->unsignedTinyInteger('recurring_day')->nullable();
            $table->unsignedBigInteger('recurring_parent_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('expense_categories');
            $table->foreign('account_id')->references('id')->on('finance_accounts');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('recurring_parent_id')->references('id')->on('expenses');
            $table->unique(['recurring_parent_id', 'date'], 'expense_recurring_occurrence_unique');
            $table->index(['date', 'category_id', 'account_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
    }
}
