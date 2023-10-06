<?php

declare(strict_types=1);

namespace App\World\Model;

class Organism
{
    public function __construct(
        private readonly int $xPosition,
        private readonly int $yPosition,
        private readonly string $type,
    ) {
    }

    public function getXPosition(): int
    {
        return $this->xPosition;
    }

    public function getYPosition(): int
    {
        return $this->yPosition;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
