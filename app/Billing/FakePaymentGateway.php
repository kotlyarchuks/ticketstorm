<?php

namespace App\Billing;

class FakePaymentGateway implements PaymentGateway {

    private $charges;
    private $beforeChargeCallback;
    /**
     * FakePaymentGateway constructor.
     */
    public function __construct()
    {
        $this->charges = collect();
    }

    public function getToken()
    {
        return 'valid-token';
    }

    public function totalCharged()
    {
        return $this->charges->sum();
    }

    public function newChargesDuring($callback)
    {
        $oldChargesCount = $this->charges->count();
        $callback();
        return $this->charges->slice($oldChargesCount)->values();
    }

    public function charge($amount, $token)
    {
        if ($this->beforeChargeCallback !== null) {
            $callback = $this->beforeChargeCallback;
            $this->beforeChargeCallback = null;
            $callback($this);
        }

        if ($token !== $this->getToken()){
            throw new FailedPaymentException;
        }
        $this->charges[] = $amount;
    }

    public function beforeCharge($callback)
    {
        $this->beforeChargeCallback = $callback;
    }
}