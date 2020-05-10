<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(dirname(__DIR__)))->bootstrap();

$app = new Laravel\Lumen\Application(dirname(__DIR__));

// load configurations before any decision making based on environment
$app->configure('app');
$app->configure('auth');
$app->configure('cache');
$app->configure('database');
$app->configure('logging');
$app->configure('queue');
$app->configure('services');
$app->configure('settings');
$app->configure('cors');

$app->withEloquent();

$app->instance(Illuminate\Contracts\Debug\ExceptionHandler::class,
    new Nord\Lumen\ChainedExceptionHandler\ChainedExceptionHandler(new App\Exceptions\Handler(), [
        new Nord\Lumen\NewRelic\NewRelicExceptionHandler(),
    ]));

$app->singleton(Illuminate\Contracts\Console\Kernel::class, App\Console\Kernel::class);

$app->middleware([
    Nord\Lumen\NewRelic\NewRelicMiddleware::class,
    Nord\Lumen\Cors\CorsMiddleware::class,
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
]);

if (is_local() || in_environment('staging')) {
    $app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);
}

if (log_db_queries()) {
    $app->register(App\Providers\QueryLoggerServiceProvider::class);
    $app->middleware(App\Http\Middleware\QueryLoggerMiddleware::class);
}

$app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(Illuminate\Redis\RedisServiceProvider::class);
$app->register(Anik\Form\FormRequestServiceProvider::class);
$app->register(Nord\Lumen\Cors\CorsServiceProvider::class);
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);
$app->register(Nord\Lumen\NewRelic\NewRelicServiceProvider::class);

if (report_to_sentry()) {
    $app->register(Sentry\Laravel\ServiceProvider::class);
}

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\Api',
    'prefix'    => 'api',
], function ($router) {
    require __DIR__ . '/../routes/api.php';
});

return $app;
