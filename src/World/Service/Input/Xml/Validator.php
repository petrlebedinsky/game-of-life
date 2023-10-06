<?php

declare(strict_types=1);

namespace App\World\Service\Input\Xml;

use App\World\Service\Input\ValidatorInterface;
use DOMDocument;
use InvalidArgumentException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class Validator implements ValidatorInterface
{
    use ErrorMessageTrait;

    public function __construct(private readonly string $xsdFilePath)
    {
    }

    public function validate(string $inputFilePath): bool
    {
        if (!file_exists($inputFilePath)) {
            throw new InvalidArgumentException(sprintf(
                'World input XML file %s does not exist!',
                $inputFilePath,
            ));
        }

        if (!file_exists($this->xsdFilePath)) {
            throw new InvalidConfigurationException(sprintf(
                'XSD schema file %s does not exist',
                $this->xsdFilePath,
            ));
        }

        libxml_use_internal_errors(true);

        $xml = new DOMDocument();
        $xml->load($inputFilePath);

        if (!$xml->schemaValidate($this->xsdFilePath)) {
            throw new InvalidArgumentException($this->createValidationMessage());
        }

        return true;
    }
}
