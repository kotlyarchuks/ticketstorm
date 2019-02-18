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
        //Arrange
        $concert = factory(Concert::class)->create([
            'date' => Carbon::parse('2002-01-13 8pm')
        ]);

        //Assert
        $date = $concert->formatted_date;
        $this->assertEquals('January 13, 2002', $date);
    }

    /** @test * */
    function can_get_formatted_time()
    {
        //Arrange
        $concert = factory(Concert::class)->create([
            'date' => Carbon::parse('2002-01-13 20:00')
        ]);

        //Assert
        $time = $concert->formatted_time;
        $this->assertEquals('8:00pm', $time);
    }

    /** @test * */
    function can_get_formatted_price()
    {
        //Arrange
        $concert = factory(Concert::class)->create([
            'price' => 1999
        ]);
        //Act
        //Assert
        $price = $concert->formatted_price;
        $this->assertEquals('19.99', $price);
    }
}
