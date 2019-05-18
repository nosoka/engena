<?php

namespace App\Api\Repositories;

use App\Api\Models\User;
use App\Api\Models\UserSocialAccount;
use App\Api\Services\FileService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository extends BaseRepository
{
    public function __construct(User $model, UserSocialAccount $socialAccount, FileService $fileService)
    {
        $this->fileService   = $fileService;
        $this->model         = $model;
        $this->socialAccount = $socialAccount;
        $this->primaryKey    = $this->model->getKeyName();
        $this->request       = app('request');
    }

    public function create(array $data = array())
    {
        $data = $data ?: $this->request->all();

        $userData = [
            'Username'  => $data['username'],
            'password'  => $data['password'],
            'full_name' => $data['fullname'],
            'Mobile'    => $data['mobile'],
            'Email'     => $data['email'],
            'confirmed' => 0,
            'confirmation_code' => sha1($data['email']) .str_random(30),
        ];

        if (!$user = $this->model->create($userData)) {
            return false;
        }

        $user->linkWechatIdIfProvided();

        return $user;
    }

    public function update(array $data = array())
    {
        $data = $data ?: $this->request->all();

        if (array_key_exists($this->primaryKey, $data)) {
            $user = $this->findOrFail($data[$this->primaryKey]);
        } else {
            $user = $this->auth->user();
        }

        $user->full_name = $data['fullname'];
        $user->Email     = $data['email'];
        $user->Mobile    = $data['mobile'];
        if(!empty($data['password'])){    //if password isset => update field
            $user->password    = $data['password'];
        }

        if ($user->save()) {
            return $user;
        }

        return false;

    }

    public function updatePhoto()
    {
        $urlPath           = url('/api/public/images/profiles/');
        $destinationFolder = base_path('/public/images/profiles/');
        $uploadedFile      = $this->request->file('filename');
        $photo             = $this->fileService->moveFile($uploadedFile, $destinationFolder);

        $user           = $this->auth->user();
        $user->PhotoUrl = $urlPath . "/" . $photo->getFilename();
        if ($user->save()) {
            return $user;
        }

        return false;
    }

    public function findUserByEmailHash($hash)
    {
        return $this->model->select()->whereRaw("sha1(email) = '$hash'")->first() ?: false;
        // TODO:: once the security features like banning/throttling etc. are implemented
        // check and throw error if user is banned or not activated yet
    }

    public function findUserByLogin($login)
    {
        // find user record via any of these fields - username/email/board_number/mobile
        return $this->model
            ->where('email', $login)
            ->OrWhere('username', $login)
            ->OrWhere('board_number', $login)
            ->OrWhere('mobile', $login)
            ->first()
            ?: false;
    }

    public function findUserByWechatId($wechatId)
    {
        return $this->findUserBySocialAccount('wechat', $wechatId);
    }

    public function findUserBySocialAccount($provider, $providerId)
    {
        $user = $this->model
            ->whereHas('socialAccounts', function ($query) use ($provider, $providerId) {
                $query->where('provider', $provider)
                    ->where('provider_id', $providerId);
            })->first();
        return $user;
    }

    public function findSocialAccount($provider, $providerId)
    {
        return $this->socialAccount
                    ->where('provider', $provider)
                    ->where('provider_id', $providerId)
                    ->first();
    }

    public function createSocialAccount($provider, $providerUser, $providerToken)
    {
        $userData = [
            'Username'  => $providerUser->getId(),
            'Email'     => $providerUser->getEmail(),
            'full_name' => "{$providerUser->getFirstName()} {$providerUser->getLastName()}",
        ];
        if ($provider == 'facebook') {
            $userData['PhotoUrl'] = $providerUser->getPictureUrl(); //download and create thumb images
        }
        if ($provider == 'google') {
            $userData['PhotoUrl'] = $providerUser->getAvatar(); //download and create thumb images
        }
        if (!$user = $this->model->create($userData)) {
            return false;
        }

        $socialAccountData = [
            'user_id'     => $user->ID,
            'provider'    => $provider,
            'provider_id' => $providerUser->getId(),
            'token'       => $providerToken,
            'full_name'   => $userData['full_name'],
            'avatar'      => $userData['PhotoUrl'],
        ];
        if (!$socialAccount = $this->socialAccount->create($socialAccountData)) {
            return false;
        }

        return $user;
    }

    public function linkSocialAccount($provider, $providerUser, $providerToken)
    {
        $avatar = '';
        if ($provider == 'facebook') { $avatar = $providerUser->getPictureUrl(); }
        if ($provider == 'google') { $avatar = $providerUser->getAvatar(); }

        $socialAccountData = [
            'user_id'     => $this->auth->user()->ID,
            'provider'    => $provider,
            'provider_id' => $providerUser->getId(),
            'token'       => $providerToken,
            'full_name'   => "{$providerUser->getFirstName()} {$providerUser->getLastName()}",
            'avatar'      => $avatar,
        ];
        if (!$socialAccount = $this->socialAccount->create($socialAccountData)) {
            return false;
        }

        return $socialAccount;
    }

    public function resetConfirmationCode($user)
    {
        $user->confirmed         = false;
        $user->confirmation_code = sha1($user->email) . str_random(30);
        return $user->save();
    }

    public function activateAccount($confirmationCode)
    {
        try {
            $user = $this->model->whereConfirmationCode($confirmationCode)->firstOrFail();
            $user->confirmed         = true;
            $user->confirmation_code = null;
            $user->save();
            return $user;
        } catch (ModelNotFoundException $e) {
            return $this->response->errorNotFound('Invalid confirmation code. Please crosscheck and retry.');
        }
    }

}
