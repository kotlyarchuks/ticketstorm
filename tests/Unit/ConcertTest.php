<?php

namespace Tests\Unit;

use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test * */
    function can_get_formatted_date()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2002-01-13 8pm')
        ]);

        $date = $concert->formatted_date;
        $this->assertEquals('January 13, 2002', $date);
    }

    /** @test * */
    function can_get_formatted_time()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2002-01-13 20:00')
        ]);

        $time = $concert->formatted_time;
        $this->assertEquals('8:00pm', $time);
    }

    /** @test * */
    function can_get_formatted_price()
    {
        $concert = factory(Concert::class)->make([
            'price' => 1999
        ]);

        $price = $concert->formatted_price;
        $this->assertEquals('19.99', $price);
    }

    /** @test * */
    function concerts_at_published_date_are_published()
    {
        $publishedConcertA = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);
        $publishedConcertB = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);
        $unpublishedConcert = factory(Concert::class)->create(['published_at' => null]);

        $concerts = Concert::published()->get();

        $this->assertTrue($concerts->contains($publishedConcertA));
        $this->assertTrue($concerts->contains($publishedConcertB));
        $this->assertFalse($concerts->contains($unpublishedConcert));
    }

    /** @test * */
    function can_buy_tickets()
    {
        $concert = factory(Concert::class)->create()->addTickets(5);

        $order = $concert->buyTickets('denis@example.com', 3);

        $this->assertEquals('denis@example.com', $order->email);
        $this->assertEquals(3, $order->ticketCount());
    }

    /** @test * */
    function can_add_tickets_to_concert()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(50);

        $this->assertEquals(50, $concert->remainingTickets());
    }

    /** @test * */
    function remaining_tickets_dont_include_tickets_associated_with_orders()
    {
        $concert = factory(Concert::class)->create()->addTickets(50);

        $concert->buyTickets('denis@example.com', 30);

        $this->assertEquals(20, $concert->remainingTickets());
    }

    /** @test * */
    function buying_more_tickets_than_remaining_throws_exception()
    {
        $concert = factory(Concert::class)->create()->addTickets(10);

        try {
            $concert->buyTickets('denis@example.com', 11);
        } catch(NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('denis@example.com'));
            $this->assertEquals(10, $concert->remainingTickets());
            return;
        }

        $this->fail('Tickets were purchased over the limit');
    }

    /** @test * */
    function cannot_order_tickets_that_already_ordered()
    {
        $concert = factory(Concert::class)->create()->addTickets(10);

        $concert->buyTickets('denis@example.com', 6);
        try {
            $concert->buyTickets('sasha@example.com', 5);
        } catch(NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('sasha@example.com'));
            $this->assertEquals(4, $concert->remainingTickets());
            return;
        }

        $this->fail("Tickets were purchased over the limit");
    }
}
