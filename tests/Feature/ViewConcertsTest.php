<?php

namespace Tests\Feature;

use App\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewConcertsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test * */
    function user_can_view_concert_listing()
    {
        //Arrange
        $concert = Concert::create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Lethargy',
            'date' => Carbon::parse('December 13, 2016 8pm'),
            'price' => 3250,
            'location' => 'The Mosh Pit',
            'street' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'additional_info' => 'For tickets, call (555) 555-5555'
        ]);
        //Act
        $response = $this->get('/concerts/'.$concert->id);
        //Assert
        $response->assertSee('The Red Chord');
        $response->assertSee('with Animosity and Lethargy');
        $response->assertSee('December 13, 2016');
        $response->assertSee('Doors at 8:00pm');
        $response->assertSee('32.50');
        $response->assertSee('The Mosh Pit');
        $response->assertSee('123 Example Lane');
        $response->assertSee('Laraville, ON 17916');
        $response->assertSee('For tickets, call (555) 555-5555');
    }
}
