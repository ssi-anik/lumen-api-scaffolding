<?php

namespace App\Exceptions;

use Anik\Form\ValidationException as FormValidationException;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        AuthorizationException::class,
        UnauthorizedException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        FormValidationException::class,
    ];

    public function report (Exception $exception) {
        if (report_to_sentry() && app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    public function render ($request, Exception $exception) {
        $message = 'Something went wrong.';
        $data = [];
        $headers = [];

        switch ( true ) {
            case $exception instanceof UnauthorizedException:
                $statusCode = 401;
                $message = $exception->getMessage() ?: 'Unauthorized.';
                break;
            case $exception instanceof QueryException:
                $statusCode = 500;
                break;
            case $exception instanceof FormValidationException:
            case $exception instanceof ValidationException:
                $statusCode = 422;
                $data = $this->parseValidationErrorResponse($exception);
                break;
            case $exception instanceof ModelNotFoundException:
            case $exception instanceof NotFoundHttpException:
                $message = 'Invalid resource.';
                $statusCode = 404;
                break;
            case $exception instanceof BaseException:
                $data = $exception->getData();
                $statusCode = $exception->getHttpStatusCode();
                $message = $exception->getResponseMessage();
                break;
            case $exception instanceof MethodNotAllowedHttpException:
                $message = 'Method now allowed.';
            case $exception instanceof HttpException:
                $statusCode = $exception->getStatusCode();
                $message = !empty($message) ? $message : $exception->getMessage();
                $headers = $exception->getHeaders() ?: [];
                break;
            default:
                $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
                break;
        }

        return $this->responder(!empty($data)
            ? $data
            : [
                'error'   => true,
                'message' => $message,
            ], $statusCode, $headers);
    }

    private function responder ($data, $statusCode, array $headers = []) {
        return response()->json($data, $statusCode, $headers);
    }

    private function parseValidationErrorResponse (Exception $exception) {
        $errors = [];
        if ($exception instanceof ValidationException) {
            $errors = $exception->errors();
        } elseif ($exception instanceof FormValidationException) {
            $errors = $exception->getResponse();
        }
        $causes = [];
        foreach ( $errors as $key => $error ) {
            $causes[$key] = $error[0];
        }

        return [ 'error' => true, 'causes' => $causes ];
    }
}
