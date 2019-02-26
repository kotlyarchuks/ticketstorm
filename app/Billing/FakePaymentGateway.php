<?php

namespace App\Billing;

class FakePaymentGateway implements PaymentGateway {


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
        if ($token !== $this->getValidTestToken()){
            throw new FailedPaymentException;
        }
        $this->charges[] = $amount;
    }
}