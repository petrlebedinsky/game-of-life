<?php

declare(strict_types=1);

namespace App\World\Service\Input;

use App\World\Model\World;

interface ParserInterface
{
    public function parse(string $filePath): World;
}
