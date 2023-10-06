<?php

declare(strict_types=1);

namespace App\World\Service\Input;

use App\World\Model\World;

class InputNormalizer implements InputNormalizerInterface
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly ParserInterface $parser
    ) {
    }

    public function normalize(string $inputFilePath): World
    {
        $this->validator->validate($inputFilePath);
        return $this->parser->parse($inputFilePath);
    }
}
