<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInetForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInetForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\InetValidationTrait;

/**
 * Implementation of PostgreSQL INET data type.
 *
 * @see https://www.postgresql.org/docs/current/datatype-net-types.html#DATATYPE-INET
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Inet extends BaseType
{
    use InetValidationTrait;

    protected const TYPE_NAME = 'inet';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidInetForPHPException::forInvalidType($value);
        }

        if (!$this->isValidInetAddress($value)) {
            throw InvalidInetForPHPException::forInvalidFormat($value);
        }

        return $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidInetForDatabaseException::forInvalidType($value);
        }

        if (!$this->isValidInetAddress($value)) {
            throw InvalidInetForDatabaseException::forInvalidFormat($value);
        }

        return $value;
    }
}
