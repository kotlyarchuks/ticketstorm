<?php

namespace Tests\Unit;

use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase {

    use DatabaseMigrations;

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
