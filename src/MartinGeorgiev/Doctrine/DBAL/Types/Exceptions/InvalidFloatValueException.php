<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidFloatValueException extends ConversionException
{
    public static function forValueThatIsNotAValidPHPFloat(mixed $value): self
    {
        return new self(\sprintf("Given value of %s content cannot be transformed to valid PHP float.", \var_export($value, true)));
    }

    public static function forValueThatIsTooCloseToZero(mixed $value, string $type): self
    {
        return new self(sprintf("Given value of %s is too close to zero for PostgreSQL %s type", var_export($value, true), $type));
    }

    public static function forValueThatExceedsMaximumPrecision(mixed $value, int $maxPrecision, string $type): self
    {
        return new self(\sprintf("Given value of %s exceeds maximum precision of %d for PostgreSQL %s type ", \var_export($value, true), $maxPrecision, $type));
    }
}
