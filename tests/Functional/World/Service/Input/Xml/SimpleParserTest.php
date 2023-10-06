<?php

declare(strict_types=1);

namespace App\Tests\Functional\World\Service\Input\Xml;

use App\World\Model\Organism;
use App\World\Service\Input\Xml\SimpleParser;
use App\World\Service\Population\Initializer;
use InvalidArgumentException;
use OutOfBoundsException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SimpleParserTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testParseSuccess(): void
    {
        $parser = $this->getSimpleParser();
        $world = $parser->parse(__DIR__ . '/fixtures/world-valid.xml');

        self::assertSame(14, $world->getDimension());
        self::assertSame(3, $world->getSpeciesCount());
        self::assertSame(20000, $world->getIterations());

        $organism = $world->getWorld()[1][1];
        self::assertInstanceOf(Organism::class, $organism);
        self::assertSame('veverka', $organism->getType());
    }

    public function testParseNoXml(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Line: 0, Message: failed to load external entity "invalid.xml"');

        $parser = $this->getSimpleParser();
        $parser->parse('invalid.xml');
    }

    public function testParseInvalidOrganismPositionsWithValidationOn(): void
    {
        $expMsg = 'There is an organism with invalid positions. ';
        $expMsg .= 'x: 50, y: 2, type: veverka. Dimension is only 14';

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage($expMsg);
        $parser = new SimpleParser($this->getInitializer(), true);
        $parser->parse(__DIR__ . '/fixtures/world-invalid-position.xml');
    }

    public function testParseInvalidOrganismPositionsWithValidationOff(): void
    {
        $parser = new SimpleParser($this->getInitializer(), false);
        $world = $parser->parse(__DIR__ . '/fixtures/world-invalid-position.xml');

        self::assertSame(14, $world->getDimension());
    }

    private function getSimpleParser(): SimpleParser
    {
        /** @var SimpleParser $parser */ /** @psalm-suppress UnnecessaryVarAnnotation */
        $parser = static::getContainer()->get(SimpleParser::class);
        return $parser;
    }

    private function getInitializer(): Initializer
    {
        /** @var Initializer $initializer */ /** @psalm-suppress UnnecessaryVarAnnotation */
        $initializer = static::getContainer()->get(Initializer::class);
        return $initializer;
    }
}
