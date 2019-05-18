<?php

class SubscriptionCest
{
    protected $token;

    public function _before(ApiTester $I, AuthCest $auth)
    {
        $auth->_createToken($I);
        $this->token = $auth->_getToken();
    }

    public function testAccess(ApiTester $I)
    {
        $I->wantTo('test access to /subscriptions');
        $I->deleteHeader('Authorization');

        // test access for all
        $I->sendGET('/subscriptions');
        $I->seeResponseCodeIs(401);
        $I->sendPOST('/subscriptions');
        $I->seeResponseCodeIs(401);
    }

    public function testAll(ApiTester $I)
    {
        $I->wantTo('test data from /subscriptions');
        $I->amBearerAuthenticated($this->token);

        // test data for all
        $I->sendGET('/subscriptions');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertGreaterThanOrEqual(1,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['monthlyFee' => 'integer:>0'], '$.data[*]');
    }

    // put this back on after automating fake data
    public function _testCreate(ApiTester $I)
    {
        $I->wantTo('test adding to /subscriptions');
        $I->amBearerAuthenticated($this->token);

        // test add
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST('/subscriptions', ['subscriptionId' => '1']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'success']);

        // test wrong data
        $I->sendPOST('/subscriptions', ['subscriptionId' => '1']); // already subscribed
        $I->seeResponseCodeIs(422);
        $I->sendPOST('/subscriptions', ['subscriptionId' => 'wrong']); // invalid subscription
        $I->seeResponseCodeIs(422);
    }
}
