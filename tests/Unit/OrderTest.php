<?php

namespace Tests\Unit;

use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use App\Order;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase {

    use DatabaseMigrations;

    /** @test * */
    function can_generate_order_for_tickets_email_and_amount()
    {
        $concert = factory(Concert::class)->create(['price' => 2500])->addTickets(5);
        $this->assertEquals(5, $concert->remainingTickets());

        $order = Order::forTickets($concert->findTickets(3), 'denis@example.com', 7500);

        $this->assertEquals('denis@example.com', $order->email);
        $this->assertEquals(3, $order->ticketCount());
        $this->assertEquals(7500, $order->amount);
        $this->assertEquals(2, $concert->remainingTickets());
    }

    /** @test * */
    function converting_to_array()
    {
        $concert = factory(Concert::class)->create(['price' => 2500])->addTickets(3);
        $order = $concert->buyTickets('denis@example.com', 3);

        $result = $order->toArray();

        $this->assertEquals([
            'email'   => 'denis@example.com',
            'tickets' => 3,
            'amount'  => 7500
        ], $result);
    }

    /** @test * */
    function order_can_be_cancelled()
    {
        $concert = factory(Concert::class)->create()->addTickets(5);
        $order = $concert->buyTickets('denis@example.com', 3);

        $order->cancel();

        $this->assertFalse($concert->hasOrderFor('denis@example.com'));
    }

    /** @test * */
    function tickets_are_released_if_payment_fails()
    {
        $concert = factory(Concert::class)->create()->addTickets(5);

        $order = $concert->buyTickets('denis@example.com', 3);
        $this->assertEquals(2, $concert->remainingTickets());

        $order->cancel();
        $this->assertEquals(5, $concert->remainingTickets());
    }
}
