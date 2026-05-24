<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHstoreArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHstoreArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\HstoreParserTrait;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL hstore[] data type.
 *
 * Maps PostgreSQL hstore[] to PHP array<int, array<string, string|null>|null>.
 *
 * @see https://www.postgresql.org/docs/current/hstore.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class HstoreArray extends BaseArray
{
    use HstoreParserTrait;

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::HSTORE_ARRAY;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item === null || \is_array($item);
    }

    protected function createInvalidHstoreValueTypeException(mixed $value): ConversionException
    {
        return InvalidHstoreArrayItemForDatabaseException::forInvalidType($value);
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        \assert(\is_array($item));

        if ($item === []) {
            return '""';
        }

        return $this->quoteAndEscapeArrayItem($this->buildHstoreString($item));
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    /**
     * @return array<string, string|null>|null
     */
    public function transformArrayItemForPHP(mixed $item): ?array
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidHstoreArrayItemForPHPException::forInvalidType($item);
        }

        return $this->parseHstoreString($item);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidHstoreArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidHstoreArrayItemForDatabaseException::forInvalidType($item);
    }
}
