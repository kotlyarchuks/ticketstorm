<?php

namespace Tests\Unit;

use App\Billing\FailedPaymentException;
use App\Billing\FakePaymentGateway;
use App\Billing\StripePaymentGateway;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase {

    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
        $this->lastCharge = $this->lastCharge();
    }

    private function lastCharge()
    {
        return \Stripe\Charge::all(['limit' => 1],
            ['api_key' => config('services.stripe.secret')])
            ->data[0];
    }

    private function getToken()
    {
        return \Stripe\Token::create([
            "card" => [
                "number"    => "4242424242424242",
                "exp_month" => 1,
                "exp_year"  => date('Y') + 1,
                "cvc"       => "123"
            ]
        ],
            ['api_key' => config('services.stripe.secret')]
        )->id;
    }

    private function newCharges()
    {
        return \Stripe\Charge::all(['limit' => 1, 'ending_before' => $this->lastCharge->id],
            ['api_key' => config('services.stripe.secret')])
            ->data;
    }

    /** @test * */
    function purchases_with_valid_token_are_successful()
    {
        //create payment gateway
        $gateway = new StripePaymentGateway(config('services.stripe.secret'));
        //charge
        $gateway->charge(2400, $this->getToken());
        //assert that charge was successful
        $this->assertCount(1, $this->newCharges());
        $this->assertEquals(2400, $this->lastCharge()->amount);
    }

    /** @test * */
    function purchases_with_invalid_token_fail()
    {
        $gateway = new StripePaymentGateway(config('services.stripe.secret'));
        try
        {
            $gateway->charge(2400, 'invalid-token');
        } catch (FailedPaymentException $e)
        {
            $this->assertCount(0, $this->newCharges());
            return;
        }

        $this->fail();
    }
}
