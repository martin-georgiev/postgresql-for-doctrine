<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidUuidArrayItemForDatabaseException extends ConversionException
{
    public static function forInvalidFormat(mixed $value): self
    {
        return new self(\sprintf('Invalid UUID format in array: %s', \var_export($value, true)));
    }
}
