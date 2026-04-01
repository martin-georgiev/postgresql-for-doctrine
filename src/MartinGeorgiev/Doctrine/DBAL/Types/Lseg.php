<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLsegForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLsegForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidLsegException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Lseg as LsegValueObject;

/**
 * Implementation of PostgreSQL LSEG data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-LSEG
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Lseg extends BaseType
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::LSEG;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof LsegValueObject) {
            throw InvalidLsegForPHPException::forInvalidType($value);
        }

        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?LsegValueObject
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidLsegForDatabaseException::forInvalidType($value);
        }

        try {
            return LsegValueObject::fromString($value);
        } catch (InvalidLsegException) {
            throw InvalidLsegForDatabaseException::forInvalidFormat($value);
        }
    }
}
