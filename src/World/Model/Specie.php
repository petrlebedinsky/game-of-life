<?php

declare(strict_types=1);

namespace App\World\Model;

class Specie
{
    public function __construct(
        private readonly string $type
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }
}
