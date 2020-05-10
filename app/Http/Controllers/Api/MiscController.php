<?php

namespace App\Http\Controllers\Api;

class MiscController extends ApiController
{
    public function index () {
        return [
            'app'       => 'lumen-application',
            'framework' => app()->version(),
            'route'     => 'api',
        ];
    }
}