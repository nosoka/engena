<?php

namespace App\Api\Controllers;

use App\Api\Models\Activity;
use App\Api\Transformers\ActivityTransformer;

/**
 * @Resource("Activities", uri="api/activities")
 */
class ActivityController extends BaseController
{
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    /**
     * Activities
     *
     * // Optional filters - id, name
     *
     * ```js
     * // Get all activities
     * $.ajax({
     *     url: "https://engena.co.za/api/activities",
     *     type: "GET",
     *     headers: { Authorization: "Bearer <token>" },
     * });
     *
     * // Filter by id
     *     activities?id=1
     *     activities?id[]=1&id[]=2
     *
     * // Filter by name
     *     activities?name=bike
     *     activities?name[]=bike&name[]=running
     *
     * ```
     * @Get("")
     * @Response(200, body={"data":{{"id":2,"name":"Hike"},{"id":4,"name":"Bike"}}})
     *
     */
    public function all()
    {
        return $this->collection($this->activity->addFilters()->get(), new ActivityTransformer);
    }
}
