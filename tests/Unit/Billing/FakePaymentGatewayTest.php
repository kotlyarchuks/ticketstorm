<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FakePaymentGatewayTest extends TestCase
{
    use DatabaseMigrations;

    /** @test * */
    function purchases_with_valid_token_are_successful()
    {
        $gateway = new FakePaymentGateway;
        $gateway->charge(2400, $gateway->getValidTestToken());

        $this->assertEquals(2400, $gateway->totalCharged());
    }
}
