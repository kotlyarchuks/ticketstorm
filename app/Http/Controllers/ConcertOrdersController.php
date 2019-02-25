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
        $this->validate(request(), [
            'email'   => 'required',
            'tickets' => 'required|gte:1'
        ]);

        $this->gateway->charge(request('tickets') * $concert->price, request('token'));

        $order = $concert->buyTickets(request('email'), request('tickets'));

        return response()->json([], 201);
    }
}
