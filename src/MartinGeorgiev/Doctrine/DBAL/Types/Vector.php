<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidVectorForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidVectorForPHPException;

/**
 * Implementation of the pgvector vector data type.
 *
 * Stores a fixed-dimension floating-point vector.
 * Requires the pgvector extension.
 *
 * @see https://github.com/pgvector/pgvector
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Vector extends BaseType
{
    protected const TYPE_NAME = Type::VECTOR;

    /**
     * @param list<float>|null $value
     */
    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_array($value)) {
            throw InvalidVectorForDatabaseException::forInvalidType($value);
        }

        foreach ($value as $item) {
            if (!\is_float($item) && !\is_int($item)) {
                throw InvalidVectorForDatabaseException::forInvalidItemType($item);
            }
        }

        return '[' . \implode(',', $value) . ']';
    }

    /**
     * @return list<float>|null
     */
    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?array
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidVectorForPHPException::forInvalidType($value);
        }

        $trimmed = \trim($value, '[]');
        if ($trimmed === '') {
            return [];
        }

        $parts = \explode(',', $trimmed);
        $result = [];
        foreach ($parts as $part) {
            if (!\is_numeric($part)) {
                throw InvalidVectorForPHPException::forInvalidFormat($value);
            }
            $result[] = (float) $part;
        }

        return $result;
    }
}
