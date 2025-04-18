<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidBooleanException extends ConversionException
{
    public static function forNonLiteralNode(string $nodeClass, string $functionName): self
    {
        return new self(\sprintf(
            'The boolean parameter for %s must be a string literal, got %s',
            $functionName,
            $nodeClass
        ));
    }

    public static function forInvalidBoolean(string $value, string $functionName): self
    {
        return new self(\sprintf(
            'Invalid boolean value "%s" provided for %s. Must be "true" or "false".',
            $value,
            $functionName
        ));
    }
}
