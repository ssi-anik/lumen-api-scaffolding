<?php

namespace App\Providers;

use App\Http\Middleware\QueryLoggerMiddleware;
use Illuminate\Support\ServiceProvider;

class QueryLoggerServiceProvider extends ServiceProvider
{
    public function register () {
        $this->app['db']->connection()->setEventDispatcher($this->app['events']);
        // uses terminate method, that's why singleton
        $this->app->singleton(QueryLoggerMiddleware::class);
    }
}
