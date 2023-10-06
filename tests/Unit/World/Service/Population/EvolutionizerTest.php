<?php

declare(strict_types=1);

namespace App\Tests\Unit\World\Service\Population;

use App\World\Model\Organism;
use App\World\Model\World;
use App\World\Service\Helper\Math;
use App\World\Service\Population\Evolutionizer;
use PHPUnit\Framework\TestCase;

class EvolutionizerTest extends TestCase
{
    public function testEvolveNoEvolution(): void
    {
        $evolutionizer = $this->createEvolutionizer();
        self::assertFalse($evolutionizer->evolve($this->createWorld()));
    }

    public function testEvolveIsolation(): void
    {
        $evolutionizer = $this->createEvolutionizer();
        $world = $this->createWorld();
        $organism = new Organism(1, 1, 'test');
        $matrix = $world->getWorld();
        $matrix[1][1] = $organism;
        $world->setWorld($matrix);
        $worldEvolved = $evolutionizer->evolve($world);
        self::assertTrue($worldEvolved);
        self::assertNull($world->getWorld()[1][1]);
    }

    public function testEvolveOvercrowded(): void
    {
        $evolutionizer = $this->createEvolutionizer();
        $world = $this->createOvercrowdedWorld();

        $worldEvolved = $evolutionizer->evolve($world);
        self::assertTrue($worldEvolved);
        self::assertNull($world->getWorld()[2][2]);
    }

    public function testEvolveSurvive(): void
    {
        $evolutionizer = $this->createEvolutionizer();
        $world = $this->createWorldWithSurvivableCell();

        $organism = $world->getWorld()[2][2];
        self::assertInstanceOf(Organism::class, $organism);

        $worldEvolved = $evolutionizer->evolve($world);
        self::assertTrue($worldEvolved);

        $organism = $world->getWorld()[2][2];
        self::assertInstanceOf(Organism::class, $organism);
        self::assertSame('test', $organism->getType());
    }

    public function testEvolveBirthEmptyCell(): void
    {
        $evolutionizer = $this->createEvolutionizer();
        $world = $this->createWorldWithCellBorn();
        self::assertNull($world->getWorld()[2][2]);

        $worldEvolved = $evolutionizer->evolve($world);
        self::assertTrue($worldEvolved);

        /** @var Organism|null $organism */ /** @psalm-suppress UnnecessaryVarAnnotation */
        $organism = $world->getWorld()[2][2];
        self::assertInstanceOf(Organism::class, $organism);
        self::assertSame('test', $organism->getType());
    }

    public function testEvolveBirthIntoOccupiedCell(): void
    {
        $evolutionizer = $this->createEvolutionizer();
        $world = $this->createWorldWithCellBornToOccupied();
        $organism = $world->getWorld()[2][2];
        self::assertInstanceOf(Organism::class, $organism);
        self::assertSame('test2', $organism->getType());

        $worldEvolved = $evolutionizer->evolve($world);
        self::assertTrue($worldEvolved);

        $organism = $world->getWorld()[2][2];
        self::assertInstanceOf(Organism::class, $organism);
        self::assertSame('test', $organism->getType());
    }

    public function testEvolveBirthConflict(): void
    {
        $evolutionizer = $this->createEvolutionizer();
        $world = $this->createWorldWithBornConflict();
        $organism = $world->getWorld()[2][2];
        self::assertInstanceOf(Organism::class, $organism);
        self::assertSame('test2', $organism->getType());

        $worldEvolved = $evolutionizer->evolve($world);
        self::assertTrue($worldEvolved);

        $organism = $world->getWorld()[2][2];
        self::assertInstanceOf(Organism::class, $organism);
        self::assertContains($organism->getType(), ['test', 'test3']);
    }

    private function createWorld(): World
    {
        $emptyMatrix = [
            1 => [1 => null, 2 => null, 3 => null],
            2 => [1 => null, 2 => null, 3 => null],
            3 => [1 => null, 2 => null, 3 => null],
        ];

        return new World(3, 2, 20, $emptyMatrix);
    }

    private function createOvercrowdedWorld(): World
    {
        $organismMiddle = new Organism(2, 2, 'test');
        $organismTop1 = new Organism(1, 1, 'test');
        $organismTop2 = new Organism(1, 2, 'test');
        $organismTop3 = new Organism(1, 3, 'test');
        $organismBottom = new Organism(3, 2, 'test');

        $worldMatrix = [
            1 => [1 => $organismTop1, 2 => $organismTop2, 3 => $organismTop3],
            2 => [1 => null, 2 => $organismMiddle, 3 => null],
            3 => [1 => null, 2 => null, 3 => $organismBottom],
        ];

        return new World(3, 2, 20, $worldMatrix);
    }

    private function createWorldWithCellBorn(): World
    {
        $organismBotton = new Organism(2, 3, 'test');
        $organismTop1 = new Organism(1, 1, 'test');
        $organismTop2 = new Organism(1, 2, 'test');

        $worldMatrix = [
            1 => [1 => $organismTop1, 2 => $organismTop2, 3 => null],
            2 => [1 => null, 2 => null, 3 => null],
            3 => [1 => null, 2 => $organismBotton, 3 => null],
        ];

        return new World(3, 2, 20, $worldMatrix);
    }

    private function createWorldWithCellBornToOccupied(): World
    {
        $organismBotton = new Organism(2, 3, 'test');
        $organismTop1 = new Organism(1, 1, 'test');
        $organismTop2 = new Organism(1, 2, 'test');
        $organismMiddle = new Organism(1, 2, 'test2');

        $worldMatrix = [
            1 => [1 => $organismTop1, 2 => $organismTop2, 3 => null],
            2 => [1 => null, 2 => $organismMiddle, 3 => null],
            3 => [1 => null, 2 => $organismBotton, 3 => null],
        ];

        return new World(3, 2, 20, $worldMatrix);
    }

    private function createWorldWithBornConflict(): World
    {
        $organismTop1 = new Organism(1, 1, 'test');
        $organismTop2 = new Organism(1, 2, 'test');
        $organismTop3 = new Organism(1, 3, 'test');
        $organismMiddle = new Organism(1, 2, 'test2');
        $organismBottom1 = new Organism(3, 1, 'test3');
        $organismBottom2 = new Organism(3, 2, 'test3');
        $organismBottom3 = new Organism(3, 3, 'test3');

        $worldMatrix = [
            1 => [1 => $organismTop1, 2 => $organismTop2, 3 => $organismTop3],
            2 => [1 => null, 2 => $organismMiddle, 3 => null],
            3 => [1 => $organismBottom1, 2 => $organismBottom2, 3 => $organismBottom3],
        ];

        return new World(3, 2, 20, $worldMatrix);
    }

    private function createWorldWithSurvivableCell(): World
    {
        $organismMiddle = new Organism(2, 2, 'test');
        $organismTop1 = new Organism(1, 1, 'test');
        $organismTop2 = new Organism(1, 2, 'test');

        $worldMatrix = [
            1 => [1 => $organismTop1, 2 => $organismTop2, 3 => null],
            2 => [1 => null, 2 => $organismMiddle, 3 => null],
            3 => [1 => null, 2 => null, 3 => null],
        ];

        return new World(3, 2, 20, $worldMatrix);
    }

    private function createEvolutionizer(): Evolutionizer
    {
        return new Evolutionizer(new Math());
    }
}
