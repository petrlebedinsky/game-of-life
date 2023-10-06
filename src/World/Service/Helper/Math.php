<?php

declare(strict_types=1);

namespace App\World\Service\Helper;

class Math
{
    public function createRandomInt(int $min, int $max): int
    {
        return random_int($min, $max);
    }
}
