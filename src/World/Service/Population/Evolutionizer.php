<?php

declare(strict_types=1);

namespace App\World\Service\Population;

use App\World\Model\Organism;
use App\World\Model\World;
use App\World\Service\Helper\Math;

class Evolutionizer
{
    public function __construct(private readonly Math $mathHelper)
    {
    }
    public function evolve(World $world): bool
    {
        /** @var (Organism|null)[][] $nextGenWorld */
        $nextGenWorld = [];
        $changed = false;

        for ($x = 1; $x <= $world->getDimension(); $x++) {
            for ($y = 1; $y <= $world->getDimension(); $y++) {
                $cellChanged = $this->evolveCell($x, $y, $world, $nextGenWorld);
                if ($changed === false && $cellChanged === true) {
                    $changed = true;
                }
            }
        }

        $world->setWorld($nextGenWorld);
        return $changed;
    }

    /**
     * @param (Organism|null)[][] $nextGenWorld
     */
    private function evolveCell(int $x, int $y, World $world, array &$nextGenWorld): bool
    {
        $cell = $world->getWorld()[$x][$y];
        $surroundings = $this->extractSurroundings($x, $y, $world);
        $newCell = $this->applyLifeRules($x, $y, $cell, $surroundings);

        if ($newCell === null) {
            $nextGenWorld[$x][$y] = null;
        } else {
            $nextGenWorld[$x][$y] = $newCell;
        }

        return $cell !== $newCell;
    }

    /**
     * @param array<string, int> $surroundings
     */
    private function applyLifeRules(
        int $x,
        int $y,
        ?Organism $organism,
        array $surroundings
    ): ?Organism {
        $newOrganism = $organism !== null ? clone $organism : null;
        if ($this->isCellOverCrowded($newOrganism, $surroundings)) {
            $newOrganism = null;
        } elseif ($this->isCellIsolated($newOrganism, $surroundings)) {
            $newOrganism = null;
        } elseif ($this->canCellSurvive($newOrganism, $surroundings)) {
            $newOrganism = $organism;
        }

        $newBornOrganismType = $this->newOrganismBorn($surroundings);
        if ($newBornOrganismType !== null) {
            $newOrganism = new Organism($x, $y, $newBornOrganismType);
        }

        return $newOrganism;
    }

    /**
     * @param array<string, int> $surroundings
     */
    private function isCellOverCrowded(?Organism $organism, array $surroundings): bool
    {
        return ($organism !== null &&
            isset($surroundings[$organism->getType()]) &&
            $surroundings[$organism->getType()] >= 4
        );
    }

    /**
     * @param array<string, int> $surroundings
     */
    private function isCellIsolated(?Organism $organism, array $surroundings): bool
    {
        return ($organism !== null &&
            (!isset($surroundings[$organism->getType()]) ||
            /** @phpstan-ignore-next-line */
            (isset($surroundings[$organism->getType()]) && $surroundings[$organism->getType()] < 2))
        );
    }

    /**
     * @param array<string, int> $surroundings
     */
    private function canCellSurvive(?Organism $organism, array $surroundings): bool
    {
        return ($organism !== null &&
            isset($surroundings[$organism->getType()]) &&
            $surroundings[$organism->getType()] >= 2 &&
            $surroundings[$organism->getType()] <= 3
        );
    }

    /**
     * @param array<string, int> $surroundings
     */
    private function newOrganismBorn(array $surroundings): ?string
    {
        $typesToBeBorn = array_keys($surroundings, 3, true);
        if (count($typesToBeBorn) === 0) {
            return null;
        } elseif (count($typesToBeBorn) === 1) {
            return $typesToBeBorn[0];
        } else {
            $randomTypeIndex = $this->mathHelper->createRandomInt(0, count($typesToBeBorn) - 1);
            return $typesToBeBorn[$randomTypeIndex];
        }
    }

    /**
     * @return array<string, int>
     */
    private function extractSurroundings(int $x, int $y, World $world): array
    {
        /** @var array<string, int> $surroundings */
        $surroundings = [];

        for ($xOffset = -1; $xOffset <= 1; $xOffset++) {
            for ($yOffset = -1; $yOffset <= 1; $yOffset++) {
                if ($xOffset === 0 && $yOffset === 0) {
                    continue;
                }
                $this->extractSurroundingCell($x + $xOffset, $y + $yOffset, $surroundings, $world);
            }
        }

        return $surroundings;
    }

    /** @param array<string, int> $surroundings */
    private function extractSurroundingCell(int $x, int $y, array &$surroundings, World $world): void
    {
        if ($x < 1 || $y < 1 || $x > $world->getDimension() || $y > $world->getDimension()) {
            return;
        }

        $surroundingCell = $world->getWorld()[$x][$y];

        if ($surroundingCell === null) {
            return;
        }

        if (isset($surroundings[$surroundingCell->getType()])) {
            $surroundings[$surroundingCell->getType()] += 1;
        } else {
            $surroundings[$surroundingCell->getType()] = 1;
        }
    }
}
