<?php

class ActivityCest
{
    protected $token;

    public function _before(ApiTester $I, AuthCest $auth)
    {
        $auth->_createToken($I);
        $this->token = $auth->_getToken();
    }

    public function testAccess(ApiTester $I)
    {
        $I->wantTo('test access to /activities');
        $I->deleteHeader('Authorization');

        // test access is restricted
        $I->sendGET('/activities');
        $I->seeResponseCodeIs(401);
        $I->sendGET('/activities/1');
        $I->seeResponseCodeIs(401);
    }

    public function testAll(ApiTester $I)
    {
        $I->wantTo('test data from /activities');
        $I->amBearerAuthenticated($this->token);

        // test data for all
        $I->sendGET('/activities');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertGreaterThanOrEqual(1,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['activityId' => 'integer'], '$.data[*]');
    }

    public function testSingle(ApiTester $I)
    {
        $I->wantTo('test data from /activities/id');
        $I->amBearerAuthenticated($this->token);

        // test data for single
        $I->sendGET('/activities/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['activityId' => 'integer']);

        // test wrong id
        $I->sendGET('/activities/wrong');
        $I->seeResponseCodeIs(404);
    }

    public function testIncludes(ApiTester $I)
    {
        $I->wantTo('test includes for /activities');
        $I->amBearerAuthenticated($this->token);

        // test includes for all
        $I->sendGET('/activities?include=reserves');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['activityId' => 'integer'], '$.data[*]');
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer'], '$..reserves.data[*]');

        // test includes for single
        $I->sendGET('/activities/1?include=reserves');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['activityId' => 'integer']);
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer'], '$..reserves.data[*]');

        // test wrong includes
        $I->sendGET('/activities/1?include=region,activities,trails');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['activityId' => 'integer']);
        $I->dontSeeResponseContains('region');
        $I->dontSeeResponseContains('activities');
        $I->dontSeeResponseContains('trails');
    }

    public function testFilters(ApiTester $I)
    {
        // test filter - id
        $I->wantTo('test filters for /activities');
        $I->amBearerAuthenticated($this->token);

        $I->sendGET('/activities?id=1,2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEquals(2,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['activityId' => 'integer'], '$.data[*]');

        // test filter - name
        $I->sendGET('/activities?name=bike,hike');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEquals(2,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['activityId' => 'integer'], '$.data[*]');

        // test wrong data
        $I->sendGET('/activities?id=wrong');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEmpty($I->grabDataFromResponseByJsonPath('data')[0]);
    }
}
