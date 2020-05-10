<?php

/* @var \Laravel\Lumen\Routing\Router $router */
$router->get('/', function () use ($router) {
    return [
        'app'       => 'lumen-application',
        'framework' => $router->app->version(),
        'route'     => 'web',
    ];
});
