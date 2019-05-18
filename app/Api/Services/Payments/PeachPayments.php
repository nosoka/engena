<?php

namespace App\Api\Services\Payments;

use Dingo\Api\Routing\Helpers;
use GuzzleHttp\Client;
use Log;

class PeachPayments
{
    use Helpers;

    public function __construct()
    {
        $this->setupClient();
    }

    public function setupClient()
    {
        $this->client = new Client([
            'base_uri' => env('PEACH_BASEURL', ''),
            'query'    => [
                'authentication.userId'   => env('PEACH_USERID', ''),
                'authentication.password' => env('PEACH_PASSWORD', ''),
                'authentication.entityId' => env('PEACH_ENTITYID', ''),
            ]
        ]);
    }

    public function generateCheckoutId($data = array())
    {
        // $data['customer.surname']      = $this->auth->user()->Surname;
        $data['customer.givenName']    = $this->auth->user()->full_name;
        $data['customer.email']        = $this->auth->user()->Email;
        $data['customer.mobile']       = $this->auth->user()->Mobile;
        $data['currency']              = env('PEACH_CURRENCY', '');
        $data['paymentType']           = env('PEACH_PAYMENT_TYPE', '');
        $data['merchantTransactionId'] = md5(time() . $this->user->Email . rand());

        $queryData    = array_merge($this->client->getConfig('query'), $data);
        $response     = $this->client->post('checkouts', ['query' => $queryData]);
        $jsonResponse = json_decode($response->getBody());

        if ($jsonResponse->result->code == '000.200.100') {
            Log::info("Created CheckoutId: {$jsonResponse->id}");
            return $jsonResponse->id;
        }

        return false;
    }

    public function processPayment($checkoutId = '')
    {
        $response     = $this->client->get("checkouts/{$checkoutId}/payment");
        $jsonResponse = json_decode($response->getBody());

        if (preg_match('/^(000\.000\.|000\.100\.1|000\.[36])/', $jsonResponse->result->code)) {
            Log::info("PaymentId: {$jsonResponse->id} Code: {$jsonResponse->result->code} Message: {$jsonResponse->result->description}");
            return $jsonResponse;
        }

        Log::error("PaymentId: {$jsonResponse->id} Code: {$jsonResponse->result->code} Message: {$jsonResponse->result->description}");
        return false;
    }
}
