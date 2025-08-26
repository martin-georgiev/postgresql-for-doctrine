<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtreeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtreeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree as LtreeValueObject;

final class Ltree extends BaseType
{
    protected const TYPE_NAME = 'ltree';

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?LtreeValueObject
    {
        if (null === $value) {
            return null;
        }

        if (\is_string($value)) {
            try {
                return LtreeValueObject::fromString($value);
            } catch (\InvalidArgumentException) {
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
            } catch (\InvalidArgumentException) {
                throw InvalidLtreeForPHPException::forInvalidFormat($value);
            }
        }

        if ($value instanceof LtreeValueObject) {
            return (string) $value;
        }

        throw InvalidLtreeForPHPException::forInvalidType($value);
    }
}
