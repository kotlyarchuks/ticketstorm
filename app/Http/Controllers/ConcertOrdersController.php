<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Http\Request;

class ConcertOrdersController extends Controller {


    /**
     * ConcertOrdersController constructor.
     *
     * @param PaymentGateway $gateway
     */
    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function store(Concert $concert)
    {
        $token = request('token');
        $tickets = request('tickets');
        $amount = $tickets * $concert->price;
        $this->gateway->charge($amount, $token);

        $concert->orders()->create([
            'email'   => request('email'),
            'tickets' => $tickets,
            'token'   => $token
        ]);

        return response()->json([], 201);
    }
}
