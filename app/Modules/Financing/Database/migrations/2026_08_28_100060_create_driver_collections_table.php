<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverCollectionsTable extends Migration
{
    public function up()
    {
        Schema::create('driver_collections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('invoice_id');
            $table->decimal('collected_amount', 12, 2);
            $table->decimal('submitted_amount', 12, 2)->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('account_id')->references('id')->on('finance_accounts');
            $table->unique(['user_id', 'invoice_id'], 'driver_collection_invoice_user_unique');
            $table->index(['submitted_at', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_collections');
    }
}
