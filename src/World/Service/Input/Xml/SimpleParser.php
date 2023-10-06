<?php

declare(strict_types=1);

namespace App\World\Service\Input\Xml;

use App\World\Model\Organism;
use App\World\Model\World;
use App\World\Service\Input\ParserInterface;
use App\World\Service\Population\Initializer;
use InvalidArgumentException;
use OutOfBoundsException;

class SimpleParser implements ParserInterface
{
    use ErrorMessageTrait;

    public function __construct(
        private readonly Initializer $initializer,
        private readonly bool $validateOrganismPositions = true,
    ) {
    }

    //@TODO: add more resource friendly implementation
    public function parse(string $filePath): World
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($filePath);

        if ($xml === false) {
            throw new InvalidArgumentException($this->createValidationMessage());
        }

        /** @var mixed[] $worldData */
        $worldData = json_decode(
            json_encode($xml, JSON_THROW_ON_ERROR),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        return $this->createAndInitializeWorld($worldData);
    }

    /**
     * @param mixed[] $worldData
     */
    private function createAndInitializeWorld(array $worldData): World
    {
        $dimension = (int) ($worldData['world']['dimension'] ?? 0);
        $speciesCount = (int) ($worldData['world']['speciesCount'] ?? 0);
        $iterations = (int) ($worldData['world']['iterationsCount'] ?? 0);
        /** @var array{organism: array{x_pos: string, y_pos: string, species: string}} $organismsData */
        $organismsData = isset($worldData['organisms']['organism']['x_pos'])
            ? ['organism' => [$worldData['organisms']['organism']]]
            : $worldData['organisms'];

        $organisms = [];

        /** @var array{x_pos: string, y_pos: string, species: string} $organismData */
        foreach ($organismsData['organism'] as $organismData) {
            $organism = new Organism(
                (int) $organismData['x_pos'],
                (int) $organismData['y_pos'],
                $organismData['species'],
            );

            if ($this->haveOrganismValidPosition($organism, $dimension) === false) {
                throw new OutOfBoundsException(sprintf(
                    'There is an organism with invalid positions. x: %d, y: %d, type: %s. Dimension is only %d',
                    $organism->getXPosition(),
                    $organism->getYPosition(),
                    $organism->getType(),
                    $dimension,
                ));
            }

            $organisms[] = $organism;
        }

        $world = new World($dimension, $speciesCount, $iterations);
        $this->initializer->populateWorld($organisms, $world);

        return $world;
    }

    private function haveOrganismValidPosition(Organism $organism, int $dimension): bool
    {
        if ($this->validateOrganismPositions === false) {
            return true;
        }

        return ($organism->getXPosition() <= $dimension && $organism->getYPosition() <= $dimension);
    }
}
