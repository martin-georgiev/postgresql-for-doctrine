<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\BitValidationTrait;

/**
 * Implementation of PostgreSQL BIT data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-bit.html
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Bit extends BaseType
{
    use BitValidationTrait;

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::BIT;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $length = $fieldDeclaration['length'] ?? null;

        if (\is_int($length)) {
            return \sprintf('%s(%d)', \strtoupper(self::TYPE_NAME), $length);
        }

        return \strtoupper(self::TYPE_NAME);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidBitForPHPException::forInvalidType($value);
        }

        if (!$this->isValidBitString($value)) {
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

        if (!$this->isValidBitString($value)) {
            throw InvalidBitForDatabaseException::forInvalidFormat($value);
        }

        return $value;
    }
}
