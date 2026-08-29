<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollTables extends Migration
{
    public function up()
    {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('period_month');
            $table->unsignedSmallInteger('period_year');
            $table->text('notes')->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamp('posted_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users');
            $table->unique(['period_month', 'period_year'], 'payroll_period_unique');
        });

        Schema::create('payroll_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('run_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('base_salary', 12, 2);
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('net', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('run_id')->references('id')->on('payroll_runs');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(['run_id', 'user_id'], 'payroll_run_user_unique');
        });

        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('base_salary', 12, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->unique('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_salaries');
        Schema::dropIfExists('payroll_lines');
        Schema::dropIfExists('payroll_runs');
    }
}
