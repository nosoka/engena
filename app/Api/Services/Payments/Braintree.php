<?php

namespace App\Api\Services\Payments;

use Braintree\Exception\Configuration as ConfigurationException;
use Braintree\Gateway;
use Dingo\Api\Exception\ResourceException;
use Log;

class Braintree implements PaymentGatewayInterface
{
    protected $configuration;
    public $errors;

    public function __construct()
    {
        $this->setupConfig();
        $this->setupGateway();
    }

    public function setupConfig()
    {
        $this->configuration = [
            'environment' => env('BRAINTREE_ENVIRONMENT'),
            'merchantId'  => env('BRAINTREE_MERCHANTID'),
            'publicKey'   => env('BRAINTREE_PUBLICKEY'),
            'privateKey'  => env('BRAINTREE_PRIVATEKEY'),
        ];
    }

    public function setupGateway()
    {
        try {
            $this->gateway = new Gateway($this->configuration);
            $this->gateway->config->assertHasAccessTokenOrKeys();
        } catch (ConfigurationException $e) {
            throw new ResourceException('Unable to create payment gateway.');
        }
    }

    public function generateClientToken()
    {
        return $this->gateway->clientToken()->generate();
    }

    public function processPayment($data = array())
    {
        $result = $this->gateway->transaction()->sale([
            'amount'             => (float) $data['amount'],
            'paymentMethodNonce' => $data['nonce'],
            'options'            => ['submitForSettlement' => true],
        ]);

        if (!$result->success) {
            // TODO:: failing on a customer who is willing to pay isn't nice
            // email administrator and log the errors along with other information in the request
            // ex: logged in user details and the pass info that is being purchased
            foreach ($result->errors->deepAll() as $error) {
                Log::error($error->code . ": " . $error->message);
            }

            return false;
        }

        return $result->transaction->id;
    }
}
