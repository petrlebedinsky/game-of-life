<?php

declare(strict_types=1);

namespace App\Tests\Unit\World\Model;

use App\World\Model\Specie;
use PHPUnit\Framework\TestCase;

class SpecieTest extends TestCase
{
    public function testSpecie(): void
    {
        $specie = new Specie('test');
        self::assertSame('test', $specie->getType());
    }
}
