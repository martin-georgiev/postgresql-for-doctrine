<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddr8ForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddr8ForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\Macaddr8ValidationTrait;

/**
 * Implementation of PostgreSQL MACADDR8 data type (EUI-64 format).
 *
 * @see https://www.postgresql.org/docs/18/datatype-net-types.html#DATATYPE-MACADDR8
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Macaddr8 extends BaseType
{
    use Macaddr8ValidationTrait;

    protected const TYPE_NAME = Type::MACADDR8;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidMacaddr8ForPHPException::forInvalidType($value);
        }

        if (!$this->isValidMacaddr8Address($value)) {
            throw InvalidMacaddr8ForPHPException::forInvalidFormat($value);
        }

        return $this->normalizeMacaddr8Format($value);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidMacaddr8ForDatabaseException::forInvalidType($value);
        }

        if (!$this->isValidMacaddr8Address($value)) {
            throw InvalidMacaddr8ForDatabaseException::forInvalidFormat($value);
        }

        return $value;
    }
}
