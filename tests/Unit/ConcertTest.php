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
}
