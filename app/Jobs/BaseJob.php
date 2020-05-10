<?php

namespace App\Jobs;

abstract class BaseJob extends Job
{
    public $tries;
    protected $tag;

    protected function tag () {
        return $this->tag ?: static::class;
    }

    protected function logger ($data, $level = 'debug') {
        $tag = $this->tag();
        $writable = [
            $tag => is_array($data) ? $data : (array) $data,
        ];
        custom_logger($writable, $level);
    }
}