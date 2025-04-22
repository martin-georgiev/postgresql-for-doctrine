<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point as PointValueObject;

/**
 * Implementation of PostgreSQL POINT[] data type.
 *
 * @see https://www.postgresql.org/docs/17/datatype-geometric.html#DATATYPE-GEOMETRIC-POINTS
 * @since 3.1
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 */
class PointArray extends BaseArray
{
    protected const TYPE_NAME = 'point[]';

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if (!$item instanceof PointValueObject) {
            throw InvalidPointArrayItemForDatabaseException::isNotAPoint($item);
        }

        return '"'.$item.'"';
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        if (!\str_starts_with($postgresArray, '{"') || !\str_ends_with($postgresArray, '"}')) {
            return [];
        }

        $trimmedPostgresArray = \mb_substr($postgresArray, 2, -2);
        if ($trimmedPostgresArray === '') {
            return [];
        }

        return \explode('","', $trimmedPostgresArray);
    }

    public function transformArrayItemForPHP(mixed $item): ?PointValueObject
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            $this->throwInvalidTypeException($item);
        }

        try {
            return PointValueObject::fromString($item);
        } catch (\InvalidArgumentException) {
            $this->throwInvalidFormatException($item);
        }
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item instanceof PointValueObject;
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidPointArrayItemForPHPException::forInvalidType($value);
    }

    protected function throwInvalidFormatException(mixed $value): never
    {
        throw InvalidPointArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwInvalidItemException(): never
    {
        throw InvalidPointArrayItemForPHPException::forInvalidFormat('Array contains invalid point items');
    }
}
