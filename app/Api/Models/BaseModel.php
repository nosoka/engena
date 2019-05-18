<?php

namespace App\Api\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BaseModel extends Model
{
    use Filterable;

    protected $request;
    public $statuses = [ 0 => 'Inactive', 1 => 'Active' ];

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->request  = app('request');
    }

    public function getStatusAttribute()
    {
        return ($this->attributes['enabled'] == 1) ? "Active" : "Inactive";
    }

    public function scopeActive($query)
    {
        return $query->where('enabled', 1);
    }

    public function add(array $data = array())
    {
        $data  = $data ?: $this->request->all();
        $data  = collect($data)->only($this->fillable)->toArray();

        return $this->create($data);
    }

    public function update(array $data = [], array $options = [])
    {
        $data  = $data ?: $this->request->all();

        if (!array_key_exists($this->getKeyName(), $data)) {
            return false;
        }

        $data  = collect($data)->only($this->fillable)->toArray();
        $model = $this->where($this->getKeyName(), $this->request->get($this->getKeyName()));
        $model->update($data);

        return $model->first();
    }

    public function findOrFail($id, $columns = array('*'))
    {
        try {
            return parent::findOrFail($id, $columns);
        } catch (ModelNotFoundException $e) {
            return app('api.http.response')->errorNotFound('No Records Found. Please crosscheck your request');
        }
    }

    public function scopeAddFilters($query, array $filters = array())
    {
        $filters  = $filters ?: $this->request->all();

        return $this->scopeFilter($query, $filters);
    }
}
