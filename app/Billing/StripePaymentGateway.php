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

    public function getToken()
    {
        return \Stripe\Token::create([
            "card" => [
                "number"    => "4242424242424242",
                "exp_month" => 1,
                "exp_year"  => date('Y') + 1,
                "cvc"       => "123"
            ]
        ],
            ['api_key' => $this->apiKey]
        )->id;
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

    private function lastCharge()
    {
        return \Stripe\Charge::all(['limit' => 1],
            ['api_key' => $this->apiKey])
            ->data[0];
    }

    public function newChargesDuring($callback){
        $lastCharge = $this->lastCharge();
        $callback();
        $newCharges = $this->newChargesSince($lastCharge);
        return $newCharges->pluck('amount');
    }

    private function newChargesSince($lastCharge)
    {
        $newCharges = \Stripe\Charge::all(['limit' => 1, 'ending_before' => $lastCharge->id],
            ['api_key' => $this->apiKey])
            ->data;
        return collect($newCharges);
    }
}