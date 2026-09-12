<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidJsonArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidJsonbArrayItemForDatabaseException;
use MartinGeorgiev\Utils\Exception\InvalidJsonFormatException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;
use MartinGeorgiev\Utils\PostgresJsonToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL JSONB[] data type.
 *
 * @see https://www.postgresql.org/docs/17/arrays.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonbArray extends BaseArray
{
    use JsonTransformer;

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::JSONB_ARRAY;

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidJsonbArrayItemForDatabaseException::forInvalidArrayType($value);
    }

    protected function throwInvalidJsonValueException(mixed $phpValue): never
    {
        throw InvalidJsonbArrayItemForDatabaseException::forUnencodableValue($phpValue);
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        return $this->quoteAndEscapeArrayItem($this->transformToPostgresJson($item));
    }

    /**
     * @return array<int, mixed>
     */
    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    public function transformArrayItemForPHP(mixed $item): array|bool|float|int|string|null
    {
        if ($item === null) {
            return null;
        }

        // PostgreSQL leaves a JSON number or boolean unquoted inside the array literal, and the array parser
        // already turns those into the very PHP value a JSON decode would produce. Only quoted items still carry JSON text.
        if (\is_int($item) || \is_float($item) || \is_bool($item)) {
            return $item;
        }

        if (!\is_string($item)) {
            throw InvalidJsonArrayItemForPHPException::forInvalidType($item);
        }

        try {
            return PostgresJsonToPHPArrayTransformer::transformPostgresJsonEncodedValueToPHPValue($item);
        } catch (InvalidJsonFormatException) {
            throw InvalidJsonArrayItemForPHPException::forInvalidFormat($item);
        }
    }
}
