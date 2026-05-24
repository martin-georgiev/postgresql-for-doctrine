<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHstoreForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHstoreForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\HstoreParserTrait;

/**
 * Implementation of PostgreSQL hstore extension type.
 *
 * Maps PostgreSQL hstore (key-value store) to PHP array<string, string|null>.
 *
 * @see https://www.postgresql.org/docs/18/hstore.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Hstore extends BaseType
{
    use HstoreParserTrait;

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::HSTORE;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_array($value)) {
            throw InvalidHstoreForDatabaseException::forInvalidType($value);
        }

        if ($value === []) {
            return '';
        }

        return $this->buildHstoreString($value);
    }

    protected function createInvalidHstoreValueTypeException(mixed $value): InvalidHstoreForDatabaseException
    {
        return InvalidHstoreForDatabaseException::forInvalidType($value);
    }

    /**
     * @param mixed $value
     *
     * @return array<string, string|null>|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidHstoreForPHPException::forInvalidType($value);
        }

        return $this->parseHstoreString($value);
    }
}
