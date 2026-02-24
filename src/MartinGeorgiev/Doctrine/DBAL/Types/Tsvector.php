<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsvectorForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsvectorForPHPException;

/**
 * Implementation of PostgreSQL TSVECTOR data type.
 *
 * Stores a pre-processed document for full-text search. PostgreSQL normalises
 * the value on write (lowercasing, stemming, stop-word removal), so the value
 * retrieved from the database may differ from the value that was stored.
 *
 * @see https://www.postgresql.org/docs/18/datatype-textsearch.html
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Tsvector extends BaseType
{
    protected const TYPE_NAME = Type::TSVECTOR;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidTsvectorForPHPException::forInvalidType($value);
        }

        if ($value === '') {
            throw InvalidTsvectorForPHPException::forInvalidFormat($value);
        }

        return $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidTsvectorForDatabaseException::forInvalidType($value);
        }

        return $value;
    }
}
