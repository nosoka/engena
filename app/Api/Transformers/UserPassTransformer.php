<?php

namespace App\Api\Transformers;

use App\Api\Models\UserPass;

class UserPassTransformer extends BaseTransformer {
    protected $availableIncludes = ['pass'];
    protected $defaultIncludes = ['pass'];

    public function transform(UserPass $userPass) {
        $userPass = [
            'id' => (int)$userPass->id,
            'status' => $userPass->status,
            'is_owner' => (bool)$userPass->is_owner,
            'amount' => $userPass->pass_amount,
            'start_date' => $userPass->start_date,
            'end_date' => $userPass->end_date,
            'created_date' => $userPass->created_date,
            'photos' => $this->fileService()->guessThumbImageUrls($userPass->pass_photo),
        ];

        return $userPass;
    }

    public function includePass(UserPass $userPass) {
        if ($pass = $userPass->pass) {
            return $this->item($pass, new PassTransformer);
        }
    }

}
