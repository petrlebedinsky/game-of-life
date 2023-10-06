<?php

declare(strict_types=1);

namespace App\World\Service\Output;

use App\World\Model\Organism;

/**
 * @psalm-suppress UnusedClass
 */
interface OutputDenormalizerInterface
{
    /**
     * @param (Organism|null)[][] $world
     */
    public function denormalize(array $world): void;
}
