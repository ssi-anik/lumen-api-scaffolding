# Lumen API Scaffolding
Lumen API scaffolding is a barebone lumen application with a few pre-installed packages required for development & production environment.

If you don't know how to build lumen application from scratch or you need to start your project painlessly, this project may help you.

## Lumen Version
This branch contains Lumen `6.x`. Check other branches for other another lumen versions.

## Dependencies, requirements and build tools
This project comes with `docker` & `docker-compose`. But to minimize the boot up time when you try `docker-compose up -d --build` the local files are mounted to application & worker containers and **NOT COPIED TO CONTAINERS**.
Thus, it's recommended to use PHP & composer locally. Resolve your project dependency before you run your application using `composer install`.

This project contains,
- `nginx` for web server.
- `postgres`/`mysql` for database.
- `redis` for cache.
- `beanstalkd` for queue driver.
- `beanstalk-console` as beanstalk's admin tool.

## Packages

`require` packages
* For Authentication: [tymon/jwt-auth](https://packagist.org/packages/tymon/jwt-auth)
* Guzzle wrapper for HTTP Request: [anik/apiz](https://packagist.org/packages/anik/apiz)
* Laravel type form request validation: [anik/form-request](https://packagist.org/packages/anik/form-request)
* For Redis: [illuminate/redis](https://packagist.org/packages/illuminate/redis)
* For Cors: [nordsoftware/lumen-cors](https://packagist.org/packages/nordsoftware/lumen-cors)
* For NewRelic: [nordsoftware/lumen-newrelic](https://packagist.org/packages/nordsoftware/lumen-newrelic)
* For queue with Beanstalk: [pda/pheanstalk](https://packagist.org/packages/pda/pheanstalk)
* For UUID: [ramsey/uuid](https://packagist.org/packages/ramsey/uuid)
* For Sentry: [sentry/sentry-laravel](https://packagist.org/packages/sentry/sentry-laravel)

`require-dev` packages
* For missing Laravel commands: [flipbox/lumen-generator](https://packagist.org/packages/flipbox/lumen-generator)

## How to use?
- Clone the repository.
- Switch to appropriate branch for the Lumen version you want to use.
- `cp docker-compose.yml.example docker-compose.yml`.
- Make the required changes to your `docker-compose.yml`.
- `cp .env.example .env`.
- Make the required changes to your `.env`.
- `docker-compose up -d --build` to build your containers.
- Loading `http://127.0.0.1:{NGINX_PORT}` in your browser will return a json response.

## Understanding Environments
- `REPORT_TO_SENTRY` - set to `false` if you don't want to use sentry.
- `SENTRY_LARAVEL_DSN` - to send your error messages to sentry.
- `JWT_*` - settings for JWT package it uses.
- `REDIS_*` - configuration for `redis` as cache.
- `BS_*` - configuration for Beanstalk for your queue driver.
- `BS_DEFAULT_QUEUE` - if you run your queue supervisor without providing queue name, set comma separated values here.
- `QUEUE_REDIS_CONNECTION` - Redis connection name if you want to use `redis` as your queue driver.
- `QUEUE_REDIS_*` - Will use these settings for redis queue driver set by `QUEUE_REDIS_CONNECTION`
- `QUEUE_REDIS_DEFAULT_QUEUE` - if you run your queue supervisor without any queue name, set comma separated values here.
- `ENABLE_QUERY_LOG` - set `false` to disable or `true` to enable. `daily` to **enable** and log in ./storage/logs/query-{Y-m-d}.log file.
- `LOG_HTTP_REQUEST` - set `true` to enable or `false` to disable when using `App\Service\AbstractApiService` on behalf of APIZ package to Communicate with remote service.
- `SERVICE_*` - Timeouts when using `App\Service\AbstractApiService` class on behalf of APIZ.
- `VERIFY_SSL` - SSL Verification when using `App\Service\AbstractApiService` class on behalf of APIZ.

## What if I don't want to use a few services?

### NewRelic
- In `bootstrap/app.php`, change

```php
$app->instance(Illuminate\Contracts\Debug\ExceptionHandler::class,
    new Nord\Lumen\ChainedExceptionHandler\ChainedExceptionHandler(new App\Exceptions\Handler(), [
        new Nord\Lumen\NewRelic\NewRelicExceptionHandler(),
    ]));
```
to
```php
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);
```

- In `bootstrap/app.php`, remove `Nord\Lumen\NewRelic\NewRelicMiddleware::class` from `$app->middleware()` array.
- In `bootstrap/app.php`, comment `$app->register(Nord\Lumen\NewRelic\NewRelicServiceProvider::class);`.
- Remove `nordsoftware/lumen-newrelic` from `composer.json`.

### Sentry
- In `bootstrap/app.php`, remove the following block.

```php
if (report_to_sentry()) {
    $app->register(Sentry\Laravel\ServiceProvider::class);
}
```

- In `app\Exceptions\Handler.php` remove the following.
```php
if (report_to_sentry() && app()->bound('sentry') && $this->shouldReport($exception)) {
    app('sentry')->captureException($exception);
}
```
- Remove `sentry/sentry-laravel` from your `composer.json`.

### CORS
- In `bootstrap/app.php`, comment `$app->configure('cors');`
- In `bootstrap/app.php`, remove `Nord\Lumen\Cors\CorsMiddleware::class` from `$app->middleware()` array.
- Remove `nordsoftware/lumen-cors` from your `composer.json`.

### Query logs
- In `bootstrap/app.php`, remove the following block.
```php
if (log_db_queries()) {
    $app->register(App\Providers\QueryLoggerServiceProvider::class);
    $app->middleware(App\Http\Middleware\QueryLoggerMiddleware::class);
}
```

## Keep in mind
- Use separate docker file for your **PRODUCTION**, in which you'll **copy your code** and not **mount** volumes.
- Enable `opcache` for better performance and remove `nano`, `unzip`, `zip`, `git`, `composer` in your production dockerfile.
- Change `docker/php/conf.ini` according to your need.
- The `app` service in `docker-compose` file, `app.build.args` takes NewRelic information. You can use it to install and integrate newrelic in your application. Only installs if the `app.build.args.ENVIRONMENT` is `production`
- Don't use `Facade`s, use `app('facade-accessor')` instead.

## PRs?
It'd be great if you think I missed something or something is wrong, please send a PR.
