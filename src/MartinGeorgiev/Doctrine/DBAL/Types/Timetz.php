<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimetzForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimetzForPHPException;

/**
 * Implementation of PostgreSQL timetz (time with time zone) data type.
 *
 * Stores a time-of-day value with time zone offset. The PHP representation
 * is a string in the format returned by PostgreSQL (e.g. "12:34:56+02:00").
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Timetz extends BaseType
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::TIMETZ;

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidTimetzForDatabaseException::forInvalidType($value);
        }

        if ($value === '') {
            throw InvalidTimetzForDatabaseException::forInvalidFormat($value);
        }

        return $value;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidTimetzForPHPException::forInvalidType($value);
        }

        return $value;
    }
}
