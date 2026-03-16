<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitVaryingForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitVaryingForPHPException;

/**
 * Implementation of PostgreSQL BIT VARYING data type.
 *
 * @see https://www.postgresql.org/docs/current/datatype-bit.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class BitVarying extends BaseType
{
    protected const TYPE_NAME = 'bit varying';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'BIT VARYING';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidBitVaryingForPHPException::forInvalidType($value);
        }

        if (!\preg_match('/^[01]+$/', $value)) {
            throw InvalidBitVaryingForPHPException::forInvalidFormat($value);
        }

        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidBitVaryingForDatabaseException::forInvalidType($value);
        }

        if (!\preg_match('/^[01]+$/', $value)) {
            throw InvalidBitVaryingForDatabaseException::forInvalidFormat($value);
        }

        return $value;
    }
}
