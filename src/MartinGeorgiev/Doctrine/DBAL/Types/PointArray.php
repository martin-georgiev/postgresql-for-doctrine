<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointArrayItemForPHPException;
use MartinGeorgiev\ValueObject\Point as PointValueObject;

/**
 * Implementation of PostgreSQL POINT[] data type.
 *
 * @see https://www.postgresql.org/docs/current/datatype-geometric.html#DATATYPE-GEOMETRIC-POINTS
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
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

        if (!\preg_match('/^\((-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)\)$/', $item, $matches)) {
            $this->throwInvalidFormatException($item);
        }

        return new PointValueObject((float) $matches[1], (float) $matches[2]);
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
