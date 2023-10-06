<?php

declare(strict_types=1);

namespace App\World\Service\Input\Xml;

trait ErrorMessageTrait
{
    protected function createValidationMessage(): string
    {
        $message = '';
        foreach (libxml_get_errors() as $error) {
            $message .= sprintf(
                'Line: %d, Message: %s',
                $error->line,
                $error->message,
            );
        }
        return $message;
    }
}
