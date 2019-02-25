<?php

namespace Tests\Unit;

use App\Concert;
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
        $concert = factory(Concert::class)->create();

        $order = $concert->buyTickets('denis@example.com', 3);

        $this->assertEquals('denis@example.com', $order->email);
        $this->assertEquals(3, $order->tickets()->count());
    }
}
