<?php

namespace Tests\Unit;

use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test * */
    function order_can_be_cancelled()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(5);

        $order = $concert->buyTickets('denis@example.com', 3);
        $order->cancel();

        $this->assertNull($concert->orders()->where('email', 'denis@example.com')->first());
    }
}
