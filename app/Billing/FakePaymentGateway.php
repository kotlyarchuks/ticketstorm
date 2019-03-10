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

    public function getValidTestToken()
    {
        return 'valid-token';
    }

    public function totalCharged()
    {
        return $this->charges->sum();
    }

    public function charge($amount, $token)
    {
        if ($this->beforeChargeCallback !== null) {
            $this->beforeChargeCallback->__invoke($this);
        }

        if ($token !== $this->getValidTestToken()){
            throw new FailedPaymentException;
        }
        $this->charges[] = $amount;
    }

    public function beforeCharge($callback)
    {
        $this->beforeChargeCallback = $callback;
    }
}