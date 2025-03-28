<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddrForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddrForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\MacaddrValidationTrait;

/**
 * Implementation of PostgreSQL MACADDR data type.
 *
 * @see https://www.postgresql.org/docs/current/datatype-net-types.html#DATATYPE-MACADDR
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Macaddr extends BaseType
{
    use MacaddrValidationTrait;

    protected const TYPE_NAME = 'macaddr';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidMacaddrForPHPException::forInvalidType($value);
        }

        if (!$this->isValidMacAddress($value)) {
            throw InvalidMacaddrForPHPException::forInvalidFormat($value);
        }

        return $this->normalizeFormat($value);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidMacaddrForDatabaseException::forInvalidType($value);
        }

        if (!$this->isValidMacAddress($value)) {
            throw InvalidMacaddrForDatabaseException::forInvalidFormat($value);
        }

        return $value;
    }
}
