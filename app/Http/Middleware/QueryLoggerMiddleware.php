<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Database\Events\QueryExecuted;

class QueryLoggerMiddleware
{
    static $records = [];
    private $file = null;

    public function handle ($request, Closure $next) {
        $url = $request->url();
        $method = $request->method();
        $queries = $request->query();

        $now = Carbon::now();

        $name = 'queries.log';
        if (config('settings.enable_query_log') === 'daily') {
            $name = sprintf('queries-%s.log', $now->toDateString());
        }

        $this->file = storage_path(sprintf('logs/%s', $name));

        static::$records[] = sprintf('========================== %s: %s =============================', $method, $url);
        static::$records[] = sprintf('Request on [%s] - Query params: %s', $now->toDateTimeString(),
            json_encode($queries));

        app('db')->listen(function (QueryExecuted $query) use ($now) {
            $sqlWithPlaceholders = str_replace([ '%', '?' ], [ '%%', '%s' ], $query->sql);

            $bindings = $query->connection->prepareBindings($query->bindings);
            $pdo = $query->connection->getPdo();
            $realSql = vsprintf($sqlWithPlaceholders, array_map([ $pdo, 'quote' ], $bindings));
            $duration = $this->formatDuration($query->time / 1000);

            static::$records[] = sprintf("[%s] [%'.12s] %s", $now->toDateTimeString(), $duration, $realSql);
        });

        return $next($request);
    }

    private function formatDuration ($seconds) {
        if ($seconds < 0.001) {
            return round($seconds * 1000000) . 'μs';
        } elseif ($seconds < 1) {
            return round($seconds * 1000, 2) . 'ms';
        }

        return round($seconds, 2) . 's';
    }

    public function terminate ($request, $response) {
        static::$records[] = '======================================= END =======================================' . PHP_EOL;
        // log only if records count > 3, cause 3 records are pushed as separator
        count(static::$records) > 3 ? app('files')->append($this->file, implode(PHP_EOL, static::$records)) : null;
    }
}
