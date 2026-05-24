<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTstzMultirangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTstzMultirangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzMultirange as TstzMultirangeValueObject;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL TSTZMULTIRANGE[] data type.
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TstzMultirangeArray extends BaseArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::TSTZMULTIRANGE_ARRAY;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item === null || $item instanceof TstzMultirangeValueObject;
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        if (!$item instanceof TstzMultirangeValueObject) {
            throw InvalidTstzMultirangeArrayItemForDatabaseException::forInvalidType($item);
        }

        return $this->quoteAndEscapeArrayItem((string) $item);
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    public function transformArrayItemForPHP(mixed $item): ?TstzMultirangeValueObject
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidTstzMultirangeArrayItemForPHPException::forInvalidType($item);
        }

        try {
            return TstzMultirangeValueObject::fromString($item);
        } catch (\InvalidArgumentException) {
            throw InvalidTstzMultirangeArrayItemForPHPException::forInvalidFormat($item);
        }
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidTstzMultirangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidTstzMultirangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
