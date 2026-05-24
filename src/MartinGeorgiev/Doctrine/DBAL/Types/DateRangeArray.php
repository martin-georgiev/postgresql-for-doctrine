<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateRangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateRangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange as DateRangeValueObject;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL DATERANGE[] data type.
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class DateRangeArray extends BaseArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::DATERANGE_ARRAY;

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        if (!$item instanceof DateRangeValueObject) {
            throw InvalidDateRangeArrayItemForDatabaseException::forInvalidType($item);
        }

        return $this->quoteAndEscapeArrayItem((string) $item);
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item === null || $item instanceof DateRangeValueObject;
    }

    public function transformArrayItemForPHP(mixed $item): ?DateRangeValueObject
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidDateRangeArrayItemForPHPException::forInvalidType($item);
        }

        try {
            return DateRangeValueObject::fromString($item);
        } catch (\InvalidArgumentException) {
            throw InvalidDateRangeArrayItemForPHPException::forInvalidFormat($item);
        }
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidDateRangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidDateRangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
