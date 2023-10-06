<?php

declare(strict_types=1);

namespace App\World\Service\Visualization;

use App\World\Model\World;

/**
 * @psalm-suppress UnusedClass
 */
interface RendererInterface
{
    public function render(World $world): void;
}
