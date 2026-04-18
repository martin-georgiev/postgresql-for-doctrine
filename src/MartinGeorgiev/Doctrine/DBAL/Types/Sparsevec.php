<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidSparsevecForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidSparsevecForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\LengthAwareSQLDeclarationTrait;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Sparsevec as SparsevecValueObject;

/**
 * Implementation of the pgvector SPARSEVEC data type.
 *
 * Stores a sparse vector using the format `{index:value,...}/dimensions`.
 * Use the `length` column option to specify the number of dimensions (e.g. `length: 1024`).
 * Omitting `length` produces a dimensionless column, which is valid DDL but cannot be indexed.
 *
 * @see https://github.com/pgvector/pgvector
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Sparsevec extends BaseType
{
    use LengthAwareSQLDeclarationTrait;

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::SPARSEVEC;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof SparsevecValueObject) {
            throw InvalidSparsevecForDatabaseException::forInvalidType($value);
        }

        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?SparsevecValueObject
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidSparsevecForPHPException::forInvalidType($value);
        }

        return SparsevecValueObject::fromString($value);
    }
}
