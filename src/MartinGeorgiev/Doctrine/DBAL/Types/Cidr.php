<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCidrForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCidrForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\CidrValidationTrait;

/**
 * Implementation of PostgreSQL CIDR data type.
 *
 * @see https://www.postgresql.org/docs/current/datatype-net-types.html#DATATYPE-CIDR
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Cidr extends BaseType
{
    use CidrValidationTrait;

    protected const TYPE_NAME = 'cidr';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidCidrForPHPException::forInvalidType($value);
        }

        if (!$this->isValidCidrAddress($value)) {
            throw InvalidCidrForPHPException::forInvalidFormat($value);
        }

        return $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidCidrForDatabaseException::forInvalidType($value);
        }

        if (!$this->isValidCidrAddress($value)) {
            throw InvalidCidrForDatabaseException::forInvalidFormat($value);
        }

        return $value;
    }
}
