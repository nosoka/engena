<?php

namespace App\Api\Services\Authentication;

interface AuthenticatorInterface
{

    // public function register($data);

    // public function activate($userId);

    // public function disable($userId);

    // authenticate the user
    public function attempt(array $credentials);

    // check that the user is logged in or not
    // public function check();

    // get the logged in user info
    // public function user();

    // get user information
    // public function findUserByLogin($login);

    // public function findUserById($id);

    // public function logout();

    // public function getErrors();
}
