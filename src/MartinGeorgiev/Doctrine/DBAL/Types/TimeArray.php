<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimeArrayItemForPHPException;

/**
 * Implementation of PostgreSQL TIME[] data type.
 *
 * Array items are handled as strings (e.g. "10:30:00").
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TimeArray extends BaseStringArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::TIME_ARRAY;

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

    protected function createInvalidTypeExceptionForPHP(mixed $item): InvalidTimeArrayItemForPHPException
    {
        return InvalidTimeArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidTimeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidTimeArrayItemForDatabaseException::forInvalidFormat($item);
    }
}
