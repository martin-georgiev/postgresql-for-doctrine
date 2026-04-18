<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\LengthAwareSQLDeclarationTrait;

/**
 * Shared implementation for pgvector dense float vector types (VECTOR, HALFVEC).
 *
 * Use the `length` column option to specify the number of dimensions (e.g. `length: 1024`).
 * Omitting `length` produces a dimensionless column, which is valid DDL but cannot be indexed.
 *
 * @see https://github.com/pgvector/pgvector
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseVector extends BaseType
{
    use LengthAwareSQLDeclarationTrait;

    abstract protected function throwInvalidTypeForDatabase(mixed $value): never;

    abstract protected function throwInvalidItemTypeForDatabase(mixed $value): never;

    abstract protected function throwInvalidTypeForPHP(mixed $value): never;

    abstract protected function throwInvalidFormatForPHP(mixed $value): never;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_array($value)) {
            $this->throwInvalidTypeForDatabase($value);
        }

        if (!\array_is_list($value)) {
            $this->throwInvalidTypeForDatabase($value);
        }

        $stringItems = [];
        foreach ($value as $item) {
            if (!\is_float($item) && !\is_int($item)) {
                $this->throwInvalidItemTypeForDatabase($item);
            }

            if (\is_float($item) && !\is_finite($item)) {
                $this->throwInvalidItemTypeForDatabase($item);
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
            $this->throwInvalidTypeForPHP($value);
        }

        if (\strlen($value) < 2 || $value[0] !== '[' || $value[\strlen($value) - 1] !== ']') {
            $this->throwInvalidFormatForPHP($value);
        }

        $trimmed = \substr($value, 1, -1);
        if ($trimmed === '') {
            return [];
        }

        $parts = \explode(',', $trimmed);
        $result = [];
        foreach ($parts as $part) {
            if (!\is_numeric($part)) {
                $this->throwInvalidFormatForPHP($value);
            }

            $result[] = (float) $part;
        }

        return $result;
    }
}
