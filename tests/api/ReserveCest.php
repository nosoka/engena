<?php

class ReserveCest
{
    protected $token;

    public function _before(ApiTester $I, AuthCest $auth)
    {
        $auth->_createToken($I);
        $this->token = $auth->_getToken();
    }

    public function testAccess(ApiTester $I)
    {
        $I->wantTo('test access to /reserves');
        $I->deleteHeader('Authorization');

        // test access for all
        $I->sendGET('/reserves');
        $I->seeResponseCodeIs(401);

        // test access for single
        $I->sendGET('/reserves/1');
        $I->seeResponseCodeIs(401);
    }

    public function testAll(ApiTester $I)
    {
        $I->wantTo('test data from /reserves');
        $I->amBearerAuthenticated($this->token);

        // test data for all
        $I->sendGET('/reserves');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertGreaterThanOrEqual(1,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer'], '$.data[*]');
    }

    public function testSingle(ApiTester $I)
    {
        $I->wantTo('test data from /reserves/id');
        $I->amBearerAuthenticated($this->token);

        // test data for single
        $I->sendGET('/reserves/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer']);

        // test wrong id
        $I->sendGET('/reserves/wrong');
        $I->seeResponseCodeIs(404);
    }

    public function testIncludes(ApiTester $I)
    {
        $I->wantTo('test includes for /reserves');
        $I->amBearerAuthenticated($this->token);

        // test includes for all
        $I->sendGET('/reserves?include=region,trails,entrances');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer'], '$.data[*]');
        $I->seeResponseMatchesJsonType(['regionId' => 'integer'], '$.data[*].region');
        $I->seeResponseMatchesJsonType(['trailId' => 'integer'], '$..trails.data[*]');
        $I->seeResponseMatchesJsonType(['entranceId' => 'integer'], '$..entrances.data[*]');

        // test includes for single
        $I->sendGET('/reserves/1?include=region,trails,entrances');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer']);
        $I->seeResponseMatchesJsonType(['regionId' => 'integer'], '$.region');
        $I->seeResponseMatchesJsonType(['trailId' => 'integer'], '$..trails.data[*]');
        $I->seeResponseMatchesJsonType(['entranceId' => 'integer'], '$..entrances.data[*]');

        // test wrong includes
        $I->sendGET('/reserves/1?include=activities,wrong');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer']);
        $I->dontSeeResponseContains('activities');
        $I->dontSeeResponseContains('wrong');
    }

    public function testFilters(ApiTester $I)
    {
        // test filter - id  'id' => 'ID', 'name' => 'ReserveName',  'region' => 'RegionID'
        $I->wantTo('test filters for /reserves');
        $I->amBearerAuthenticated($this->token);

        $I->sendGET('/reserves?id=1,2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEquals(2,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer'], '$.data[*]');

        // test filter - name
        $I->sendGET('/reserves?name=Jonkershoek,Bottelary');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEquals(2,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer'], '$.data[*]');

        $I->sendGET('/reserves?region=1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertGreaterThenOrEqual(1,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer'], '$.data[*]');

        // test wrong data
        $I->sendGET('/reserves?id=wrong');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEmpty($I->grabDataFromResponseByJsonPath('data')[0]);
    }
}
