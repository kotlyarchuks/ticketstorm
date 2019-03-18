<?php

namespace App;

use App\Billing\FailedPaymentException;

class Reservation
{

    private $tickets;
    private $email;
    /**
     * Reservation constructor.
     *
     * @param array $tickets
     */
    public function __construct($tickets, $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    /**
     * @return array
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function totalSum()
    {
        return $this->tickets->sum('price');
    }

    public function complete($paymentGateway, $paymentToken)
    {
        $paymentGateway->charge($this->totalSum(), $paymentToken);
        return Order::fromReservation($this->getEmail(), $this->totalSum(), $this->getTickets());
    }

    public function cancel()
    {
        $this->tickets->each(function($ticket){
            $ticket->release();
        });
    }
}
