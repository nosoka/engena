<?php

namespace App\Api\Repositories;

use App\Api\Models\FavoriteReserve;

class FavoriteRepository extends BaseRepository
{
    protected $filters = ['reserve' => 'ReserveID'];

    public function __construct(FavoriteReserve $model)
    {
        $this->model = $model;
    }

    public function addFavoriteReserve(array $data = array())
    {
        $data = $data ?: app('request')->all();
        $row  = [
            'UserID'    => $this->auth->user()->ID,
            'ReserveID' => $data['reserveId'],
        ];

        if ($this->isInFavorites($row)) {
            return false;
        }
        if (!$this->model->create($row)) {
            return false;
        }

        return true;
    }

    public function deleteFavoriteReserve(array $data = array())
    {
        $data = $data ?: app('request')->all();
        $row  = [
            'UserID'    => $this->auth->user()->ID,
            'ReserveID' => $data['reserveId'],
        ];

        $query = $this->model->select()
            ->where('UserID', $row['UserID'])
            ->where('ReserveID', $row['ReserveID']);

        if (!$query->delete()) {
            return false;
        }

        return true;
    }

    public function isInFavorites($data)
    {
        $rowCount = $this->model->select()
            ->where('UserID', $data['UserID'])
            ->where('ReserveID', $data['ReserveID'])
            ->count();

        if ($rowCount > 0) {
            return true;
        }

        return false;
    }

    public function customDbFilters()
    {
        $this->select->Where('UserID', $this->auth->user()->ID);

        return $this;
    }
}
