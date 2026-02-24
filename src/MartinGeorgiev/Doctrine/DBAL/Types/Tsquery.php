<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsqueryForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsqueryForPHPException;

/**
 * Implementation of PostgreSQL TSQUERY data type.
 *
 * Stores a full-text search query. A tsquery value stores lexemes that are to
 * be searched for, and may combine them using boolean operators & (AND), |
 * (OR), and ! (NOT), as well as the phrase search operator <-> (FOLLOWED BY).
 *
 * @see https://www.postgresql.org/docs/18/datatype-textsearch.html
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Tsquery extends BaseType
{
    protected const TYPE_NAME = Type::TSQUERY;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidTsqueryForPHPException::forInvalidType($value);
        }

        if ($value === '') {
            throw InvalidTsqueryForPHPException::forInvalidFormat($value);
        }

        return $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidTsqueryForDatabaseException::forInvalidType($value);
        }

        return $value;
    }
}
