<?php

declare(strict_types=1);

namespace App\Tests\Functional\World\Service\Input\Xml;

use App\World\Service\Input\Xml\Validator;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class ValidatorTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testValidateSuccess(): void
    {
        $validator = $this->getXmlValidator();
        $isValid = $validator->validate(__DIR__ . '/fixtures/world-valid.xml');

        self::assertTrue($isValid);
    }

    public function testValidateInvalidXsdPath(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('XSD schema file invalid.xsd does not exist');

        $validator = new Validator('invalid.xsd');
        $validator->validate(__DIR__ . '/fixtures/world-valid.xml');
    }

    public function testValidateInvalidXmlPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('World input XML file invalid.xml does not exist!');
        $validator = $this->getXmlValidator();
        $validator->validate('invalid.xml');
    }

    //@TODO: add another test cases for schema related errors
    public function testValidateNegativeDimension(): void
    {
        $expMsg = 'Line: 4, Message: Element \'dimension\': \'-14\' is not ';
        $expMsg .= 'a valid value of the atomic type \'xs:positiveInteger\'';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expMsg);

        $validator = $this->getXmlValidator();
        $validator->validate(__DIR__ . '/fixtures/world-invalid-dimension.xml');
    }

    private function getXmlValidator(): Validator
    {
        /** @var Validator $validator */ /** @psalm-suppress UnnecessaryVarAnnotation */
        $validator = static::getContainer()->get(Validator::class);
        return $validator;
    }
}
