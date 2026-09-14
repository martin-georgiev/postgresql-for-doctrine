<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\PostgresDateTimeConversionTrait;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateTimeInfinity;

/**
 * Base class for the PostgreSQL datetime types (DATE, TIMESTAMP, TIMESTAMPTZ).
 *
 * Values are \DateTimeImmutable or DateTimeInfinity instances, matching the items of the array counterparts.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseDateTime extends BaseType
{
    use PostgresDateTimeConversionTrait;

    abstract protected function throwInvalidDatabaseTypeException(mixed $value): never;

    abstract protected function throwInvalidPHPTypeException(mixed $value): never;

    /**
     * @throws ConversionException
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInfinity) {
            return $value->value;
        }

        if (!$value instanceof \DateTimeInterface) {
            $this->throwInvalidDatabaseTypeException($value);
        }

        return $this->transformDateTimeForPostgres($value);
    }

    /**
     * @throws ConversionException
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): \DateTimeImmutable|DateTimeInfinity|null
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            $this->throwInvalidPHPTypeException($value);
        }

        return $this->transformPostgresStringForPHP($value);
    }
}
