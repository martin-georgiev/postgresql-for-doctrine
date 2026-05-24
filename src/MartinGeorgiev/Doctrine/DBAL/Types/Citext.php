<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCitextForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCitextForPHPException;

/**
 * Implementation of PostgreSQL citext extension type.
 *
 * Case-insensitive text type — comparisons are case-insensitive in PostgreSQL
 * while preserving the original casing. Requires the citext extension.
 *
 * @see https://www.postgresql.org/docs/18/citext.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Citext extends BaseType
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::CITEXT;

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidCitextForDatabaseException::forInvalidType($value);
        }

        return $value;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidCitextForPHPException::forInvalidType($value);
        }

        return $value;
    }
}
