<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

/**
 * Abstract handling of PostgreSQL array data types.
 *
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseArray extends BaseType
{
    /**
     * Converts a value from its PHP representation to its PostgreSQL representation of the type.
     *
     * @param array|null $phpArray the value to convert
     *
     * @throws ConversionException When passed argument is not PHP array OR When invalid array items are detected
     */
    public function convertToDatabaseValue($phpArray, AbstractPlatform $platform): ?string
    {
        if ($phpArray === null) {
            return null;
        }

        if (!\is_array($phpArray)) {
            $this->throwInvalidTypeException($phpArray);
        }

        foreach ($phpArray as &$item) {
            if (!$this->isValidArrayItemForDatabase($item)) {
                $this->throwInvalidItemException();
            }

            $item = $this->transformArrayItemForPostgres($item);
        }

        return '{'.\implode(',', $phpArray).'}';
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function throwInvalidTypeException(mixed $value): never
    {
        throw $this->createInvalidTypeException($value);
    }

    /**
     * @throws ConversionException
     */
    protected function throwInvalidItemException(): never
    {
        throw $this->createInvalidItemException();
    }

    /**
     * @throws ConversionException
     */
    protected function throwInvalidPostgresTypeException(mixed $value): never
    {
        throw new ConversionException(
            \sprintf('Given PostgreSQL value content type is not PHP string. Instead it is "%s".', \gettype($value))
        );
    }

    protected function createInvalidTypeException(mixed $value): \InvalidArgumentException
    {
        return new \InvalidArgumentException(
            \sprintf('Given PHP value content type is not PHP array. Instead it is "%s".', \gettype($value))
        );
    }

    protected function createInvalidItemException(): ConversionException
    {
        return new ConversionException("One or more of the items given doesn't look valid.");
    }

    /**
     * Tests if given PHP array item is from compatible type for PostgreSQL.
     */
    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return true;
    }

    /**
     * Transforms PHP array item to a PostgreSQL compatible array item.
     *
     * @return mixed
     */
    protected function transformArrayItemForPostgres(mixed $item)
    {
        return $item;
    }

    /**
     * Converts a value from its PostgreSQL representation to its PHP representation of this type.
     *
     * @param string|null $postgresArray the value to convert
     */
    public function convertToPHPValue($postgresArray, AbstractPlatform $platform): ?array
    {
        if ($postgresArray === null) {
            return null;
        }

        if (!\is_string($postgresArray)) {
            $this->throwInvalidPostgresTypeException($postgresArray);
        }

        $phpArray = $this->transformPostgresArrayToPHPArray($postgresArray);
        foreach ($phpArray as &$item) {
            $item = $this->transformArrayItemForPHP($item);
        }

        return $phpArray;
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        $trimmedPostgresArray = \mb_substr($postgresArray, 1, -1);
        if ($trimmedPostgresArray === '') {
            return [];
        }

        return \explode(',', $trimmedPostgresArray);
    }

    /**
     * Transforms PostgreSQL array item to a PHP compatible array item.
     *
     * @return mixed
     */
    public function transformArrayItemForPHP(mixed $item)
    {
        return $item;
    }
}
