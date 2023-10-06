<?php

declare(strict_types=1);

namespace App\World\Service\Population;

use App\World\Model\Organism;
use App\World\Model\Specie;
use App\World\Model\World;
use App\World\Service\Helper\Math;

class Initializer
{
    public function __construct(private readonly Math $mathHelper)
    {
    }
    /**
     * @param Organism[] $organisms
     */
    public function populateWorld(array $organisms, World $world): void
    {
        $worldMatrix = $this->createEmptyMatrix($world->getDimension());
        $this->createPopulation($organisms, $worldMatrix, $world);
    }

    /**
     * @return null[][]
     */
    private function createEmptyMatrix(int $dimension): array
    {
        $world = [];
        for ($x = 1; $x <= $dimension; $x++) {
            for ($y = 1; $y <= $dimension; $y++) {
                $world[$x][$y] = null;
            }
        }

        return $world;
    }

    /**
     * @param Organism[] $organisms
     * @param (Organism|null)[][] $worldMatrix
     */
    private function createPopulation(array $organisms, array $worldMatrix, World $world): void
    {
        $species = [];
        foreach ($organisms as $organism) {
            //invalid position
            if (
                $organism->getXPosition() < 1 ||
                $organism->getYPosition() < 1 ||
                $organism->getXPosition() > $world->getDimension() ||
                $organism->getYPosition() > $world->getDimension()
            ) {
                continue;
            }

            // adding new specie
            if (isset($species[$organism->getType()]) === false) {
                $species[$organism->getType()] = new Specie($organism->getType());
            }

            // empty cell - adding new organism
            if (World::isCellEmpty($organism->getXPosition(), $organism->getYPosition(), $worldMatrix)) {
                $worldMatrix[$organism->getXPosition()][$organism->getYPosition()] = $organism;
            } elseif (
                // same species type cell - skipping
                World::isCellWithSameOrganism(
                    $organism->getXPosition(),
                    $organism->getYPosition(),
                    $worldMatrix,
                    $organism,
                )
            ) {
                continue;
            } else {
                // cell already occupied - resolve conflict
                $randomlyChangeSpecies = (bool) $this->mathHelper->createRandomInt(0, 1);
                if ($randomlyChangeSpecies === true) {
                    $worldMatrix[$organism->getXPosition()][$organism->getYPosition()] = $organism;
                }
            }
        }

        $world->setSpecies($species);
        $world->setWorld($worldMatrix);
    }
}
