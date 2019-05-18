<?php


class RegionCest
{
    public function _before(ApiTester $I, AuthCest $auth)
    {
        $auth->_createToken($I);
        $this->token = $auth->_getToken();
    }

    public function testAccess(ApiTester $I)
    {
        $I->wantTo('test access to /regions');
        $I->deleteHeader('Authorization');

        // test access
        $I->sendGET('/regions');
        $I->seeResponseCodeIs(401);
    }

    public function testAll(ApiTester $I)
    {
        $I->wantTo('test data from /regions');
        $I->amBearerAuthenticated($this->token);

        // test data for all
        $I->sendGET('/regions');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertGreaterThanOrEqual(1,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['regionId' => 'integer'], '$.data[*]');
    }

    public function testIncludes(ApiTester $I)
    {
        $I->wantTo('test includes for /regions');
        $I->amBearerAuthenticated($this->token);

        // test includes
        $I->sendGET('/regions?include=reserves');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['regionId' => 'integer'], '$.data[*]');
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer'], '$..reserves.data[*]');

        // test wrong includes
        $I->sendGET('/regions?include=trails,activities');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['regionId' => 'integer'], '$.data[*]');
        $I->dontSeeResponseContains('trails');
        $I->dontSeeResponseContains('activities');
    }

    public function testFilters(ApiTester $I)
    {
        // test filter - id
        $I->wantTo('test filters for /regions');
        $I->amBearerAuthenticated($this->token);

        $I->sendGET('/regions?id=1,2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEquals(2,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['regionId' => 'integer'], '$.data[*]');

        // test filter - name
        $I->sendGET('/regions?name=Stellenbosch,Durbanville');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEquals(2,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['regionId' => 'integer'], '$.data[*]');

        // test wrong data
        $I->sendGET('/regions?id=wrong');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEmpty($I->grabDataFromResponseByJsonPath('data')[0]);
    }
}
