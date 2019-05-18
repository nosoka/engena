<?php

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['namespace' => 'App\Api\Controllers'], function ($api) {

    // Publicly available routes
    $api->group(['prefix' => 'api/auth/'], function ($api) {
        $api->post('login', 'AuthController@postLogin');
        $api->post('register', 'AuthController@postRegister');
        $api->post('activate', 'AuthController@activateAccount');
        $api->post('resend_activation', 'AuthController@resendActivation');
        $api->post('forgotpassword', 'AuthController@forgotPassword');
        $api->post('verifyreset', 'AuthController@verifyResetCode');
        $api->post('resetpassword', 'AuthController@resetPassword');

        $api->post('facebook', 'AuthController@facebookLogin');
        $api->post('google', 'AuthController@googleLogin');
    });

    $api->group(['prefix' => 'api/'], function ($api) {
        //these shouldn't be behind auth, so that unauthenticated users can view trail info without having to login
        $api->get('activities', 'ActivityController@all');
        $api->get('regions', 'RegionController@all');
        $api->get('reserves', 'ReserveController@all');
        $api->get('trails', 'TrailController@index');
    });


    // Accessible only for logged in users
    $api->group(['middleware' => ['wechat', 'api.auth'], 'prefix' => 'api/'], function ($api) {

        // Social Logins
        $api->group(['prefix' => 'connect/'], function ($api) {
            $api->post('strava', 'AuthController@stravaConnect');
            $api->post('facebook', 'AuthController@facebookLogin');
            $api->post('google', 'AuthController@googleLogin');
        });

        // User Profile
        $api->get('user', 'UserController@getUser');
        $api->put('user', 'UserController@updateUser');
        $api->post('user/photo', 'UserController@updatePhoto');

        // User Passes
        // $api->get('passes', 'PaymentController@getPasses');
        $api->get('passes', 'PassController@index');
        $api->get('passes/{id}', 'PassController@show');
        $api->post('passes', 'PassController@create');
        $api->post('passes/{id}/photo', 'PassController@updatePhoto');

        // User Payments
        $api->get('payments', 'PaymentController@index');
        $api->post('payments', 'PaymentController@processPayment');
        $api->get('payments/token', 'PaymentController@getCheckoutId');

        // User Favorites
        $api->get('favorites', 'FavoriteController@index');
        $api->post('favorites', 'FavoriteController@create');
        $api->delete('favorites', 'FavoriteController@delete');

        // User Subscriptions
        // $api->get('subscriptions', 'SubscriptionController@index');
        // $api->post('subscriptions', 'SubscriptionController@create');

        // QR Codes
        $api->post('checkin', 'QrcodeController@checkin');
        $api->post('checkout', 'QrcodeController@checkout');

        // TODO:: clean this group after moving the functionality into url filters
        $api->group(['prefix' => 'reserves/lat/{latitude}/long/{longitude}'], function ($api) {
            $api->get('', 'ReserveController@getReservesByCoords');
            $api->get('activity/{activityId}', 'ReserveController@getReservesByCoordsAndActivity');
            // $api->get('count/{count}', 'ReserveController@getReservesByCoordsCount');
            // $api->get('user', 'ReserveController@getUserReservesByCoords');
            // $api->get('activity/{activityId}/user', 'ReserveController@getUserReservesByCoordsAndActivity');
        });
    });

    //TODO:: move this behind auth
    $api->post('api/trails/files', 'TrailController@uploadImage');
    $api->post('api/qrcodes', 'QrcodeController@store');
    $api->delete('api/qrcodes/{id}', 'QrcodeController@destroy');

// DB::listen(function($sql, $bindings, $time) { echo "{$sql}"; print_r($bindings); });
});
