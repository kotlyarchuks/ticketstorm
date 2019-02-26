<?php

namespace App\Http\Controllers;

use App\Billing\FailedPaymentException;
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
            'email'   => 'required|email',
            'tickets' => 'required|integer|gte:1',
            'token'   => 'required',
        ]);

        try {
            $this->gateway->charge(request('tickets') * $concert->price, request('token'));

            $order = $concert->buyTickets(request('email'), request('tickets'));
        } catch(FailedPaymentException $e){
            return response()->json([], 422);
        }


        return response()->json([], 201);
    }
}
