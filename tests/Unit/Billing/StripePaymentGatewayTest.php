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
//        $this->lastCharge = $this->lastCharge();
    }

    protected function getPaymentGateway()
    {
        return new StripePaymentGateway(config('services.stripe.secret'));
    }


    /** @test * */
    function purchases_with_valid_token_are_successful()
    {
        //create payment gateway
        $gateway = $this->getPaymentGateway();

        //charge
        $newCharges = $gateway->newChargesDuring(function() use($gateway){
            $gateway->charge(2400, $gateway->getToken());
        });

        //assert that charge was successful
        $this->assertCount(1, $newCharges);
        $this->assertEquals(2400, $newCharges->sum());
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
