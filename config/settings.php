<?php

return [
    'enable_query_log' => env('ENABLE_QUERY_LOG', false),
    'report_to_sentry' => (bool) env('REPORT_TO_SENTRY', true),

    'service' => [
        'response_timeout'   => env('SERVICE_RESPONSE_TIMEOUT', 5),
        'connection_timeout' => env('SERVICE_CONNECTION_TIMEOUT', 5),
        'verify_ssl'         => env('VERIFY_SSL', false),
        'log_request'        => (bool) env('LOG_HTTP_REQUEST', false),
    ],
];