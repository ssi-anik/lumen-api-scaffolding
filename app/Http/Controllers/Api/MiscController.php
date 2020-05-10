<?php

namespace App\Http\Controllers\Api;

use Throwable;

class MiscController extends ApiController
{
    public function index () {
        return [
            'app'       => 'lumen-application',
            'framework' => app()->version(),
            'route'     => 'api',
        ];
    }

    public function health () {
        $causes = [];
        try {
            app('cache')->connection();
        } catch ( Throwable $e ) {
            $causes[] = 'cache';
        }

        try {
            app('db')->connection()->getPdo();
        } catch ( Throwable $e ) {
            $causes[] = 'database';
        }

        return $this->respondSuccess([
            'error'     => !empty($causes),
            'condition' => empty($causes) ? 'All is well!' : 'Feeling a bit down!',
            'causes'    => $causes,
        ], !empty($causes) ? 500 : 200);
    }
}