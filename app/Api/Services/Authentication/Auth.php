<?php

namespace App\Api\Services\Authentication;

use App\Api\Repositories\UserRepository;
use App\Api\Services\EmailService;
use Cartalyst\Sentinel\Sentinel as SentinelAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class Auth implements AuthenticatorInterface
{
    protected $data;
    protected $error;

    public function __construct(JWTAuth $jwt, UserRepository $repo, SentinelAuth $sentinel, EmailService $mailer)
    {
        $this->jwt      = $jwt;
        $this->mailer   = $mailer;
        $this->repo     = $repo;
        $this->sentinel = $sentinel;
        $this->reminder = $this->sentinel->getReminderRepository();
        $this->request  = app('request');
    }

    /**
     * @param $credentials
     * @return bool
     * sets errors on failure
     */
    public function attempt(array $credentials = null)
    {
        $login = $this->request->input('username');
        if (!$user = $this->repo->findUserByLogin($login)) {
            $this->setError('Wrong username or password.');
            return false;
        }

        // TODO:: verify why eloquent is not returning saved data when queried immediately
        $wechatId = $user->linkWechatIdIfProvided();

        if (!$wechatId && !$user->confirmed) {
            $this->setError('Inactive account. Please confirm your account via email');
            return false;
        }

        $credentials = $credentials ?: [
            'username'  => $user->Username,
            'password'  => $this->request->input('password')
        ];

        return $this->createToken($credentials);
    }

    public function createToken($credentials)
    {
        try {
            if (!$token = $this->jwt->attempt($credentials)) {
                $this->setError('Wrong username or password.');
                return false;
            }
        } catch (JWTException $e) {
            $this->setError('Wrong username or password.');
            return false;
        }

        return $token;
    }

    public function register(array $data = array())
    {
        if (!$user = $this->repo->create($data)) {
            return false;
        }

        // TODO:: move this to use queues.
        $this->mailer->emailSignupConfirmation($user);

        $user->token = $this->jwt->fromUser($user);
        return $user;
    }

    public function error()
    {
        return $this->getError();
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getSentinal()
    {
        return $this->sentinal;
    }

    public function createPasswordReminder($login)
    {
        if (!$user = $this->repo->findUserByLogin($login)) {
            $this->setError('Could not find that account. Please crosscheck and retry.');
            return false;
        }
        if (!$user->confirmed) {
            $this->setError('Account is not active. Please activate your account.');
            return false;
        }
        if (!$reminder = $this->reminder->create($user)) {
            $this->setError('Failed to create password reset code. Please contact support');
            return false;
        }

        if (!$this->mailer->emailPasswordReminder($user, $reminder)) {
            $this->setError('Failed to email the password reset code. Please contact support');
            return false;
        }

        return true;
    }


    public function reminderExists($emailHash = null)
    {
        if (!$user = $this->repo->findUserByEmailHash($emailHash)) {
            return false;
        }
        if (!$this->reminder->exists($user)) {
            return false;
        }

        return true;
    }


    public function resetPassword($emailHash = null, $resetCode = null, $newPassword = null)
    {
        if (is_null($emailHash) || is_null($resetCode) || is_null($newPassword)) {
            return false;
        }
        if (!$user = $this->repo->findUserByEmailHash($emailHash)) {
            return false;
        }
        if (!$user->confirmed) {
            return false;
        }
        if (!$this->reminder->complete($user, $resetCode, $newPassword)) {
            return false;
        }

        $user->password = $newPassword;
        if (!$user->save()) {
            return false;
        }
        if (!$token = $this->jwt->fromUser($user)) {
            return false;
        }

        return $token;
    }

    public function activateAccount($confirmationCode)
    {
        if (!$user = $this->repo->activateAccount(app('request')->get('code'))) {
            return false;
        }

        if (!$token = $this->jwt->fromUser($user)) {
            return false;
        }

        return $token;
    }

    public function resendActivation($login)
    {
        if (!$user = $this->repo->findUserByLogin($login)) {
            $this->setError('Could not find that account. Please crosscheck and retry.');
            return false;
        }
        if ($user->confirmed) {
            $this->setError('Account is already active. Please go to the login page to login to your account');
            return false;
        }
        if (!$this->repo->resetConfirmationCode($user)) {
            $this->setError('Something went wrong. Please contact support.');
            return false;
        }

        return $this->mailer->emailSignupConfirmation($user);
    }

    public function isValidWechatRequest()
    {
        if ($wechatId = app('request')->header(env('WECHAT_HEADER_PARAM', ''))) {
            if ($user = $this->repo->findUserByWechatId($wechatId)) {
                return true;
            }
        }

        return false;
    }
}
