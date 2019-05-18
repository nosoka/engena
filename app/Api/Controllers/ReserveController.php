<?php

namespace App\Api\Controllers;

use App\Api\Repositories\ReserveEntranceRepository;
use App\Api\Repositories\ReserveRepository;
use App\Api\Transformers\ReserveEntranceTransformer;
use App\Api\Transformers\ReserveTransformer;
use League\Fractal\Resource\Collection;

/**
 * @Resource("Reserves", uri="api/reserves")
 */
class ReserveController extends BaseController
{
    public function __construct(ReserveRepository $repo, ReserveTransformer $transformer,
        ReserveEntranceRepository $entranceRepo, ReserveEntranceTransformer $entranceTransformer) {
        $this->repo                = $repo;
        $this->entranceRepo        = $entranceRepo;
        $this->transformer         = $transformer;
        $this->entranceTransformer = $entranceTransformer;
    }

    /**
     * Reserves
     *
     * ```js
     * // Sample Request
     * $.ajax({
     *     url: "https://api.engena.co.za/api/reserves",
     *     type: "GET",
     * });
     *
     * // Get all reserves where (activity is 1 or 2 or 3).
     * $.ajax({
     *     url: "https://api.engena.co.za/api/reserves?activity=1,2,3",
     *     type: "GET",
     * });
     *
     * // Get all reserves where (region=1 or 2).
     * $.ajax({
     *     url: "https://api.engena.co.za/api/reserves?region=1,2",
     *     type: "GET",
     * });
     *
     * // Get all reserves with (activity=3) and (region=2).
     * // also include trails data for each matching reserve
     * $.ajax({
     *     url: "https://api.engena.co.za/api/reserves?activity=3&region=1&include=trails",
     *     type: "GET",
     * });
     *
     * // Optional include - region, trails, entrances
     * // Optional filters - id, name, activity, region
     * // example: /reserves?activity=1,2,3&region=1,2
     * // example: /reserves?id=1,2&include=region,trails,entrances
     * // example: /reserves?region=1,2&include=trails
     * ```
     * @Get("")
     * @Response(200, body={"data":{{"reserveId":1,"reserveName":"Jonkershoek"},{"reserveId":2,"reserveName":"Bottelary"}}})
     */
    public function all()
    {
        return $this->collection($this->repo->all(), $this->transformer);
    }

    //TODO:: this can be cleaned up as its already accessible ex: /api/reserves?region=1,2
    public function getReservesByRegion($regionId = null)
    {
        $reserves = $this->repo->getReservesByRegion($regionId);

        return $this->response->collection($reserves, $this->transformer);
    }

    //TODO:: this can be cleaned up as its already accessible ex: /api/reserves?region=1,2&activity=2,3
    public function getReservesByRegionAndActivity($regionId = null, $activityId = null)
    {
        $reserves = $this->repo->getReservesByRegionAndActivity($regionId, $activityId);

        return $this->response->collection($reserves, $this->transformer);
    }

    public function getReservesByCoords($latitude = null, $longitude = null)
    {
        $fractal          = $this->getFractal();
        $collection       = new Collection($this->entranceRepo->all(), $this->entranceTransformer);
        $reserves         = $fractal->createData($collection)->toArray();
        $reserves['data'] = $this->entranceRepo->addDistanceToData($reserves['data'], $latitude, $longitude);

        return response()->json($reserves);
    }

    //TODO:: this can be cleaned up as its accessible /api/reserves/lat/x/long/y?activity=2,3
    public function getReservesByCoordsAndActivity($latitude = null, $longitude = null, $activityId = null)
    {
        $fractal          = $this->getFractal();
        $collection       = new Collection($this->entranceRepo->getByActivity($activityId), $this->entranceTransformer);
        $reserves         = $fractal->createData($collection)->toArray();
        $reserves['data'] = $this->entranceRepo->addDistanceToData($reserves['data'], $latitude, $longitude);

        return response()->json($reserves);
    }
}
