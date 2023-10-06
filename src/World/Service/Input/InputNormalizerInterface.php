<?php

declare(strict_types=1);

namespace App\World\Service\Input;

use App\World\Model\World;

interface InputNormalizerInterface
{
    public function normalize(string $inputFilePath): World;
}
