<?php

namespace Tests\Unit;

use App\Reservation;

use Tests\TestCase;

class ReservationTest extends TestCase {

    /** @test * */
    function can_calculate_total_amount_for_reservation()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalSum());
    }

    /** @test * */
    function reservation_can_be_cancelled()
    {
        $tickets = collect([
            \Mockery::spy(Ticket::class),
            \Mockery::spy(Ticket::class),
            \Mockery::spy(Ticket::class)
        ]);

        $reservation = new Reservation($tickets);
        $reservation->cancel();

        foreach ($tickets as $ticket){
            $ticket->shouldHaveReceived('release');
        }
    }
}
