<?php

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

if (!function_exists('in_environment')) {
    function in_environment () {
        $envs = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();
        $runningIn = config('app.env');

        // check if exactly as it is
        if (in_array($runningIn, $envs)) {
            return true;
        }

        // check if starts with i.e; prod as production
        foreach ( $envs as $env ) {
            if (Str::startsWith(strtolower($runningIn), strtolower($env))) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('is_production')) {
    function is_production () {
        return in_environment('prod');
    }
}

if (!function_exists('is_local')) {
    function is_local () {
        return in_environment('local');
    }
}

if (!function_exists('report_to_sentry')) {
    function report_to_sentry () {
        return config('settings.report_to_sentry');
    }
}

if (!function_exists('log_db_queries')) {
    function log_db_queries () {
        return config('settings.enable_query_log');
    }
}

if (!function_exists('auth')) {
    /**
     * @param string|null $guard
     *
     * @return \Illuminate\Contracts\Auth\Factory|\Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    function auth ($guard = null) {
        if (is_null($guard)) {
            return app(AuthFactory::class);
        }

        return app(AuthFactory::class)->guard($guard);
    }
}

if (!function_exists('generate_random_digits')) {
    function generate_random_digits ($length = 6) {
        $generator = '1357902468';

        $result = '';
        for ( $i = 1; $i <= $length; $i++ ) {
            $result .= substr($generator, (rand() % (strlen($generator))), 1);
        }

        return $result;
    }
}

if (!function_exists('should_log_request_info')) {
    function should_log_request_info () {
        return config('settings.service.log_request', false);
    }
}

if (!function_exists('custom_logger')) {
    function custom_logger (array $data, $level = 'debug', $channels = '') {
        $defaultChannel = env('LOG_CHANNEL', 'single');
        if (empty($channels)) {
            $channels = $defaultChannel;
        }

        // if channels passed as array, we'll also push to the default channel
        if (!is_array($channels)) {
            $channels = [ $channels ];
        } else {
            // if default doesn't exist, push default.
            if (!in_array($defaultChannel, $channels)) {
                $channels[] = $defaultChannel;
            }
        }

        app('log')->stack($channels)->$level(json_encode($data));
    }
}

if (!function_exists('get_uuid')) {
    function get_uuid () {
        return app('request')->header('x-correlation-id') ?: (string) Uuid::uuid4();
    }
}

if (!function_exists('public_path')) {
    function public_path ($path = '') {
        return app()->make('path.public') . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }
}