<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidVectorForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidVectorForPHPException;

/**
 * Implementation of the pgvector VECTOR data type.
 *
 * Stores a fixed-dimension floating-point vector.
 *
 * @see https://github.com/pgvector/pgvector
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Vector extends BaseVector
{
    protected const TYPE_NAME = Type::VECTOR;

    protected function throwInvalidTypeForDatabase(mixed $value): never
    {
        throw InvalidVectorForDatabaseException::forInvalidType($value);
    }

    protected function throwInvalidItemTypeForDatabase(mixed $value): never
    {
        throw InvalidVectorForDatabaseException::forInvalidItemType($value);
    }

    protected function throwInvalidTypeForPHP(mixed $value): never
    {
        throw InvalidVectorForPHPException::forInvalidType($value);
    }

    protected function throwInvalidFormatForPHP(mixed $value): never
    {
        throw InvalidVectorForPHPException::forInvalidFormat($value);
    }
}
