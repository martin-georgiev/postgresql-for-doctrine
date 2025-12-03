<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateTrunc;

/**
 * @since 3.7
 *
 * @author Jan Klan <jan@klan.com.au>
 */
class InvalidTruncFieldException extends ConversionException
{
    public static function forNonLiteralNode(string $nodeClass, string $functionName): self
    {
        return new self(\sprintf(
            'The date_trunc field parameter for %s must be a string literal, got %s',
            $functionName,
            $nodeClass
        ));
    }

    public static function forInvalidField(string $field, string $functionName): self
    {
        return new self(\sprintf(
            'Invalid field value "%s" provided for %s. Must be one of: %s.',
            $field,
            $functionName,
            \implode(', ', DateTrunc::FIELDS)
        ));
    }
}
