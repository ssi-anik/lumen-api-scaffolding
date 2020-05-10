<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function respondSuccess ($data, $statusCode = 200, array $headers = []) {
        return response()->json($data, $statusCode, $headers);
    }

    public function respondError ($data, $statusCode = 400, array $headers = []) {
        return response()->json($data, $statusCode, $headers);
    }
}