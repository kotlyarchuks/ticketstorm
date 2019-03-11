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
        $savedRequest = $this->app['request'];
        $response = $this->json('POST', "/concerts/{$concert->id}/orders", $params);
        $this->app['request'] = $savedRequest;
        return $response;
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
        $concert = factory(Concert::class)->state('published')->create(['price' => 4250])->addTickets(5);
        //request to endpoint
        $response = $this->buyTickets($concert, [
            'email'   => 'johndoe@example.com',
            'tickets' => 3,
            'token'   => $this->paymentGateway->getValidTestToken(),
        ]);
        $response->assertStatus(201);
        $response->assertJson([
            'email'   => 'johndoe@example.com',
            'tickets' => 3,
            'amount'  => 12750
        ]);

        $this->assertEquals(12750, $this->paymentGateway->totalCharged());

        $this->assertTrue($concert->hasOrderFor('johndoe@example.com'));
        $this->assertEquals(3, $concert->ordersFor('johndoe@example.com')->first()->ticketCount());
    }

    /** @test * */
    function cannot_purchase_tickets_for_unpublished_concert()
    {
        $concert = factory(Concert::class)->state('unpublished')->create()->addTickets(3);

        $response = $this->buyTickets($concert, [
            'email'   => 'johndoe@example.com',
            'tickets' => 3,
            'token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(404);

        $this->assertFalse($concert->hasOrderFor('johndoe@example.com'));

        $this->assertEquals(0, $this->paymentGateway->totalCharged());
    }

    /** @test * */
    function cannot_purchase_more_tickets_that_remained()
    {
        $this->disableExceptionHandling();
        $concert = factory(Concert::class)->state('published')->create()->addTickets(50);

        $response = $this->buyTickets($concert, [
            'email'   => 'johndoe@example.com',
            'tickets' => 51,
            'token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(422);

        $this->assertFalse($concert->hasOrderFor('johndoe@example.com'));

        $this->assertEquals(0, $this->paymentGateway->totalCharged());
        $this->assertEquals(50, $concert->remainingTickets());
    }

    /** @test * */
    function cannot_purchase_tickets_that_already_are_reserved_by_other_person()
    {
        $concert = factory(Concert::class)->state('published')->create(['price' => 1200])->addTickets(3);

        $this->paymentGateway->beforeCharge(function ($paymentGateway) use($concert){
            $response = $this->buyTickets($concert, [
                'email'   => 'personB@mail.com',
                'tickets' => 3,
                'token'   => $paymentGateway->getValidTestToken(),
            ]);

            $response->assertStatus(422);
            $this->assertFalse($concert->hasOrderFor('personB@mail.com'));
            $this->assertEquals(0, $this->paymentGateway->totalCharged());
        });

        $this->buyTickets($concert, [
            'email'   => 'personA@mail.com',
            'tickets' => 3,
            'token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertEquals(3600, $this->paymentGateway->totalCharged());

        $this->assertTrue($concert->hasOrderFor('personA@mail.com'));
        $this->assertEquals(3, $concert->ordersFor('personA@mail.com')->first()->ticketCount());
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
        $this->disableExceptionHandling();
        $concert = factory(Concert::class)->state('published')->create()->addTickets(2);

        $response = $this->buyTickets($concert, [
            'email'   => 'john@doe.com',
            'tickets' => 2,
            'token'   => 'invalid-token',
        ]);

        $response->assertStatus(422);

        $this->assertFalse($concert->hasOrderFor('john@doe.com'));
    }
}
