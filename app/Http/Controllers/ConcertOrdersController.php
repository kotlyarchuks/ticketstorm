<?php

namespace App\Http\Controllers;

use App\Billing\FailedPaymentException;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
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
            $amount = request('tickets') * $concert->price;
            $order = $concert->buyTickets(request('email'), request('tickets'));
            $this->gateway->charge($amount, request('token'));
        } catch (FailedPaymentException $e)
        {
            $order->cancel();

            return response()->json([], 422);
        } catch (NotEnoughTicketsException $e)
        {
            return response()->json([], 422);
        }

        return response()->json($order->toArray(), 201);
    }
}
