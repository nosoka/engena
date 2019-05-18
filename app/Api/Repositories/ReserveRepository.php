<?php

namespace App\Api\Repositories;

use App\Api\Models\Reserve;

class ReserveRepository extends BaseRepository
{
    protected $filters = ['id' => 'ID', 'name' => 'ReserveName', 'region' => 'region_id'];

    public function __construct(Reserve $model)
    {
        $this->model = $model;
    }

    public function getReservesByRegion($regionId = null)
    {
        return $this->model->where('region_id', $regionId)->get();
    }

    public function getReservesByRegionAndActivity($regionId = null, $activityId = null)
    {
        return $this->model->where('region_id', $regionId)
            ->whereHas('activities', function ($query) use ($activityId) {
                $query->where('activity_id', '=', $activityId);})
            ->get();
    }

    public function customDbFilters()
    {
        // Only fetch active reserves
        $this->select->whereEnabled(1);

        if ($activityId = app('request')->input('activity')) {
            $this->select->whereHas('activities', function ($query) use ($activityId) {
                $this->addCondition($query, 'activity_id', $activityId);
            });
        }
        return $this;
    }

    public function includeActivePasses()
    {
        return $this->model
            ->whereEnabled(true)
            ->with([
                'userPasses' => function ($query) {
                    $query->where('start_date', '<=', date("Y-m-d"))
                        ->where('end_date', '>=', date("Y-m-d"))
                        ->orderBy('created_at', 'desc');
                },
                'userPasses.user','userPasses.pass',
            ])->get();
    }
}
