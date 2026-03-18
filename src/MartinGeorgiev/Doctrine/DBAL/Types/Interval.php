<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntervalForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntervalForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Interval as IntervalValueObject;

/**
 * Implementation of PostgreSQL INTERVAL data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html#DATATYPE-INTERVAL-INPUT
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Interval extends BaseType
{
    protected const TYPE_NAME = Type::INTERVAL;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'INTERVAL';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?IntervalValueObject
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidIntervalForPHPException::forInvalidType($value);
        }

        try {
            return IntervalValueObject::fromString($value);
        } catch (\Throwable) {
            throw InvalidIntervalForPHPException::forInvalidFormat($value);
        }
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if (\is_string($value)) {
            $originalString = $value;
            try {
                $value = IntervalValueObject::fromString($value);
            } catch (\Throwable) {
                throw InvalidIntervalForDatabaseException::forInvalidFormat($originalString);
            }
        }

        if ($value instanceof \DateInterval) {
            $value = IntervalValueObject::fromDateInterval($value);
        }

        if ($value instanceof IntervalValueObject) {
            return (string) $value;
        }

        throw InvalidIntervalForDatabaseException::forInvalidType($value);
    }
}
