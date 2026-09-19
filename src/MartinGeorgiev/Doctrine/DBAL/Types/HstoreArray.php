<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHstoreArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHstoreArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\HstoreParserTrait;
use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL hstore[] data type.
 *
 * Maps PostgreSQL hstore[] to PHP array<int, array<string, string|null>|null>.
 *
 * @see https://www.postgresql.org/docs/18/hstore.html
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

    protected function createInvalidHstoreValueTypeException(mixed $value): InvalidHstoreArrayItemForDatabaseException
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

    /**
     * Coercion is load-bearing here: it turns an element PostgreSQL would never emit for `hstore[]`, such as the
     * `42` in `{42}`, into a value the item hook rejects. Preserving string types would hand the hstore parser
     * `'42'`, which it reads as a map of nothing.
     */
    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        try {
            return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
        } catch (InvalidArrayFormatException) {
            throw InvalidHstoreArrayItemForPHPException::forInvalidFormat($postgresArray);
        }
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
