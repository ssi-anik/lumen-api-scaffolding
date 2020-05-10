<?php

namespace App\Jobs;

class ExampleJob extends BaseJob
{
    public function __construct () {
        //
    }

    public function handle () {
        // $this->logger([ 'attempt' => $this->attempts(), 'working' => true, ]);
    }
}
