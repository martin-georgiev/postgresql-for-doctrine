<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidTimestampArrayItemForDatabaseException extends ConversionException
{
    public static function forInvalidType(mixed $value): self
    {
        return new self(\sprintf('Array values must be instances of DateTimeInterface, %s given', \var_export($value, true)));
    }
}
