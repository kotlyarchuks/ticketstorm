<?php

namespace App\Http\Controllers;

use App\Billing\FailedPaymentException;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use App\Order;
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

    public function store($concertId)
    {
        $concert = Concert::published()->findOrFail($concertId);

        $this->validate(request(), [
            'email'   => 'required|email',
            'tickets' => 'required|integer|gte:1',
            'token'   => 'required',
        ]);

        try
        {
            // Find tickets
            $tickets = $concert->findTickets(request('tickets'));
            // Charge
            $this->gateway->charge($tickets->sum('price'), request('token'));
            // Create order
            $order = Order::forTickets($tickets, request('email'), $tickets->sum('price'));

        } catch (FailedPaymentException | NotEnoughTicketsException $e)
        {
            return response()->json([], 422);
        }

        return response()->json($order->toArray(), 201);
    }
}
