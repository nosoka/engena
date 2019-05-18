<?php

use App\Api\Models\User;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserCest
{
    public function _before(ApiTester $I, AuthCest $auth)
    {
        $auth->_createToken($I);
        $this->token = $auth->_getToken();
    }

    public function testAccess(ApiTester $I)
    {
        $I->wantTo('test access to /user');
        $I->deleteHeader('Authorization');

        // test access
        $I->sendGET('/user');
        $I->seeResponseCodeIs(401);
        $I->sendPUT('/user');
        $I->seeResponseCodeIs(401);
        $I->sendPOST('/user/photo');
        $I->seeResponseCodeIs(401);
    }

    public function testData(ApiTester $I)
    {
        $I->wantTo('test data from /user');
        $I->amBearerAuthenticated($this->token);

        // test data
        $I->sendGET('/user');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['firstName' => 'string', 'surName' => 'string',
            'mobile' => 'string', 'email' => 'string']);
    }

    public function testIncludes(ApiTester $I)
    {
        $I->wantTo('test includes for /user');
        $I->amBearerAuthenticated($this->token);

        // test includes
        $I->sendGET('/user?include=passes,subscriptions,favorites');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['firstName' => 'string', 'surName' => 'string']);
        $I->seeResponseMatchesJsonType(['id' => 'integer'], '$.passes.data[*]');
        $I->seeResponseMatchesJsonType(['id' => 'integer'], '$.subscriptions.data[*]');
        $I->seeResponseMatchesJsonType(['reserveId' => 'integer'], '$.favorites.data[*]');

        // test wrong includes
        $I->sendGET('/user?include=region,reserves,trails,activities');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['firstName' => 'string', 'surName' => 'string']);
        $I->dontSeeResponseContains('region');
        $I->dontSeeResponseContains('reserves');
        $I->dontSeeResponseContains('trails');
        $I->dontSeeResponseContains('activities');

    }
    public function testUpdate(ApiTester $I, User $user)
    {
        $I->wantTo('test updates to /user');
        $I->amBearerAuthenticated($this->token);

        $faker = Factory::create();
        $userData = [
            'firstname' => $faker->firstName,
            'surname'   => $faker->lastName,
            'mobile'    => $faker->phoneNumber,
            'email'     => $faker->email,
        ];

        // test successful update
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPUT('/user',$userData);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'firstName' => $userData['firstname'], 'surName' => $userData['surname'],
            'mobile' => $userData['mobile'], 'email' => $userData['email'],
        ]);

        // test required fields
        $I->sendPUT('/user', []);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->assertEquals(sizeof($userData),sizeof($I->grabDataFromResponseByJsonPath('$.errors')[0]));
        $I->seeResponseMatchesJsonType(['0' => 'string:regex(/field is required/)'], '$.errors[*]');

        // test email format
        $I->sendPUT('/user', ['email' => 'test']);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->assertEquals(sizeof($userData),sizeof($I->grabDataFromResponseByJsonPath('$.errors')[0]));
        $I->seeResponseMatchesJsonType(['0' => 'string:regex(/must be a valid email/)'], '$.errors.email');

        // test email uniqueness. email is no longer
        // because sometimes we don't get the email address while signing up social accounts
        // $usedEmail = $user->getModel()->where('email', '!=', $userData['email'])->first()->Email;
        // $I->sendPUT('/user', ['email' => $usedEmail]);
        // $I->seeResponseCodeIs(422);
        // $I->seeResponseIsJson();
        // $I->assertEquals(sizeof($userData),sizeof($I->grabDataFromResponseByJsonPath('$.errors')[0]));
        // $I->seeResponseMatchesJsonType(['0' => 'string:regex(/taken/)'], '$.errors.email');
    }

    //TODO: fix this after figuring how to upload files using sendPOST
    public function _testUploadPhoto(ApiTester $I)
    {
        $I->wantTo('test photo upload to /user');
        $I->amBearerAuthenticated($this->token);

        $path = codecept_data_dir();
        $filename = 'elephant.jpg';
        $mime = 'image/jpeg';
        $uploadedFile = new UploadedFile($path . $filename, $filename, $mime,filesize($path . $filename));

        // test successful update
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST('/user/photo', null, ['filename' => $uploadedFile]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}
