<?php

declare(strict_types=1);

namespace App\Tests\Unit\World\Model;

use App\World\Model\Organism;
use App\World\Model\World;
use PHPUnit\Framework\TestCase;

class WorldTest extends TestCase
{
    public function testIsEmptyCell(): void
    {
        $world = [];
        $world[1][1] = null;
        $isEmpty = World::isCellEmpty(1, 1, $world);
        self::assertTrue($isEmpty);

        $world[1][1] = new Organism(1, 1, 'test');
        $isEmpty = World::isCellEmpty(1, 1, $world);
        self::assertFalse($isEmpty);
    }

    public function testIsCellWithSameOrganism(): void
    {
        $world = [];
        $world[1][1] = new Organism(1, 1, 'test');
        $isSameType = World::isCellWithSameOrganism(
            1,
            1,
            $world,
            new Organism(1, 1, 'test')
        );
        self::assertTrue($isSameType);

        $isSameType = World::isCellWithSameOrganism(
            1,
            1,
            $world,
            new Organism(1, 1, 'no-test')
        );
        self::assertFalse($isSameType);
    }
}
