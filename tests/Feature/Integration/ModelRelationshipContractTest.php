<?php

namespace Tests\Feature\Integration;

use App\client;
use App\invoice;
use App\payment;
use App\sCase;
use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class ModelRelationshipContractTest extends TestCase
{
    public function test_invoice_relationships_use_the_existing_case_and_doctor_foreign_keys(): void
    {
        $invoice = new invoice();

        $case = $invoice->case();
        $this->assertInstanceOf(BelongsTo::class, $case);
        $this->assertSame('case_id', $case->getForeignKeyName());
        $this->assertInstanceOf(sCase::class, $case->getRelated());

        $client = $invoice->client();
        $this->assertInstanceOf(BelongsTo::class, $client);
        $this->assertSame('doctor_id', $client->getForeignKeyName());
        $this->assertInstanceOf(client::class, $client->getRelated());
    }

    public function test_payment_relationships_keep_client_collector_and_receiver_links_intact(): void
    {
        $payment = new payment();

        $client = $payment->client();
        $this->assertSame('doctor_id', $client->getForeignKeyName());
        $this->assertInstanceOf(client::class, $client->getRelated());

        $collector = $payment->collectorUserRecord();
        $this->assertSame('collector', $collector->getForeignKeyName());
        $this->assertInstanceOf(User::class, $collector->getRelated());

        $receiver = $payment->receivedBy();
        $this->assertSame('received_by', $receiver->getForeignKeyName());
        $this->assertInstanceOf(User::class, $receiver->getRelated());
    }

    public function test_client_cases_relation_keeps_doctor_id_contract(): void
    {
        $relation = (new client())->cases();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('doctor_id', $relation->getForeignKeyName());
        $this->assertInstanceOf(sCase::class, $relation->getRelated());
    }

    public function test_core_business_models_keep_soft_delete_behavior(): void
    {
        foreach ([invoice::class, payment::class, client::class, User::class, sCase::class] as $modelClass) {
            $this->assertContains(
                SoftDeletes::class,
                class_uses_recursive($modelClass),
                "{$modelClass} no longer uses SoftDeletes."
            );
        }
    }
}
