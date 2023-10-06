<?php

declare(strict_types=1);

namespace App\Tests\Functional\World\Service\Input;

use App\World\Model\Organism;
use App\World\Service\Input\InputNormalizer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InputNormalizerTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testNormalize(): void
    {
        /** @var InputNormalizer $inputNormalizer */ /** @psalm-suppress UnnecessaryVarAnnotation */
        $inputNormalizer = static::getContainer()->get(InputNormalizer::class);
        $world = $inputNormalizer->normalize(__DIR__ . '/Xml/fixtures/world-valid.xml');

        self::assertSame(14, $world->getDimension());
        self::assertSame(3, $world->getSpeciesCount());
        self::assertSame(20000, $world->getIterations());

        $organism = $world->getWorld()[1][2];
        self::assertInstanceOf(Organism::class, $organism);
        self::assertSame('svist', $organism->getType());
    }
}
