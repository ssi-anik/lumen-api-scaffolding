<?php

/* @var \Laravel\Lumen\Routing\Router $router */
$router->get('/', 'MiscController@index');
$router->get('health', 'MiscController@health');
