<?php

namespace App\Api\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    use Helpers;

    public function respondWithErrorIfEmpty($resultSet = array())
    {
        if (sizeof($resultSet) == 0) {
            return $this->response->errorNotFound('No Records Found. Please crosscheck your request');
        }
    }

    public function getFractal()
    {
        $request     = app('request');
        $transformer = app('api.transformer')->getAdapter();
        $transformer->parseFractalIncludes($request);

        return $transformer->getFractal();
    }
}
