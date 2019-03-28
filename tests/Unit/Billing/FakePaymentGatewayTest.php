<?php

namespace Tests\Unit;

use App\Billing\FailedPaymentException;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase {

    use DatabaseMigrations;

    protected function getPaymentGateway()
    {
        return new FakePaymentGateway;
    }

    /** @test * */
    function can_fetch_charges_that_were_made_during_callback()
    {
        $gateway = $this->getPaymentGateway();
        $gateway->charge(1000, $gateway->getToken());
        $gateway->charge(2000, $gateway->getToken());

        $newCharges = $gateway->newChargesDuring(function() use($gateway){
            $gateway->charge(1200, $gateway->getToken());
            $gateway->charge(1500, $gateway->getToken());
        });

        $this->assertCount(2, $newCharges);
        $this->assertEquals([1200, 1500], $newCharges->all());
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
        $gateway = new FakePaymentGateway;
        try
        {
            $gateway->charge(2400, 'invalid-token');
        } catch (FailedPaymentException $e)
        {
            $this->assertTrue(true);
            return;
        }

        $this->fail();
    }

    /** @test * */
    function can_set_callback_to_use_before_charge()
    {
        $gateway = new FakePaymentGateway;
        $callbackRan = false;

        $gateway->beforeCharge(function($gateway) use(&$callbackRan){
            $callbackRan = true;
            $gateway->charge(2500, $gateway->getValidTestToken());

            $this->assertEquals(2500, $gateway->totalCharged());
        });

        $gateway->charge(2400, $gateway->getValidTestToken());

        $this->assertTrue($callbackRan);
        $this->assertEquals(4900, $gateway->totalCharged());
    }
}
