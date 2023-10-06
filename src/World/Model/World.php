<?php

declare(strict_types=1);

namespace App\World\Model;

class World
{
    /** @var Specie[] */
    private array $species = [];

    /** @param (Organism|null)[][] $world */
    public function __construct(
        private readonly int $dimension,
        private readonly int $speciesCount,
        private readonly int $iterations,
        private array $world = [],
    ) {
    }

    public function getSpeciesCount(): int
    {
        return $this->speciesCount;
    }

    public function getDimension(): int
    {
        return $this->dimension;
    }

    public function getIterations(): int
    {
        return $this->iterations;
    }

    /**
     * @return (Organism|null)[][]
     */
    public function getWorld(): array
    {
        return $this->world;
    }

    /**
     * @param (Organism|null)[][] $world
     */
    public function setWorld(array $world): self
    {
        $this->world = $world;
        return $this;
    }

    /**
     * @return Specie[]
     */
    public function getSpecies(): array
    {
        return $this->species;
    }

    /**
     * @param Specie[] $species
     */
    public function setSpecies(array $species): self
    {
        $this->species = $species;
        return $this;
    }

    /**
     * @param (Organism|null)[][] $world
     */
    public static function isCellWithSameOrganism(int $x, int $y, array $world, Organism $organism): bool
    {
        return (isset($world[$x][$y]) && $world[$x][$y]->getType() === $organism->getType());
    }

    /**
     * @param (Organism|null)[][] $world
     */
    public static function isCellEmpty(int $x, int $y, array &$world): bool
    {
        return !isset($world[$x][$y]);
    }
}
