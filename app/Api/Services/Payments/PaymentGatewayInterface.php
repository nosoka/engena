<?php

namespace App\Api\Services\Payments;

interface PaymentGatewayInterface
{
    public function setupConfig();

    public function generateClientToken();

    public function processPayment();
}
