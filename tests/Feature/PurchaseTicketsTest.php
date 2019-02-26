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

    protected function setUp()
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    protected function buyTickets($concert, $params)
    {
        return $this->json('POST', "/concerts/{$concert->id}/orders", $params);
    }

    protected function assertHasValidationErrors($response, $key)
    {
        $response->assertStatus(422);
        $this->assertArrayHasKey($key, $response->decodeResponseJson()['errors']);
    }

    /** @test * */
    function user_can_purchase_tickets_for_concert()
    {
        //create concert
        $concert = factory(Concert::class)->state('published')->create(['price' => 4250]);
        $concert->addTickets(5);
        //request to endpoint
        $response = $this->buyTickets($concert, [
            'email'   => 'johndoe@example.com',
            'tickets' => 3,
            'token'   => $this->paymentGateway->getValidTestToken(),
        ]);
        $response->assertStatus(201);
        //assert that we charged needed amount
        $this->assertEquals(12750, $this->paymentGateway->totalCharged());
        //assert that concert has order
        $order = $concert->orders()->where('email', 'johndoe@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());
    }

    /** @test * */
    function cannot_purchase_tickets_for_unpublished_concert()
    {
        $concert = factory(Concert::class)->state('unpublished')->create();
        $concert->addTickets(3);

        $response = $this->buyTickets($concert, [
            'email'   => 'johndoe@example.com',
            'tickets' => 3,
            'token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(404);

        $this->assertEquals(0, $concert->orders()->count());

        $this->assertEquals(0, $this->paymentGateway->totalCharged());
    }

    /** @test * */
    function cannot_purchase_more_tickets_that_remained()
    {
        $this->disableExceptionHandling();
        $concert = factory(Concert::class)->state('published')->create();
        $concert->addTickets(50);

        $response = $this->buyTickets($concert, [
            'email'   => 'johndoe@example.com',
            'tickets' => 51,
            'token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(422);

        $order = $concert->orders()->where('email', 'johndoe@example.com')->first();
        $this->assertNull($order);

        $this->assertEquals(0, $this->paymentGateway->totalCharged());
        $this->assertEquals(50, $concert->remainingTickets());
    }

    /** @test * */
    function email_is_required_to_buy_tickets()
    {
        $concert = factory(Concert::class)->state('published')->create();

        $response = $this->buyTickets($concert, [
            'tickets' => 3,
            'token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertHasValidationErrors($response, 'email');

        $response = $this->buyTickets($concert, [
            'email'   => 'my-email-address',
            'tickets' => 3,
            'token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertHasValidationErrors($response, 'email');
    }

    /** @test * */
    function ticket_quantity_is_required_to_buy_tickets()
    {
        $concert = factory(Concert::class)->state('published')->create();

        $response = $this->buyTickets($concert, [
            'email' => 'john@doe.com',
            'token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertHasValidationErrors($response, 'tickets');

        $response = $this->buyTickets($concert, [
            'tickets' => 0,
            'email'   => 'john@doe.com',
            'token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertHasValidationErrors($response, 'tickets');
    }

    /** @test * */
    function token_is_required_to_buy_tickets()
    {
        $concert = factory(Concert::class)->state('published')->create();

        $response = $this->buyTickets($concert, [
            'email'   => 'john@doe.com',
            'tickets' => 2
        ]);

        $this->assertHasValidationErrors($response, 'token');
    }

    /** @test * */
    function order_is_not_created_if_payment_fails()
    {
        $concert = factory(Concert::class)->state('published')->create();
        $concert->addTickets(2);

        $response = $this->buyTickets($concert, [
            'email'   => 'john@doe.com',
            'tickets' => 2,
            'token'   => 'invalid-token',
        ]);

        $response->assertStatus(422);

        $order = $concert->orders()->where('email', 'john@doe.com')->first();
        $this->assertNull($order);
    }
}
