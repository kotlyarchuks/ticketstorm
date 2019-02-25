<?php
/**
 * Created by PhpStorm.
 * User: kotlyarchuk
 * Date: 21.02.2019
 * Time: 17:14
 */

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use Tests\TestCase;

class PurchaseTicketsTest extends TestCase {

    use DatabaseMigrations;

    /** @test * */
    function user_can_purchase_tickets_for_concert()
    {
        $paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $paymentGateway);
        //create concert
        $concert = factory(Concert::class)->create(['price' => 4250]);
        //request to endpoint
        $response = $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email'   => 'johndoe@example.com',
            'tickets' => 3,
            'token'   => $paymentGateway->getValidTestToken(),
        ]);
        $response->assertStatus(201);
        //assert that we charged needed amount
        $this->assertEquals(12750, $paymentGateway->totalCharged());
        //assert that concert has order
        $order = $concert->orders()->where('email', 'johndoe@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets);
    }
}
