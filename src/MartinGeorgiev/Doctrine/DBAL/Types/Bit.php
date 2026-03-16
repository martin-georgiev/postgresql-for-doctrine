<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitForPHPException;

/**
 * Implementation of PostgreSQL BIT data type.
 *
 * @see https://www.postgresql.org/docs/current/datatype-bit.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Bit extends BaseType
{
    protected const TYPE_NAME = 'bit';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'BIT';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidBitForPHPException::forInvalidType($value);
        }

        if (!\preg_match('/^[01]+$/', $value)) {
            throw InvalidBitForPHPException::forInvalidFormat($value);
        }

        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidBitForDatabaseException::forInvalidType($value);
        }

        if (!\preg_match('/^[01]+$/', $value)) {
            throw InvalidBitForDatabaseException::forInvalidFormat($value);
        }

        return $value;
    }
}
