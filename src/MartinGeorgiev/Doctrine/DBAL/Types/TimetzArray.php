<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimetzArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimetzArrayItemForPHPException;

/**
 * Implementation of PostgreSQL timetz[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TimetzArray extends BaseStringArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::TIMETZ_ARRAY;

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

    protected function createInvalidTypeExceptionForPHP(mixed $item): InvalidTimetzArrayItemForPHPException
    {
        return InvalidTimetzArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidTimetzArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidTimetzArrayItemForDatabaseException::forInvalidFormat($item);
    }
}
