<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceInstallmentsTable extends Migration
{
    public function up()
    {
        Schema::create('invoice_installments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('client_id');
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('payment_id')->references('id')->on('payments');
            $table->index(['client_id', 'due_date', 'paid_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_installments');
    }
}
