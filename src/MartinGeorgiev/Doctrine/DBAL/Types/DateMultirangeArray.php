<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateMultirangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateMultirangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateMultirange as DateMultirangeValueObject;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL DATEMULTIRANGE[] data type.
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class DateMultirangeArray extends BaseArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::DATEMULTIRANGE_ARRAY;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item === null || $item instanceof DateMultirangeValueObject;
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        if (!$item instanceof DateMultirangeValueObject) {
            throw InvalidDateMultirangeArrayItemForDatabaseException::forInvalidType($item);
        }

        return $this->quoteAndEscapeArrayItem((string) $item);
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    public function transformArrayItemForPHP(mixed $item): ?DateMultirangeValueObject
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidDateMultirangeArrayItemForPHPException::forInvalidType($item);
        }

        try {
            return DateMultirangeValueObject::fromString($item);
        } catch (\InvalidArgumentException) {
            throw InvalidDateMultirangeArrayItemForPHPException::forInvalidFormat($item);
        }
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidDateMultirangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidDateMultirangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
