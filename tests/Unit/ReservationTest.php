<?php

namespace Tests\Unit;

use App\Concert;
use App\Reservation;

use App\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReservationTest extends TestCase {

    use DatabaseMigrations;

    /** @test * */
    function can_calculate_total_amount_for_reservation()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'test@test.com');

        $this->assertEquals(3600, $reservation->totalSum());
    }

    /** @test * */
    function can_retrieve_tickets_from_reservation()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'test@test.com');

        $tickets_retrieved = $reservation->getTickets();

        $this->assertEquals($tickets, $tickets_retrieved);
    }

    /** @test * */
    function can_retrieve_email_from_reservation()
    {
        $tickets = collect([
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'denis@example.com');

        $this->assertEquals('denis@example.com', $reservation->getEmail());
    }

    /** @test * */
    function can_create_order_by_completing_reservation()
    {
        $concert = factory(Concert::class)->create(['price' => 1200]);
        $tickets = factory(Ticket::class, 3)->create(['concert_id' => $concert->id]);

        $reservation = new Reservation($tickets, 'denis@example.com');

        $order = $reservation->complete();

        $this->assertEquals('denis@example.com', $order->email);
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(3, $order->ticketCount());
    }

    /** @test * */
    function reservation_can_be_cancelled()
    {
        $tickets = collect([
            \Mockery::spy(Ticket::class),
            \Mockery::spy(Ticket::class),
            \Mockery::spy(Ticket::class)
        ]);

        $reservation = new Reservation($tickets, 'test@test.com');
        $reservation->cancel();

        foreach ($tickets as $ticket){
            $ticket->shouldHaveReceived('release');
        }
    }
}
