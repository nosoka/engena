<?php

namespace App\Api\Providers;

use Dingo\Api\Transformer\Adapter\Fractal;
use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;

class FractalServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('\Dingo\Api\Transformer\Adapter\Fractal', function($app) {
            $fractal = new Manager;
            $fractal->setSerializer(new ArraySerializer);
            return new Fractal($fractal);
        });
    }
}
