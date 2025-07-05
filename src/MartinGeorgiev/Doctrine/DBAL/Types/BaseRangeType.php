<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidRangeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidRangeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;

/**
 * Base class for PostgreSQL range types.
 *
 * @template T of Range
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseRangeType extends BaseType
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Range) {
            throw InvalidRangeForDatabaseException::forInvalidType($value);
        }

        return (string) $value;
    }

    /**
     * @param mixed $value
     *
     * @return T|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Range
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidRangeForPHPException::forInvalidType($value);
        }

        if ($value === '') {
            return null;
        }

        try {
            return $this->createFromString($value);
        } catch (\InvalidArgumentException) {
            throw InvalidRangeForPHPException::forInvalidFormat($value);
        }
    }

    /**
     * @return T
     */
    abstract protected function createFromString(string $value): Range;
}
