<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumRangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumRangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange as NumericRangeValueObject;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL NUMRANGE[] data type.
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class NumRangeArray extends BaseArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::NUMRANGE_ARRAY;

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        if (!$item instanceof NumericRangeValueObject) {
            throw InvalidNumRangeArrayItemForDatabaseException::forInvalidType($item);
        }

        return $this->quoteAndEscapeArrayItem((string) $item);
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item === null || $item instanceof NumericRangeValueObject;
    }

    public function transformArrayItemForPHP(mixed $item): ?NumericRangeValueObject
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidNumRangeArrayItemForPHPException::forInvalidType($item);
        }

        try {
            return NumericRangeValueObject::fromString($item);
        } catch (\InvalidArgumentException) {
            throw InvalidNumRangeArrayItemForPHPException::forInvalidFormat($item);
        }
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidNumRangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidNumRangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
