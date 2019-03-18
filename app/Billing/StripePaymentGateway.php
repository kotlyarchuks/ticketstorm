<?php

namespace App\Billing;

class StripePaymentGateway implements PaymentGateway {

    public function charge($amount, $token)
    {
        return \Stripe\Charge::create([
            "amount"      => $amount,
            "currency"    => "usd",
            "source"      => $token,
            "description" => "Test charge"
        ],
            ['api_key' => config('services.stripe.secret')]);
    }
}