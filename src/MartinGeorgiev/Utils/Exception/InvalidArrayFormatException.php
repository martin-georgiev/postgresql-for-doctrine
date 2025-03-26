<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils\Exception;

class InvalidArrayFormatException extends \InvalidArgumentException
{
    public static function multiDimensionalArrayNotSupported(): self
    {
        return new self('Only single-dimensioned arrays are supported');
    }

    public static function invalidFormat(string $details = ''): self
    {
        $message = 'Invalid array format';
        if ($details !== '') {
            $message .= ': '.$details;
        }

        return new self($message);
    }
}
