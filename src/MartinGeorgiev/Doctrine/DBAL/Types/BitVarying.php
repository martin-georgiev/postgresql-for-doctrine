<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitVaryingForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitVaryingForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\BitValidationTrait;

/**
 * Implementation of PostgreSQL BIT VARYING data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-bit.html
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class BitVarying extends BaseType
{
    use BitValidationTrait;

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::BIT_VARYING;

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidBitVaryingForPHPException::forInvalidType($value);
        }

        if (!$this->isValidBitString($value)) {
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

        if (!$this->isValidBitString($value)) {
            throw InvalidBitVaryingForDatabaseException::forInvalidFormat($value);
        }

        return $value;
    }
}
