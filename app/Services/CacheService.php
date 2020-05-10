<?php

namespace App\Services;

use Illuminate\Contracts\Cache\Repository;

class CacheService
{
    private $repository;

    public function __construct (Repository $repository) {
        $this->repository = $repository;
    }

    private function keyResolver (string $specifier, ...$values) : string {
        return sprintf($specifier, ...$values);
    }
}