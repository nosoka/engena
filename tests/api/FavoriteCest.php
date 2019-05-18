<?php

class FavoriteCest
{
    protected $token;

    public function _before(ApiTester $I, AuthCest $auth)
    {
        $auth->_createToken($I);
        $this->token = $auth->_getToken();
    }

    public function testAccess(ApiTester $I)
    {
        $I->wantTo('test access to /favorites');
        $I->deleteHeader('Authorization');

        // test access for all
        $I->sendGET('/favorites');
        $I->seeResponseCodeIs(401);
        $I->sendPOST('/favorites');
        $I->seeResponseCodeIs(401);
        $I->sendDELETE('/favorites');
        $I->seeResponseCodeIs(401);
    }

    public function testAll(ApiTester $I)
    {
        $I->wantTo('test data from /favorites');
        $I->amBearerAuthenticated($this->token);

        // test data for all
        $I->sendGET('/favorites');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertGreaterThanOrEqual(1,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer'], '$.data[*]');
    }

    //TODO:: add this back on after fixing delete
    public function _testCreate(ApiTester $I)
    {
        $I->wantTo('test adding to /favorites');
        $I->amBearerAuthenticated($this->token);

        // test add
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST('/favorites', ['reserveId' => '5']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'success']);

        // test wrong data
        $I->sendPOST('/favorites', ['reserveId' => '5']); //already favorited above
        $I->seeResponseCodeIs(200);
        $I->sendPOST('/favorites', ['reserveId' => 'wrong']); // wrong reserve id
        $I->seeResponseCodeIs(422);
    }

    //TODO:: looks like codeception is not sending the data properly for DELETE request
    public function _testDelete(ApiTester $I)
    {
        $I->wantTo('test deleting from /favorites');
        $I->amBearerAuthenticated($this->token);

        // test delete
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendDELETE('/favorites', array('reserveId' => '5'));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'success']);

        // // test wrong data
        $I->sendDELETE('/favorites', array('reserveId' => 'wrong'));
        $I->seeResponseCodeIs(422);
    }

    public function testFilters(ApiTester $I)
    {
        $I->wantTo('test filters for /favorites');
        $I->amBearerAuthenticated($this->token);

        // test filter - reserve
        $I->sendGET('/favorites?reserve=1,2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertGreaterThanOrEqual(1,sizeof($I->grabDataFromResponseByJsonPath('$..data')[0]));
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer'], '$.data[*]');

        // test wrong data
        $I->sendGET('/favorites?reserve=wrong');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEmpty($I->grabDataFromResponseByJsonPath('data')[0]);
    }

    // public function testPagination();
    // public function testSorting();
}
