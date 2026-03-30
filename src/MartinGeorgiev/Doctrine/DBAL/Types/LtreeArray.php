<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtreeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtreeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidLtreeException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree as LtreeValueObject;

/**
 * Implementation of PostgreSQL LTREE[] data type.
 *
 * @see https://www.postgresql.org/docs/13/ltree.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class LtreeArray extends BaseArray
{
    protected const TYPE_NAME = Type::LTREE_ARRAY;

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        if (!$item instanceof LtreeValueObject) {
            throw InvalidLtreeArrayItemForDatabaseException::forInvalidType($item);
        }

        return (string) $item;
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item === null || $item instanceof LtreeValueObject;
    }

    public function transformArrayItemForPHP(mixed $item): ?LtreeValueObject
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidLtreeArrayItemForPHPException::forInvalidType($item);
        }

        try {
            return LtreeValueObject::fromString($item);
        } catch (InvalidLtreeException) {
            throw InvalidLtreeArrayItemForPHPException::forInvalidFormat($item);
        }
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        $trimmed = \trim($postgresArray, '{}');
        if ($trimmed === '') {
            return [];
        }

        return \array_map(
            static fn (string $item): ?string => ($item === 'NULL') ? null : $item,
            \explode(',', $trimmed)
        );
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidLtreeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidLtreeArrayItemForDatabaseException::forInvalidType($item);
    }
}
