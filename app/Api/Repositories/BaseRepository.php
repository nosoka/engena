<?php

namespace App\Api\Repositories;

use Dingo\Api\Routing\Helpers;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class BaseRepository
{
    use Helpers;

    public $model;
    protected $select;
    protected $filters = ['id' => 'ID'];

    public function getFilters()
    {
        return $this->filters;
    }

    public function all($columns = array('*'))
    {
        // TODO:: break the query builder into reusable methods
        // add conditions based on input variables passed via url
        // add orderby based on sort variables
        // add limit based on pagination variables
        // add eager loading conditions?
        // return $this->select()->addConditions()->get();
        // return $this->select()->addConditions()->get();
        return $this->select($columns)->parseUrlParams()->customDbFilters()->get();
    }

    public function select($columns = array('*'))
    {
        $this->select = $this->model->select($columns);

        return $this;
    }

    public function parseUrlParams()
    {
        $filters = $this->getFilters();

        foreach (app('request')->input() as $field => $value) {
            if (array_key_exists($field, $filters)) {
                $dbColumn = $filters[$field];

                $this->addCondition($this->select, $dbColumn, $value);
            }
        }

        return $this;
    }

    public function addCondition($selectHandler, $dbColumn, $value)
    {
        $seperator = ',';
        if (strpos($value, $seperator)) {
            $selectHandler->whereIn($dbColumn, explode($seperator, $value));
        } else {
            $selectHandler->where($dbColumn, $value);
        }
        return $this;
    }

    public function get()
    {
        return $this->select->get();
    }

    public function find($id, $columns = array('*'))
    {
        return $this->model->find($id, $columns);
    }

    public function findOrFail($id, $columns = array('*'))
    {
        try {
            return $this->model->findOrFail($id, $columns);
        } catch (ModelNotFoundException $e) {
            return $this->response->errorNotFound('No Records Found. Please crosscheck your request');
        }
    }

    public function delete($id)
    {
        if ($this->model->destroy($id)) {
            return true;
        }

        return false;
    }

    public function customDbFilters()
    {
        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }
}
