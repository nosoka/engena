<?php

namespace App\Api\Controllers;

use App\Api\Repositories\UserRepository;
use App\Api\Services\Authentication\Auth;
use App\Api\Services\Authentication\SocialAuth;
use App\Api\Validators\EmailValidator;
use App\Api\Validators\LoginValidator;
use App\Api\Validators\ResetPasswordValidator;
use App\Api\Requests\CreateUserRequest;
use App\Api\Requests\LoginRequest;
use App\Api\Validators\SocialAuthValidator;

/**
 * @Resource("Authentication", uri="/api/auth")
 */
class AuthController extends BaseController
{
    public function __construct(Auth $authService, SocialAuth $socialauthService, UserRepository $repo)
    {
        $this->authService       = $authService;
        $this->socialauthService = $socialauthService;
        $this->repo              = $repo;
    }

    /**
     * Create new user
     *
     * Validate if all the necessary information is provided. If all is well create a new user account and respond with an access token
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://engena.co.za/api/auth/register",
     *     type: "POST",
     *     data: {
     *         "username": "foo",
     *         "password": "bar",
     *         "fullname": "Gorrest Gump",
     *         "email": "abc@xyz.com",
     *         "mobile": 9876543210
     *     }
     * });
     *
     * // access token is returned if WeChat-ID is present in headers.
     *     headers: { WeChat-ID: "xyz.." },
     *
     * ```
     * @Post("/register")
     * @Transaction({
     *      @Response(200, body={"status": "success"}),
     *      @Response(422, body={"message": "Some Fields are missing.", "status_code": 422})
     * })
     */
    public function postRegister(CreateUserRequest $request)
    {
        if (!$user = $this->authService->register()) {
            return $this->response->error('Unable to register new user', 422);
        }

        return response()->json(['status' => 'success', 'message' => "User Registered Successfully."]);
    }

    /**
     * Login user
     *
     * Verify the login info that is passed and respond with an access token
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://engena.co.za/api/auth/login",
     *     type: "POST",
     *     data: {
     *         "username": "foo",
     *         "password": "bar"
     *     }
     * });
     *
     *
     * // If WeChat-ID is present in headers,
     * // access token is returned after verifying credentials
     * // even if the user has not activated via their email
     *    headers: { WeChat-ID: "xyz.." },
     *
     * ```
     * @Post("/login")
     * @Transaction({
     *      @Response(200, body={"token": "xyz.....", "status_code": 200}),
     *      @Response(422, body={"message": "Invalid Credentials.", "status_code": 422})
     * })
     */
    public function postLogin(LoginRequest $request)
    {

        if (!$token = $this->authService->attempt()) {
            return $this->response->error($this->authService->error(), 401);
        }


        return response()->json(compact('token'));
    }

    /**
     * Forgot password
     *
     * Validate the login provided and generate password reset code and email the same.
     * login field can be any of username, email, mobile, board number.
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://engena.co.za/api/auth/forgotpassword",
     *     type: "POST",
     *     data: { "login": "abc@xyz.com" }
     * });
     * ```
     * @Post("/forgotpassword")
     * @Transaction({
     *     @Response(200, body={"token": "success", "status_code": 200}),
     *     @Response(422, body={"message": "Could not find that account. Please crosscheck and retry.", "status_code": 422}),
     * })
     */
    public function forgotPassword()
    {
        if (!$this->authService->createPasswordReminder(app('request')->get('login'))) {
            return $this->response->error($this->authService->getError(), 422);
        }

        return response()->json(['status' => 'success','info' => 'Check your email and confirm the password reset']);
    }

    /**
     * Reset password
     *
     * Validate the token provided and update the password and respond with access token.
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://engena.co.za/api/auth/resetpassword",
     *     type: "POST",
     *     data: {
     *         "code": "NW3RyDay0xJpFCxyonMsgkej6O....",
     *         "password": "bar"
     *     }
     * });
     * ```
     * @Post("/resetpassword")
     * @Response(200, body={"token": "success", "status_code": 200})
     */
    public function resetPassword(ResetPasswordValidator $validator)
    {
        list($emailHash, $reminderCode) = explode(".", app('request')->get('code'));
        $newPassword                    = app('request')->get('password');

        if (!$token = $this->authService->resetPassword($emailHash, $reminderCode, $newPassword)) {
            return $this->response->error('Failed to update password. Please contact support', 422);
        }

        return response()->json(compact('token'));
    }

    public function verifyResetCode()
    {
        list($emailHash, $reminderCode) = explode(".", app('request')->get('code'));

        if (!$this->authService->reminderExists($emailHash)) {
            return $this->response->error('Invalid code. Please contact support', 422);
        }

        return response()->json(['status' => 'success','info' => 'Valid code. please go ahead and update your password']);
    }


    public function facebookLogin(SocialAuthValidator $validator)
    {
        $accessToken = app('request')->get('accessToken');

        if (!$token = $this->socialauthService->facebookLogin($accessToken)) {
            return $this->response->error('Failed to login', 422);
        }
        return response()->json(compact('token'));
    }

    public function googleLogin(SocialAuthValidator $validator)
    {
        $accessToken = app('request')->get('accessToken');

        if (!$token = $this->socialauthService->googleLogin($accessToken)) {
            return $this->response->error('Failed to login', 422);
        }
        return response()->json(compact('token'));
    }

    public function stravaConnect(SocialAuthValidator $validator)
    {
        $accessToken = app('request')->get('accessToken');

        if (!$userData = $this->socialauthService->stravaConnect($accessToken)) {
            return $this->response->error('Failed to connect to strava account', 422);
        }
        return response()->json(['status' => 'success', 'data' => $userData]);
    }

    /**
     * Activation Email
     *
     * Validate the login provided and generate activation code and email the same.
     * login field can be any of username, email, mobile, board number.
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://engena.co.za/api/auth/resend_activation",
     *     type: "POST",
     *     data: { "login": "abc@xyz.com" }
     * });
     * ```
     * @Post("/resend_activation")
     * @Transaction({
     *     @Response(200, body={"token": "success", "status_code": 200}),
     *     @Response(422, body={"message": "Could not find that account. Please crosscheck and retry.", "status_code": 422}),
     * })
     */
    public function resendActivation()
    {
        if (!$this->authService->resendActivation(app('request')->get('login'))) {
            return $this->response->error($this->authService->getError(), 422);
        }

        return response()->json(['status' => 'success', 'message' => 'Emailed activation email to the account. Please check your email.']);
    }

    /**
     * Activate Account
     *
     * Validate the activation code provided and respond with access token.
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://engena.co.za/api/auth/resetpassword",
     *     type: "POST",
     *     data: {
     *         "code": "NW3RyDay0xJpFCxyonMsgkej6O....",
     *     }
     * });
     * ```
     * @Post("/activate")
     * @Response(200, body={"token": "success", "status_code": 200})
     */
    public function activateAccount()
    {
        if (!$token = $this->authService->activateAccount(app('request')->get('code'))) {
            return $this->response->error('Invalid confirmation code. Please crosscheck and retry.', 422);
        }

        return response()->json(compact('token'));
    }
}
