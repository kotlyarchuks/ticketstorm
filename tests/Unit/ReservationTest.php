<?php

namespace Tests\Unit;

use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use App\Order;
use App\Reservation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationTest extends TestCase {

    use DatabaseMigrations;

    /** @test * */
    function can_calculate_total_amount_for_reservation()
    {
        $concert = factory(Concert::class)->create(['price' => 2500])->addTickets(5);
        $this->assertEquals(5, $concert->remainingTickets());

        $reservation = new Reservation($concert->findTickets(3));

        $this->assertEquals(7500, $reservation->totalSum());
    }
}
