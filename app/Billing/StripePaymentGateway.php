<?php

namespace App\Billing;

class StripePaymentGateway implements PaymentGateway {


    /**
     * StripePaymentGateway constructor.
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function charge($amount, $token)
    {
        try
        {
            \Stripe\Charge::create([
                "amount"      => $amount,
                "currency"    => "usd",
                "source"      => $token,
                "description" => "Test charge"
            ], $this->apiKey);
        } catch (\Stripe\Error\InvalidRequest $e){
            dd($e);
            throw new FailedPaymentException();
        }
    }
}