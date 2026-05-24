<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumMultirangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumMultirangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericMultirange as NumericMultirangeValueObject;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL NUMMULTIRANGE[] data type.
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class NumMultirangeArray extends BaseArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::NUMMULTIRANGE_ARRAY;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item === null || $item instanceof NumericMultirangeValueObject;
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        if (!$item instanceof NumericMultirangeValueObject) {
            throw InvalidNumMultirangeArrayItemForDatabaseException::forInvalidType($item);
        }

        return $this->quoteAndEscapeArrayItem((string) $item);
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    public function transformArrayItemForPHP(mixed $item): ?NumericMultirangeValueObject
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidNumMultirangeArrayItemForPHPException::forInvalidType($item);
        }

        try {
            return NumericMultirangeValueObject::fromString($item);
        } catch (\InvalidArgumentException) {
            throw InvalidNumMultirangeArrayItemForPHPException::forInvalidFormat($item);
        }
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidNumMultirangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidNumMultirangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
