<?php

namespace App\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    protected $responseMessage, $httpStatusCode, $data;

    public function __construct ($message = "Exception raised", $code = 400, $data = []) {
        parent::__construct($message, $code);
        $this->setResponseMessage($message)->setHttpStatusCode($code)->setData($data);
    }

    public function setData ($data) {
        $this->data = $data;

        return $this;
    }

    public function getData () {
        return $this->data;
    }

    public function getHttpStatusCode () {
        return $this->httpStatusCode;
    }

    public function setHttpStatusCode ($code) {
        $this->httpStatusCode = $code;

        return $this;
    }

    public function getResponseMessage () {
        return $this->responseMessage;
    }

    public function setResponseMessage ($message) {
        $this->responseMessage = $message;

        return $this;
    }
}
