<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancePaymentAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('finance_payment_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('account_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('payment_id')->references('id')->on('payments');
            $table->foreign('account_id')->references('id')->on('finance_accounts');
            $table->unique('payment_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('finance_payment_accounts');
    }
}
