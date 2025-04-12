<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidTimezoneException extends ConversionException
{
    public static function forNonLiteralNode(string $nodeClass, string $functionName): self
    {
        return new self(\sprintf(
            'The timezone parameter for %s must be a string literal, got %s',
            $functionName,
            $nodeClass
        ));
    }

    public static function forInvalidTimezone(string $timezone, string $functionName): self
    {
        return new self(\sprintf(
            'Invalid timezone "%s" provided for %s. Must be a valid PHP timezone identifier.',
            $timezone,
            $functionName
        ));
    }
}
