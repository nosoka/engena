<?php

class PaymentCest
{
    protected $token;

    public function _before(ApiTester $I, AuthCest $auth)
    {
        $auth->_createToken($I);
        $this->token = $auth->_getToken();
    }

    public function testAccess(ApiTester $I)
    {
        $I->wantTo('test access to /payments');
        $I->deleteHeader('Authorization');

        // test access for all
        $I->sendGET('/payments');
        $I->seeResponseCodeIs(401);
        $I->sendPOST('/payments');
        $I->seeResponseCodeIs(401);
        $I->sendGET('/payments/token');
        $I->seeResponseCodeIs(401);
    }

    public function testAll(ApiTester $I)
    {
        $I->wantTo('test data from /payments');
        $I->amBearerAuthenticated($this->token);

        // test data for all
        $I->sendGET('/payments');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertGreaterThanOrEqual(1,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['id' => 'integer'], '$.data[*]');
    }

    public function __oldBraintreeToken(ApiTester $I)
    {
        $I->wantTo('test braintree token from /payments/token');
        $I->amBearerAuthenticated($this->token);

        $I->sendGET('/payments/token');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['token' => 'string']);
    }

    // public function testPayment();
}
