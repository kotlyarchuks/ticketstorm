<?php

namespace App;

class Reservation
{

    private $tickets;
    /**
     * Reservation constructor.
     *
     * @param array $tickets
     */
    public function __construct($tickets = [])
    {
        $this->tickets = $tickets;
    }

    public function totalSum()
    {
        return $this->tickets->sum('price');
    }
}
