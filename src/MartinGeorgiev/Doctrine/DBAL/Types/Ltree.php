<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtreeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtreeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidLtreeException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree as LtreeValueObject;

/**
 * Implementation of PostgreSQL LTREE data type.
 *
 * @see https://www.postgresql.org/docs/13/ltree.html
 * @since 3.5
 *
 * @author Pierre-Yves Landuré
 */
final class Ltree extends BaseType
{
    protected const TYPE_NAME = Type::LTREE;

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?LtreeValueObject
    {
        if (null === $value) {
            return null;
        }

        if (\is_string($value)) {
            try {
                return LtreeValueObject::fromString($value);
            } catch (InvalidLtreeException) {
                throw InvalidLtreeForDatabaseException::forInvalidFormat($value);
            }
        }

        throw InvalidLtreeForDatabaseException::forInvalidType($value);
    }

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if (\is_string($value)) {
            try {
                $value = LtreeValueObject::fromString($value);
            } catch (InvalidLtreeException) {
                throw InvalidLtreeForPHPException::forInvalidFormat($value);
            }
        }

        if ($value instanceof LtreeValueObject) {
            return (string) $value;
        }

        throw InvalidLtreeForPHPException::forInvalidType($value);
    }
}
