<?php

namespace App\Api\Controllers;

use App\Api\Repositories\FavoriteRepository;
use App\Api\Transformers\FavoriteReserveTransformer;
use App\Api\Validators\FavoriteReserveValidator;

/**
 * @Resource("Favorites", uri="favorites")
 */
class FavoriteController extends BaseController
{
    public function __construct(FavoriteRepository $repo, FavoriteReserveTransformer $transformer)
    {
        $this->repo        = $repo;
        $this->transformer = $transformer;
    }

    /**
     * Get user favorites
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://api.engena.co.za/api/favorites",
     *     type: "GET",
     * });
     *
     * // Optional include - reserves
     * // Optional filters - id, reserve
     * // example: /favorites?id=1,2
     * // example: /favorites?reserve=1,2,3&include=reserve
     * ```
     * @Get("")
     * @Response(200, body={"data":{{"ReserveID":1},{"ReserveID":2},{"ReserveID":3}}})
     */
    public function index()
    {
        return $this->collection($this->repo->all(), $this->transformer);
    }

    /**
     * Add user favorite
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://api.engena.co.za/api/favorites",
     *     type: "POST",
     *     data: { "reserveId" : 1 }
     * });
     * ```
     *
     * @Post("")
     * @Response(200, body={"status":"success"})
     */
    public function create(FavoriteReserveValidator $validator)
    {
        if (!$favorite = $this->repo->addFavoriteReserve()) {
            return $this->response->error('Unable to add favorite', 422);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Delete user favorite
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://api.engena.co.za/api/favorites",
     *     type: "DELETE",
     *     data: { "reserveId" : 1 }
     * });
     * ```
     * @Delete("")
     * @Response(200, body={"status":"success"})
     */
    public function delete(FavoriteReserveValidator $validator)
    {
        if (!$favorite = $this->repo->deleteFavoriteReserve()) {
            return $this->response->error('Unable to delete favorite', 422);
        }

        return response()->json(['status' => 'success']);
    }
}
