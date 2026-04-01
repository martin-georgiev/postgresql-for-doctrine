<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsvectorArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsvectorArrayItemForPHPException;

/**
 * Implementation of PostgreSQL TSVECTOR[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-textsearch.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TsvectorArray extends BaseStringArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::TSVECTOR_ARRAY;

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

    protected function createInvalidTypeExceptionForPHP(mixed $item): InvalidTsvectorArrayItemForPHPException
    {
        return InvalidTsvectorArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidTsvectorArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidTsvectorArrayItemForDatabaseException::forInvalidFormat($item);
    }
}
