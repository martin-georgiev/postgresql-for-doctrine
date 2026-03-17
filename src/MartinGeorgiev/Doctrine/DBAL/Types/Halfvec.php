<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
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
class Halfvec extends BaseType
{
    protected const TYPE_NAME = Type::HALFVEC;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_array($value)) {
            throw InvalidHalfvecForDatabaseException::forInvalidType($value);
        }

        if (!\array_is_list($value)) {
            throw InvalidHalfvecForDatabaseException::forInvalidType($value);
        }

        $stringItems = [];
        foreach ($value as $item) {
            if (!\is_float($item) && !\is_int($item)) {
                throw InvalidHalfvecForDatabaseException::forInvalidItemType($item);
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
            throw InvalidHalfvecForPHPException::forInvalidType($value);
        }

        if (\strlen($value) < 2 || $value[0] !== '[' || $value[\strlen($value) - 1] !== ']') {
            throw InvalidHalfvecForPHPException::forInvalidFormat($value);
        }

        $trimmed = \substr($value, 1, -1);
        if ($trimmed === '') {
            return [];
        }

        $parts = \explode(',', $trimmed);
        $result = [];
        foreach ($parts as $part) {
            if (!\is_numeric($part)) {
                throw InvalidHalfvecForPHPException::forInvalidFormat($value);
            }

            $result[] = (float) $part;
        }

        return $result;
    }
}
