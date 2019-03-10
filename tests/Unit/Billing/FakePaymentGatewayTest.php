<?php

namespace Tests\Unit;

use App\Billing\FailedPaymentException;
use App\Billing\FakePaymentGateway;
use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FakePaymentGatewayTest extends TestCase {

    use DatabaseMigrations;

    /** @test * */
    function purchases_with_valid_token_are_successful()
    {
        $gateway = new FakePaymentGateway;
        $gateway->charge(2400, $gateway->getValidTestToken());

        $this->assertEquals(2400, $gateway->totalCharged());
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
            $this->assertEquals(0, $gateway->totalCharged());
        });

        $gateway->charge(2400, $gateway->getValidTestToken());

        $this->assertTrue($callbackRan);
        $this->assertEquals(2400, $gateway->totalCharged());
    }
}
