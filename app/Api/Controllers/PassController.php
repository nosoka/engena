<?php

namespace App\Api\Controllers;

use App\Api\Repositories\UserPassRepository;
use App\Api\Requests\CreatePassesRequest;
use App\Api\Requests\UpdatePassRequest;
use App\Api\Services\Authentication\Auth;
use App\Api\Transformers\UserPassTransformer;

/**
 * @Resource("Passes", uri="passes")
 */
class PassController extends BaseController
{
    public function __construct(Auth $authService, UserPassRepository $repo, UserPassTransformer $transformer)
    {
        $this->authService = $authService;
        $this->repo        = $repo;
        $this->transformer = $transformer;
    }

    /**
     * Get user passes
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://api.engena.co.za/api/passes",
     *     type: "GET",
     * });
     *
     * // Get all passes with no photos uploaded
     * $.ajax({
     *     url: "https://api.engena.co.za/api/passes?photo=null",
     *     type: "GET",
     * });
     *
     * // Optional include - reserve
     * // Optional filters - id, photo, reserve,
     * // example: /passes?photo=null
     * // example: /passes?id=1,2&include=reserve
     * // example: /passes?reserve=1,2&include=reserve
     * ```
     * @Get("")
     * @Response(200, body={"data":{{"passType":"pass","passId":1,"passAmount":"100","passDate":"2016-01-15","transactionDate":"2016-01-10","ownPass":true,"photoUrl":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7.jpeg","photos":{"user":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7.jpeg","small":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7_small.jpeg","medium":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7_medium.jpeg","original":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7_original.jpeg"}}}})
     */
    public function index()
    {
        return $this->collection($this->repo->all(), $this->transformer);
    }

    /**
     * Get user pass
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://api.engena.co.za/api/passes/1",
     *     type: "GET",
     * });
     *
     * // Optional include - reserve
     * // example: /passes/1
     * // example: /passes/1?include=reserve
     * ```
     * @Get("id")
     * @Response(200, body={"passType":"pass","passId":1,"passAmount":"100","passDate":"2016-01-15","transactionDate":"2016-01-10","ownPass":true,"photoUrl":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7.jpeg","photos":{"user":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7.jpeg","small":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7_small.jpeg","medium":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7_medium.jpeg","original":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7_original.jpeg"}})
     */
    public function show($id)
    {
        return $this->item($this->repo->findOrFail($id), $this->transformer);
    }


    /**
     * Create Passes
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://api.engena.co.za/api/passes",
     *     type: "POST",
     *     data: {
     *         'passes' : [
     *             { id: 1, quantity: 1, startDate: '2016-12-06' },
     *             { id: 2, quantity: 1, startDate: '2016-12-07' },
     *         ]
     *     },
     * });
     * ```
     * @Post("")
     * @Response(200, body={"data":{{"id":289,"status":"active","is_owner":true,"amount":"50.00","start_date":"Dec 6, 2016","end_date":"Dec 6, 2016","created_date":null},{"id":290,"status":"active","is_owner":true,"amount":"50.00","start_date":"Dec 7, 2016","end_date":"Dec 7, 2016","created_date":null}}})
     */
    public function create(CreatePassesRequest $request)
    {
        if(!$this->authService->isValidWechatRequest()) {
            return $this->response->error('Please provide a valid wechat id', 422);
        }

        $passes = $this->repo->createPasses();
        if($passes->count() > 0) {
            return $this->collection($passes, $this->transformer);
        } else {
            return $this->response->error('Failed to create passes', 422);
        }
    }

    /**
     * Update pass photo
     *
     * ```js
     * // Sample Request
     * var formData = new FormData();
     * var files = $("#pass_photo").get(0).files;
     * form.append("filename", files[0]);
     * $.ajax({
     *     url: "https://api.engena.co.za/api/passes/1",
     *     type: "POST", processData: false, contentType: false,
     *     data: formData,
     * });
     * ```
     * @Post("id")
     * @Response(200, body={"passType":"pass","passId":1,"passAmount":"100","passDate":"2016-01-15","transactionDate":"2016-01-10","ownPass":true,"photoUrl":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7.jpeg","photos":{"user":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7.jpeg","small":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7_small.jpeg","medium":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7_medium.jpeg","original":"http:\/\/api.engena.dev\/images\/passes\/6b5d34a9030c51268f8e3e6b0e85dc59b5fd4cb7_original.jpeg"}})
     */
    public function updatePhoto(UpdatePassRequest $request, $id)
    {
        if (!$pass = $this->repo->updatePhoto($id)) {
            return $this->response->error(trans('api.pass.update.failed'), 422);
        }

        return $this->response->item($pass, $this->transformer);
    }
}
