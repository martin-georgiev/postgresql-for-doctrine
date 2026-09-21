<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;
use MartinGeorgiev\Utils\PostgresBackslashEscaper;

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
     * @param array|null $phpArray
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($phpArray, AbstractPlatform $platform): ?string
    {
        if ($phpArray === null) {
            return null;
        }

        if (!\is_array($phpArray)) {
            $this->throwInvalidTypeException($phpArray);
        }

        $transformedItems = [];
        foreach ($phpArray as $item) {
            if (!$this->isValidArrayItemForDatabase($item)) {
                $this->throwInvalidItemException($item);
            }

            $transformed = $this->transformArrayItemForPostgres($item);
            \assert(\is_scalar($transformed) || $transformed instanceof \Stringable);
            $transformedItems[] = (string) $transformed;
        }

        return '{'.\implode($this->getArrayElementDelimiter(), $transformedItems).'}';
    }

    /**
     * The character PostgreSQL splits array elements on, as `pg_type.typdelim` records it for the element type.
     * It is `,` for nearly everything, but not for every type this library covers.
     */
    protected function getArrayElementDelimiter(): string
    {
        return ',';
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
    protected function throwInvalidItemException(mixed $item): never
    {
        throw $this->createInvalidItemException();
    }

    /**
     * @throws ConversionException
     */
    protected function throwInvalidPostgresTypeException(mixed $value): never
    {
        throw new ConversionException(
            \sprintf('Given PostgreSQL value must be a PHP string, %s given.', \var_export($value, true))
        );
    }

    protected function createInvalidTypeException(mixed $value): \InvalidArgumentException
    {
        return new \InvalidArgumentException(
            \sprintf('Given PHP value must be a PHP array, %s given.', \var_export($value, true))
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
     * Transforms PHP array item to a PostgreSQL-compatible array item.
     *
     * @return mixed
     */
    protected function transformArrayItemForPostgres(mixed $item)
    {
        return $item;
    }

    protected function quoteAndEscapeArrayItem(string $item): string
    {
        return '"'.PostgresBackslashEscaper::escape($item).'"';
    }

    /**
     * @param string|null $postgresArray
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

    /**
     * @return array<int, mixed>
     *
     * @throws ConversionException
     */
    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        $trimmed = \trim($postgresArray);
        $isAWellFormedBracedArrayLiteral = \str_starts_with($trimmed, '{') && \str_ends_with($trimmed, '}');
        if (!$isAWellFormedBracedArrayLiteral) {
            $this->throwInvalidArrayFormatException($postgresArray);
        }

        try {
            return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray(
                $postgresArray,
                preserveStringTypes: true,
                delimiter: $this->getArrayElementDelimiter()
            );
        } catch (InvalidArrayFormatException) {
            $this->throwInvalidArrayFormatException($postgresArray);
        }
    }

    /**
     * Thrown for a literal that is malformed as an array. This exception carries no offending item.
     *
     * @throws ConversionException
     */
    protected function throwInvalidArrayFormatException(string $postgresArray): never
    {
        throw new ConversionException(
            \sprintf('Given PostgreSQL array value is not in a valid format. Instead it is "%s".', $postgresArray)
        );
    }

    /**
     * Transforms PostgreSQL array item to a PHP-compatible array item.
     *
     * @return mixed
     */
    public function transformArrayItemForPHP(mixed $item)
    {
        return $item;
    }
}
