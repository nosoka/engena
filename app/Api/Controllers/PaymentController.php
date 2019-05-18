<?php

namespace App\Api\Controllers;

use App\Api\Repositories\UserPassRepository;
use App\Api\Services\Payments\PeachPayments;
use App\Api\Transformers\UserPassTransformer;
use App\Api\Requests\CreatePassesRequest;
use App\Api\Validators\CartValidator;
use League\Fractal\Resource\Collection;

/**
 * @Resource("Payments", uri="payments")
 */
class PaymentController extends BaseController
{
    public function __construct(PeachPayments $paymentGateway,
                                UserPassTransformer $transformer,
                                UserPassRepository $repo )
    {
        $this->repo           = $repo;
        $this->transformer    = $transformer;
        $this->paymentGateway = $paymentGateway;
        $this->request        = app('request');
    }

    /**
     * Get user payments
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://api.engena.co.za/api/payments",
     *     type: "GET",
     * });
     *
     * // Optional include - reserve
     * // Optional filters - id
     * // example: /payments?id=1,2&include=reserve
     * ```
     * @Get("")
     * @Response(200, body={"data":{{"passType":"DayPass","passId":42,"passAmount":"35","passDate":"2016-01-29","transactionDate":"2016-01-16","ownPass":true,"photoUrl":null,"photos":""},{"passType":"LongtermPass","passId":"32","passAmount":"250","passDate":"2017-01-13","transactionDate":"2016-01-13"}}})
     */
    public function index()
    {
        return $this->collection($this->repo->all(), $this->transformer);
    }

    public function getPasses()
    {
        return $this->collection($this->repo->all(), $this->transformer);
    }

    /**
     * Get payment gateway token
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://api.engena.co.za/api/payments/token?amount=<>",
     *     type: "GET",
     * });
     *
     * ```
     * @Get("token")
     * @Response(200, body="eyJ2ZXJzaW9uIjoyLCJhdXRob3JpemF0aW9uR.................", contentType="text/html")
     */
    public function getCheckoutId(CartValidator $validator)
    {
        $data = $this->request->all();
        if (!$checkoutId = $this->paymentGateway->generateCheckoutId($data)) {
            return $this->response->error('Unable to create checkout form. Please verify the details and retry', 422);
        }

        return response()->json(compact('checkoutId'));
    }

    /**
     * Process payment
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://api.engena.co.za/api/payments",
     *     type: "POST",
     *     data: {
     *         'checkoutId': 'xyz...',  //received from payment gateway
     *         'passes' : [
     *             { id: 1, quantity: 1, startDate: '2016-12-06' },
     *             { id: 2, quantity: 1, startDate: '2016-12-07' },
     *         ]
     *     },
     * });
     * ```
     * @Post("")
     * @Response(200, body={"data":{{"id":289,"status":"active","is_owner":true,"amount":"50.00","start_date":"Dec 6, 2016","end_date":"Dec 6, 2016","created_date":null},{"id":290,"status":"active","is_owner":true,"amount":"50.00","start_date":"Dec 7, 2016","end_date":"Dec 7, 2016","created_date":null}}})
     */
    public function processPayment(CreatePassesRequest $request)
    {
        $checkoutId = $this->request->get('checkoutId');
        if (!$payment = $this->paymentGateway->processPayment($checkoutId)) {
            return $this->response->error('Payment failed. Please verify the details and retry', 422);
        }

        $passes     = $this->repo->createPasses();
        if($passes->count() > 0) {
            return $this->collection($passes, $this->transformer);
        } else {
            return $this->response->error('Failed to create passes', 422);
        }
    }
}
