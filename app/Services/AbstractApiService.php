<?php namespace App\Services;

use Apiz\AbstractApi;
use Loguzz\Formatter\RequestArrayFormatter;
use Loguzz\Formatter\ResponseArrayFormatter;

abstract class AbstractApiService extends AbstractApi
{
    public function __construct () {
        $this->options = $this->defaultOptions();

        parent::__construct();
    }

    protected function defaultOptions () {
        return [
            'timeout'         => config('settings.service.response_timeout'),
            'connect_timeout' => config('settings.service.connection_timeout'),
            'verify'          => config('settings.service.verify_ssl'),
        ];
    }

    protected function logger () {
        return should_log_request_info() ? app('log') : null;
    }

    protected function logLevel () {
        return 'info';
    }

    protected function requestFormatter () {
        return new RequestArrayFormatter();
    }

    protected function responseFormatter () {
        return new ResponseArrayFormatter();
    }

    protected function tag () {
        return get_class($this);
    }

    protected function useSeparator () {
        return true;
    }

    protected function statusNotPositive ($response, $endpoint = '', $status = 200, $is = 'ne') {
        $tag = sprintf('%s-%s-%s-%d', class_basename($this), $endpoint, $is, $status);

        custom_logger([
            $tag => [
                'code'     => $response->getStatusCode(),
                'response' => $response->getContents(),
            ],
        ], 'error');
    }

    protected function reportServiceUnresponsive ($endpoint = '', array $data = []) {
        if (empty($data)) {
            $cls = class_basename($this);
            $rTO = config('settings.service_response_timeout');
            $cTO = config('settings.service_connection_timeout');
            $msg = sprintf('\'%s\' - {%s} failed to respond: Timeout <%d(s) - conn | %d(s) - res>.', $cls, $endpoint,
                $cTO, $rTO);

            $data['message'] = $msg;
        }

        custom_logger([
            'service-unreachable' => [
                'what' => get_class($this),
                'info' => $data,
            ],
        ], 'error');
    }
}