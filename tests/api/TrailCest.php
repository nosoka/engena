<?php

class TrailCest
{
    protected $token;

    public function _before(ApiTester $I, AuthCest $auth)
    {
        $auth->_createToken($I);
        $this->token = $auth->_getToken();
    }

    public function testAccess(ApiTester $I)
    {
        $I->wantTo('test access to /trails');
        $I->deleteHeader('Authorization');

        // test access for all
        $I->sendGET('/trails');
        $I->seeResponseCodeIs(401);

        // test access for single
        $I->sendGET('/trails/1');
        $I->seeResponseCodeIs(401);
    }

    public function testAll(ApiTester $I)
    {
        $I->wantTo('test data from /trails');
        $I->amBearerAuthenticated($this->token);

        // test data for all
        $I->sendGET('/trails');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertGreaterThanOrEqual(1,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['trailId' => 'integer'], '$.data[*]');
    }

    public function testSingle(ApiTester $I)
    {
        $I->wantTo('test data from /trails/id');
        $I->amBearerAuthenticated($this->token);

        // test data for single
        $I->sendGET('/trails/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['trailId' => 'integer']);

        // test wrong id
        $I->sendGET('/trails/wrong');
        $I->seeResponseCodeIs(404);
    }

    public function testFilters(ApiTester $I)
    {
        $I->wantTo('test filters for /trails');
        $I->amBearerAuthenticated($this->token);

        // test filter - id,name,reserve, activity
        $I->sendGET('/trails?id=1,2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEquals(2,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['trailId' => 'integer'], '$.data[*]');

        // test filter - name
        $I->sendGET('/trails?name=Canaries,Middelvlei Plaas');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEquals(2,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['trailId' => 'integer'], '$.data[*]');

        // test filter - reserve
        $I->sendGET('/trails?reserve=1,2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertGreaterThanOrEqual(1,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['trailId' => 'integer'], '$.data[*]');

        // test filter - activity
        $I->sendGET('/trails?activity=1,2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertGreaterThanOrEqual(1,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['trailId' => 'integer'], '$.data[*]');

        // test wrong data
        $I->sendGET('/trails?id=wrong');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEmpty($I->grabDataFromResponseByJsonPath('data')[0]);
    }
}
