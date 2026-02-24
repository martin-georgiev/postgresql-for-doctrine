<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMultirangeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMultirangeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Multirange;

/**
 * Base class for PostgreSQL multirange types.
 *
 * @template M of Multirange
 *
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseMultirangeType extends BaseType
{
    /**
     * @param M|null $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Multirange) {
            throw InvalidMultirangeForDatabaseException::forInvalidType($value);
        }

        return (string) $value;
    }

    /**
     * @param string|null $value
     *
     * @return M|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Multirange
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidMultirangeForPHPException::forInvalidType($value);
        }

        if ($value === '') {
            return null;
        }

        try {
            return $this->createFromString($value);
        } catch (\InvalidArgumentException) {
            throw InvalidMultirangeForPHPException::forInvalidFormat($value);
        }
    }

    /**
     * @return M
     */
    abstract protected function createFromString(string $value): Multirange;
}
