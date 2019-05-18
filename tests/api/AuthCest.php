<?php

use App\Api\Models\User;
use Faker\Factory;

class AuthCest
{
    protected $token;
    protected $faker;

    public function testRegistration(ApiTester $I, User $user)
    {
        $faker = Factory::create();
        $signupData = [
            'userName'  => $faker->userName,
            'password'  => $faker->password,
            'firstname' => $faker->firstName,
            'surname'   => $faker->lastName,
            'mobile'    => $faker->phoneNumber,
            'email'     => $faker->email,
        ];
        $I->wantTo('test /auth/register');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        // test valid data
        $I->sendPOST('/auth/register', $signupData);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEquals('success', $I->grabDataFromResponseByJsonPath('$.status')[0]);

        // test wrong data - empty data
        $I->sendPOST('/auth/register', []);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->assertEquals(sizeof($signupData),sizeof($I->grabDataFromResponseByJsonPath('$.errors')[0]));
        $I->seeResponseMatchesJsonType(['0' => 'string:regex(/field is required/)'], '$.errors[*]');

        // test wrong data - email format
        $I->sendPOST('/auth/register', ['email' => 'test']);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->assertEquals(sizeof($signupData),sizeof($I->grabDataFromResponseByJsonPath('$.errors')[0]));
        $I->seeResponseMatchesJsonType(['0' => 'string:regex(/must be a valid email/)'], '$.errors.email');

        // test wrong data - duplicate data
        $existingUser = $user->first();
        $I->sendPOST('/auth/register', ['userName' => $existingUser->Username]);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->assertEquals(sizeof($signupData),sizeof($I->grabDataFromResponseByJsonPath('$.errors')[0]));
        $I->seeResponseMatchesJsonType(['0' => 'string:regex(/taken/)'], '$.errors.userName');

    }

    public function testLogin(ApiTester $I)
    {
        $faker = Factory::create();
        $credentials = ['username' => 'topgun', 'password' => 'test123'];
        $fakeData = ['username' => $faker->username, 'password' => $faker->password];
        $I->wantTo('test /auth/login');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        // test valid data
        $I->sendPOST('/auth/login', $credentials);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['token' => 'string']);
        $this->_setToken($I->grabDataFromResponseByJsonPath('$.token')[0]);

        // test wrong data - empty data
        $I->sendPOST('/auth/login', []);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->assertEquals(sizeof($credentials),sizeof($I->grabDataFromResponseByJsonPath('$.errors')[0]));
        $I->seeResponseMatchesJsonType(['0' => 'string:regex(/field is required/)'], '$.errors[*]');

        // test wrong data - random/fake data
        $I->sendPOST('/auth/login', $fakeData);
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
    }

    public function testForgotPassword(ApiTester $I, User $user)
    {
        $I->wantTo('test /forgotpassword');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $faker = Factory::create();
        $existingUser = $user->first();
        $validData = ['email' => $existingUser->Email];
        $fakeData = ['email' => $faker->email];

        //test valid data
        $I->sendPOST('/auth/forgotpassword', $validData);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'success']);

        // test wrong data - empty data
        $I->sendPOST('/auth/forgotpassword', []);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->assertEquals(sizeof($validData),sizeof($I->grabDataFromResponseByJsonPath('$.errors')[0]));
        $I->seeResponseMatchesJsonType(['0' => 'string:regex(/field is required/)'], '$.errors[*]');

        // test wrong data - random/fake data
        $I->sendPOST('/auth/forgotpassword', $fakeData);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
    }

    // think about ways to mimicking this
    // public function testResetPassword()

    public function _getToken()
    {
        return $this->token;
    }

    public function _createToken(ApiTester $I)
    {
        $credentials = ['username' => 'topgun', 'password' => 'test123'];
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST('/auth/login', $credentials);
        $I->seeResponseCodeIs(200);
        $I->seeResponseMatchesJsonType(['token' => 'string']);
        $this->_setToken($I->grabDataFromResponseByJsonPath('$.token')[0]);
    }

    public function _setToken($token)
    {
        return $this->token = $token;
    }

}
