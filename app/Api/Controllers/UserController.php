<?php

namespace App\Api\Controllers;

use App\Api\Repositories\UserRepository;
use App\Api\Transformers\UserTransformer;
use App\Api\Validators\UserValidator;

/**
 * @Resource("User", uri="user")
 */
class UserController extends BaseController
{
    public function __construct(UserRepository $repo, UserTransformer $transformer)
    {
        $this->repo        = $repo;
        $this->transformer = $transformer;
    }

    /**
     * Get user information
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://api.engena.co.za/api/user",
     *     type: "GET",
     * });
     * ```
     * @Get("")
     * @Response(200, body={"userName":"topgun","firstName":"top","surName":"gun","mobile":"9876543210","email":"abc@xyz.com","photoUrl":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4.jpeg","photos":{"user":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4.jpeg","small":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4_small.jpeg","medium":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4_medium.jpeg","original":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4_original.jpeg"}})
     *
     */
    public function getUser()
    {
        return $this->response->item($this->auth->user(),$this->transformer);

    }

    /**
     * Update user information
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://api.engena.co.za/api/user",
     *     type: "PUT",
     *     data: {
     *         "firstname": "Forrest",
     *         "surname": "Gump",
     *         "email": "abc@xyz.com",
     *         "mobile": "9876543210"
     *     }
     * });
     * ```
     *
     * @Put("")
     * @Response(200, body={"userName":"topgun","firstName":"top","surName":"gun","mobile":"9876543210","email":"abc@xyz.com","photoUrl":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4.jpeg","photos":{"user":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4.jpeg","small":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4_small.jpeg","medium":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4_medium.jpeg","original":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4_original.jpeg"}})
     */
    public function updateUser(UserValidator $validator)
    {

        if (!$user = $this->repo->update()) {
            return $this->response->error('Unable to update user', 422);
        }

        //$user = $this->repo->update();

        return $this->response->item($user, $this->transformer);
    }

    /**
     * Upload user photo
     *
     * ```js
     * // Sample Request
     * var formData = new FormData();
     * var files = $("#profile_photo").get(0).files;
     * form.append("filename", files[0]);
     *
     * $.ajax({
     *     url: "https://engena.co.za/api/user/photo",
     *     type: "POST", processData: false, contentType: false,
     *     data: formData,
     * });
     * ```
     * @Post("")
     * @Response(200, body={"userName":"topgun","firstName":"top","surName":"gun","mobile":"9876543210","email":"abc@xyz.com","photoUrl":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4.jpeg","photos":{"user":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4.jpeg","small":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4_small.jpeg","medium":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4_medium.jpeg","original":"http:\/\/api.engena.dev\/images\/profiles\/fc378740ac58e3c4b00904e059177625732f2bd4_original.jpeg"}})
     */
    public function updatePhoto(UserValidator $validator)
    {
        if (!$user = $this->repo->updatePhoto()) {
            return $this->response->error('Unable to update user photo', 422);
        }

        return $this->response->item($user, $this->transformer);
    }
}
