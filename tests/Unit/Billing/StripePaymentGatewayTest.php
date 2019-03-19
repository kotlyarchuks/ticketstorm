<?php

namespace Tests\Unit;

use App\Billing\FailedPaymentException;
use App\Billing\FakePaymentGateway;
use App\Billing\StripePaymentGateway;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase {

    use DatabaseMigrations;

    /** @test * */
    function purchases_with_valid_token_are_successful()
    {
        //create payment gateway
        $gateway = new StripePaymentGateway(config('services.stripe.secret'));

        $lastCharge = $charge = \Stripe\Charge::all(['limit' => 1],
            ['api_key' => config('services.stripe.secret')])
            ->data[0];

        $token = \Stripe\Token::create([
            "card" => [
                "number" => "4242424242424242",
                "exp_month" => 1,
                "exp_year" => date('Y')+1,
                "cvc" => "123"
            ]
        ],
            ['api_key' => config('services.stripe.secret')]
        )->id;

        //charge
        $gateway->charge(2400, $token);

        $newCharge = $charge = \Stripe\Charge::all(['limit' => 1, 'ending_before' => $lastCharge->id],
            ['api_key' => config('services.stripe.secret')])
            ->data[0];

        //assert that charge was successful
        $this->assertEquals(2400, $newCharge->amount);
    }
}
