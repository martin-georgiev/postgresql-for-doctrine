<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsvectorArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsvectorArrayItemForPHPException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL TSVECTOR[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-textsearch.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TsvectorArray extends BaseArray
{
    protected const TYPE_NAME = Type::TSVECTOR_ARRAY;

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        \assert(\is_string($item));
        $escaped = \str_replace(['\\', '"'], ['\\\\', '\\"'], $item);

        return '"'.$escaped.'"';
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        if ($item === null) {
            return true;
        }

        if (!\is_string($item)) {
            return false;
        }

        return $item !== '';
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    public function transformArrayItemForPHP(mixed $item): ?string
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidTsvectorArrayItemForPHPException::forInvalidType($item);
        }

        return $item;
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidTsvectorArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidTsvectorArrayItemForDatabaseException::forInvalidFormat($item);
    }
}
