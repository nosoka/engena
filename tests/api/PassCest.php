<?php

class PassCest
{
    protected $token;

    public function _before(ApiTester $I, AuthCest $auth)
    {
        $auth->_createToken($I);
        $this->token = $auth->_getToken();
    }

    public function testAccess(ApiTester $I)
    {
        $I->wantTo('test access to /passes');
        $I->deleteHeader('Authorization');

        // test access
        $I->sendGET('/passes');
        $I->seeResponseCodeIs(401);
        $I->sendGET('/passes/1');
        $I->seeResponseCodeIs(401);
    }

    public function testAll(ApiTester $I)
    {
        $I->wantTo('test data from /passes');
        $I->amBearerAuthenticated($this->token);

        // test data for all
        $I->sendGET('/passes');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertGreaterThan(1,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['id' => 'integer'], '$.data[*]');
    }

    public function testSingle(ApiTester $I)
    {
        $I->wantTo('test data from /passes/id');
        $I->amBearerAuthenticated($this->token);

        // test data for single
        $I->sendGET('/passes/184');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['id' => 'integer']);

        // test wrong id
        $I->sendGET('/passes/wrong');
        $I->seeResponseCodeIs(404);
    }

    public function testIncludes(ApiTester $I)
    {
        $I->wantTo('test includes for /passes');
        $I->amBearerAuthenticated($this->token);

        // test includes for all
        $I->sendGET('/passes?include=reservePass');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['id' => 'integer'], '$.data[*]');
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer'], '$..reservePass.reserve');

        // test includes for single
        $I->sendGET('/passes/41?include=reservePass');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['id' => 'integer']);
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer'], '$..reservePass.reserve');

        // test wrong includes
        $I->sendGET('/passes/41?include=region,passes,trails');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['id' => 'integer']);
        $I->dontSeeResponseContains('region');
        $I->dontSeeResponseContains('passes');
        $I->dontSeeResponseContains('trails');
    }

    public function testFilters(ApiTester $I)
    {
        $I->wantTo('test filters for /passes');
        $I->amBearerAuthenticated($this->token);

        // test filter - id
        $I->sendGET('/passes?id=41,42');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEquals(2,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['id' => 'integer'], '$.data[*]');

        // test filter - missing photos
        $I->sendGET('/passes?photo=null');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertGreaterThanOrEqual(1,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['id' => 'integer'], '$.data[*]');

        // test filter - reserve
        $I->sendGET('/passes?reserve=2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertGreaterThanOrEqual(1,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['id' => 'integer'], '$.data[*]');

        // test wrong data
        $I->sendGET('/passes?id=wrong');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEmpty($I->grabDataFromResponseByJsonPath('data')[0]);
    }

    // public function testUploadPhoto(ApiTester $I)
}
