<?php

namespace App\Api\Controllers;

use App\Api\Models\Region;
use App\Api\Transformers\RegionTransformer;

/**
 * @Resource("Regions", uri="api/regions")
 */
class RegionController extends BaseController
{
    public function __construct(Region $region)
    {
        $this->region = $region;
    }

    /**
     * Regions
     *
     * // Optional includes - reserves
     * // Optional filters  - id, name
     *
     * ```js
     * // Get all regions
     * $.ajax({
     *     url: "https://engena.co.za/api/regions",
     *     type: "GET",
     *     headers: { Authorization: "Bearer <token>" },
     * });
     *
     * // Filter by id
     *     regions?id=1
     *     regions?id[]=1&id[]=2
     *
     * // Filter by name
     *     regions?name=stellenbosch
     *     regions?name[]=stellenbosch&name[]=Somerset West
     *
     * // Include reserve information
     *     regions?include=reserve
     *     regions?name=stellenbosch&include=reserve
     *
     * ```
     * @Get("")
     * @Response(200, body={"data":{{"id":1,"name":"Stellenbosch"},{"id":2,"name":"Somerset West"}}})
     */
    public function all()
    {
        return $this->collection($this->region->addFilters()->active()->get(), new RegionTransformer);
    }
}
