<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHalfvecForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHalfvecForPHPException;

/**
 * Implementation of the pgvector HALFVEC data type.
 *
 * Stores a fixed-dimension half-precision floating-point vector.
 *
 * @see https://github.com/pgvector/pgvector
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Halfvec extends BaseVector
{
    protected const TYPE_NAME = Type::HALFVEC;

    protected function throwInvalidTypeForDatabase(mixed $value): never
    {
        throw InvalidHalfvecForDatabaseException::forInvalidType($value);
    }

    protected function throwInvalidItemTypeForDatabase(mixed $value): never
    {
        throw InvalidHalfvecForDatabaseException::forInvalidItemType($value);
    }

    protected function throwInvalidTypeForPHP(mixed $value): never
    {
        throw InvalidHalfvecForPHPException::forInvalidType($value);
    }

    protected function throwInvalidFormatForPHP(mixed $value): never
    {
        throw InvalidHalfvecForPHPException::forInvalidFormat($value);
    }
}
