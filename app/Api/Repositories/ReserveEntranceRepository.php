<?php

namespace App\Api\Repositories;

use App\Api\Models\ReserveEntrance;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Geotools;

class ReserveEntranceRepository extends BaseRepository
{
    protected $filters = ['id' => 'ID', 'name' => 'EntranceName', 'reserve' => 'ReserveID'];

    public function __construct(ReserveEntrance $model)
    {
        $this->model = $model;
    }

    public function getByActivity($activityId = null)
    {
        return $this->model
            ->whereHas('activities', function ($query) use ($activityId) {
                $query->where('ActivityID', '=', $activityId);})
            ->get();
    }

    public function addDistanceToData(array $entrances = array(), $latitude, $longitude)
    {
        if (sizeof($entrances) < 1) {
            return false;
        }
        $geotools = new Geotools();
        $coordA   = new Coordinate([$latitude, $longitude]);
        foreach ($entrances as $key => $entrance) {
            $coordB                      = new Coordinate([$entrance['entranceLongitude'], $entrance['entranceLatitude']]);
            $distance                    = $geotools->distance()->setFrom($coordA)->setTo($coordB);
            $entrances[$key]["distance"] = round($distance->in('km')->haversine(), 2);
        }

        usort($entrances, array($this, 'orderByDistance'));

        return $entrances;
    }

    public function orderByDistance($a, $b)
    {
        $newKey = 'distance';
        if ($a[$newKey] == $b[$newKey]) {
            return 0;
        }
        return ($a[$newKey] < $b[$newKey]) ? -1 : 1;
    }

    public function customDbFilters()
    {
        if ($activityId = app('request')->input('activity')) {
            $this->select->whereHas('activities', function ($query) use ($activityId) {
                $this->addCondition($query, 'ActivityID', $activityId);
            });
        }
        return $this;
    }
}
