<?php

declare(strict_types=1);

namespace App\Tests\Unit\World\Service\Population;

use App\World\Model\Organism;
use App\World\Model\World;
use App\World\Service\Helper\Math;
use App\World\Service\Population\Initializer;
use PHPUnit\Framework\TestCase;

class InitializerTest extends TestCase
{
    public function testPopulateWorld(): void
    {
        $organisms = [];
        //index out of bounds
        $organisms[] = new Organism(-1, -3, 'invalid');

        //new species
        $organisms[] = new Organism(2, 2, 'test');
        //same cell, same type
        $organisms[] = new Organism(2, 2, 'test');

        //cell conflict
        $mathMock = $this->createMock(Math::class);
        $mathMock->method('createRandomInt')->willReturn(1);

        $organisms[] = new Organism(5, 5, 'test2');
        $organisms[] = new Organism(5, 5, 'test3');

        $world = new World(5, 3, 100);
        $initializer = new Initializer($mathMock);
        $initializer->populateWorld($organisms, $world);

        self::assertArrayNotHasKey('invalid', $world->getSpecies());
        self::assertArrayHasKey('test', $world->getSpecies());
        self::assertArrayHasKey('test2', $world->getSpecies());
        self::assertArrayHasKey('test3', $world->getSpecies());
        self::assertCount(3, $world->getSpecies());

        self::assertNull($world->getWorld()[1][1]);

        $organism = $world->getWorld()[2][2];
        self::assertInstanceOf(Organism::class, $organism);
        self::assertSame('test', $organism->getType());

        $organism = $world->getWorld()[5][5];
        self::assertInstanceOf(Organism::class, $organism);
        self::assertSame('test3', $organism->getType());
    }
}
