<?php

declare(strict_types=1);

namespace App\World\Service\Input;

interface ValidatorInterface
{
    public function validate(string $inputFilePath): bool;
}
