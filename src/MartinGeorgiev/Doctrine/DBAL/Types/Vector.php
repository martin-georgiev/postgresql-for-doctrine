<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
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
class Vector extends BaseType
{
    protected const TYPE_NAME = Type::VECTOR;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_array($value)) {
            throw InvalidVectorForDatabaseException::forInvalidType($value);
        }

        if (!\array_is_list($value)) {
            throw InvalidVectorForDatabaseException::forInvalidType($value);
        }

        $stringItems = [];
        foreach ($value as $item) {
            if (!\is_float($item) && !\is_int($item)) {
                throw InvalidVectorForDatabaseException::forInvalidItemType($item);
            }

            $stringItems[] = (string) $item;
        }

        return '['.\implode(',', $stringItems).']';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?array
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidVectorForPHPException::forInvalidType($value);
        }

        if (\strlen($value) < 2 || $value[0] !== '[' || $value[\strlen($value) - 1] !== ']') {
            throw InvalidVectorForPHPException::forInvalidFormat($value);
        }

        $trimmed = \substr($value, 1, -1);
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
