<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register () {
        $this->app->instance('path.public', $this->app->basePath().DIRECTORY_SEPARATOR.'public');
    }
}
